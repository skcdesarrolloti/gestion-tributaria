<?php
declare(strict_types=1);

// Crea su propia base aleatoria y la elimina al terminar. Nunca usa la base real.
require dirname(__DIR__) . '/bootstrap/autoload.php';
use App\Services\Migrator;

function verify(bool $ok, string $message): void {
    if (!$ok) { throw new RuntimeException($message); }
    echo "OK: $message\n";
}
function request(CurlHandle $client, string $base, string $route, ?array $post = null): array {
    curl_setopt_array($client, [CURLOPT_URL => $base . '/index.php?route=' . $route,
        CURLOPT_POST => $post !== null, CURLOPT_HEADER => true, CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => false, CURLOPT_TIMEOUT => 15]);
    if ($post !== null) { curl_setopt($client, CURLOPT_POSTFIELDS, http_build_query($post)); }
    $response = curl_exec($client);
    if ($response === false) { throw new RuntimeException(curl_error($client)); }
    $size = curl_getinfo($client, CURLINFO_HEADER_SIZE);
    return [curl_getinfo($client, CURLINFO_HTTP_CODE), substr($response, $size), substr($response, 0, $size)];
}
function token(string $html): string {
    preg_match('/name="_csrf_token" value="([a-f0-9]+)"/', $html, $matches);
    return $matches[1] ?? '';
}
$root = dirname(__DIR__);
$local = $root . '/config/local.php';
if (is_file($local)) {
    fwrite(STDERR, "Retira temporalmente config/local.php para ejecutar en aislamiento.\n");
    exit(1);
}
$id = bin2hex(random_bytes(6));
$name = 'mvc_test_' . $id;
$temp = sys_get_temp_dir() . '/mvc-' . $id;
mkdir($temp . '/rate-limits', 0700, true);
$dbConfig = ['host' => getenv('TEST_DB_HOST') ?: '127.0.0.1',
    'port' => (int) (getenv('TEST_DB_PORT') ?: 3306), 'name' => $name,
    'username' => getenv('TEST_DB_USER') ?: 'root', 'password' => getenv('TEST_DB_PASSWORD') ?: ''];
$server = new PDO(sprintf('mysql:host=%s;port=%d;charset=utf8mb4', $dbConfig['host'], $dbConfig['port']),
    $dbConfig['username'], $dbConfig['password'], [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
$authName = $name . '_auth';
$authConfig = array_replace($dbConfig, ['name' => $authName]);
$config = ['database' => $dbConfig, 'auth_database' => $authConfig,'activation_token_sha256'=>hash('sha256',str_repeat('b',64)),
    'auth_allow_legacy_passwords' => true, 'secure_cookies' => false,
    'session_name' => 'test_' . $id, 'storage_path' => $temp];
$socket = stream_socket_server('tcp://127.0.0.1:0');
$address = stream_socket_get_name($socket, false);
fclose($socket);
$process = null;
$client = curl_init();
curl_setopt($client, CURLOPT_COOKIEFILE, '');
try {
    file_put_contents($local, "<?php\nreturn " . var_export($config, true) . ";\n");
    $process = proc_open([PHP_BINARY, '-S', $address, '-t', $root . '/public'],
        [0 => ['pipe', 'r'], 1 => ['file', $temp . '/server.log', 'a'], 2 => ['file', $temp . '/server.log', 'a']], $pipes);
    verify(is_resource($process), 'Servidor temporal iniciado');
    $base = 'http://' . $address;
    for ($i = 0; $i < 50; $i++) {
        $ready = @fsockopen('127.0.0.1', (int) substr(strrchr($address, ':'), 1));
        if ($ready) { fclose($ready); break; }
        usleep(100000);
    }
    [$status, $html] = request($client, $base, 'login');
    verify($status === 303, 'Sin cuentas dirige a activación privada');
    [$status,$activationHtml]=request($client,$base,'activar');
    verify($status===200 && !str_contains($activationHtml,'"clave":'), 'La activación no publica su secreto');
    [$status, , $headers] = request($client, $base, 'instalacion');
    verify($status === 303 && str_contains($headers, 'route=activar'), 'Enlace antiguo no permite eludir activación privada');
    $missingRejected = false;
    try { (new App\Core\Database($authConfig, false))->connection(); }
    catch (PDOException $error) { $missingRejected = (int) $error->errorInfo[1] === 1049; }
    verify($missingRejected, 'Conexión de funcionarios no crea bases automáticamente');
    $server->exec('CREATE DATABASE ' . $authName . ' CHARACTER SET utf8mb4');
    $authDb = (new App\Core\Database($authConfig, false))->connection();
    // Solo fixtures sintéticos en la base aleatoria de esta prueba.
    $authDb->exec(file_get_contents($root . '/database/migrations/001_funcionarios.sql'));
    $authDb->exec('ALTER TABLE wp_jet_cct_funcionarios AUTO_INCREMENT = 100');
    $insertUser = $authDb->prepare('INSERT INTO wp_jet_cct_funcionarios (nombre, user_others_apss, pass_others_apss) VALUES (?, ?, ?)');
    $insertUser->execute(['<b>Funcionario central</b>', 'admin', 'Prueba-segura-2026']);
    $hash = password_hash('Otra-clave-segura', PASSWORD_DEFAULT);
    $insertUser->execute(['Cuenta con hash', 'hash-user', $hash]);
    $db = new PDO(sprintf('mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4', $dbConfig['host'], $dbConfig['port'], $name),
        $dbConfig['username'], $dbConfig['password'], [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    // Sustituye únicamente en la base aleatoria la autorización inicial por un Socio sintético.
    $db->exec('DELETE FROM accesos_tributarios WHERE funcionario_id=0');
    $grant=$db->prepare("INSERT INTO accesos_tributarios (funcionario_id,login_hash,perfil,revision) VALUES (100,?,'socio',0)");
    $grant->execute([hash('sha256','admin')]);
    require __DIR__.'/activacion-integration.php';
    $activation=['_csrf_token'=>token($activationHtml),'username'=>'admin','nombre'=>'<b>Socio independiente</b>','correo'=>'','password'=>'Prueba-segura-2026','confirmation'=>'Prueba-segura-2026','activation_key'=>''];
    verify(request($client,$base,'activar',$activation)[0]===422,'Un visitante no puede reclamar el Socio sin prueba privada');
    $activation['activation_key']=str_repeat('b',64);
    verify(request($client,$base,'activar',array_replace($activation,['username'=>'intruso']))[0]===422,'El código no permite inventar otro Socio inicial');
    verify(request($client,$base,'activar',array_replace($activation,['activation_key'=>$config['activation_token_sha256']]))[0]===422,'La huella pública del código no permite activar');
    verify(request($client,$base,'activar',array_replace($activation,['_csrf_token'=>'']))[0]===419,'Activación exige CSRF');
    verify(request($client,$base,'activar',$activation)[0]===303,'Activa cuenta propia del Socio y conserva identificador previo');
    verify(!is_file($temp.'/activacion-inicial.json'),'Retira el secreto tras activación');
    verify(request($client,$base,'activar',$activation)[0]===303 && (int)$db->query('SELECT COUNT(*) FROM usuarios_tributarios')->fetchColumn()===1,'Activación de un solo uso');
    $db->prepare('INSERT INTO usuarios_tributarios (id,usuario,nombre,clave_hash,cambiar_clave) VALUES (101,?,?,?,0)')->execute(['hash-user','Cuenta con hash',$hash]);
    $migrationCount = count(glob($root . '/database/migrations/[0-9]*.sql'));
    verify((int) $db->query('SELECT COUNT(*) FROM schema_migrations')->fetchColumn() === $migrationCount, 'Migraciones registradas una vez');
    $localUser = $db->prepare('INSERT INTO wp_jet_cct_funcionarios (nombre, user_others_apss, pass_others_apss) VALUES (?, ?, ?)');
    $localUser->execute(['Cuenta antigua local', 'local-only', password_hash('Prueba-local-2026', PASSWORD_DEFAULT)]);
    verify(request($client, $base, 'inicio')[0] === 303, 'Inicio exige autenticación');
    verify(request($client, $base, 'contribuyentes/rut&id=1')[0] === 303, 'Carga RUT exige autenticación');
    verify(request($client, $base, 'contribuyentes/rut/documento&documento=1')[0] === 303, 'Descarga RUT exige autenticación');
    verify(request($client, $base, 'beneficios')[0] === 303, 'Catálogo de beneficios exige sesión');
    verify(request($client, $base, 'beneficios/articulo&articulo=et-107')[0] === 303, 'Texto de artículos exige sesión');
    verify(request($client, $base, 'seguridad-social')[0] === 303, 'Revisión de seguridad social exige sesión');
    verify(request($client, $base, 'simulador')[0] === 303, 'Hoja de planeación exige sesión');
    verify(request($client, $base, 'simulador/calcular&id=1',[])[0] === 303, 'API de simulación exige sesión');
    verify(request($client, $base, 'proyeccion&id=1')[0] === 303, 'Proyección exige autenticación');
    verify(request($client, $base, 'exogena')[0] === 303, 'Exógena exige autenticación');
    verify(request($client, $base, 'exogena/1007')[0] === 303, '1007 exige autenticación');
    verify(request($client, $base, 'exogena/1001')[0] === 303, '1001 exige autenticación');
    foreach (App\Services\ExogenaSchemas::CODES as $code) {
        verify(request($client,$base,'exogena/'.$code)[0]===303, "$code exige sesión");
        verify(request($client,$base,'exogena/'.$code.'/registro&contribuyente=1',[])[0]===303, "$code protege escritura sin sesión");
    }
    verify(request($client, $base, 'exogena/5247/registro&contribuyente=1', [])[0] === 303, 'Captura 5247 exige autenticación');
    verify(request($client, $base, 'contribuyentes')[0] === 303, 'Contribuyentes exige autenticación');
    verify(request($client, $base, 'contribuyentes/formulario', [])[0] === 303, 'Registro exige autenticación');
    [$status, $html] = request($client, $base, 'login');
    $login = ['_csrf_token' => token($html), 'username' => 'admin', 'password' => 'Prueba-segura-2026'];
    verify(request($client, $base, 'login', array_replace($login, ['username' => 'local-only', 'password' => 'Prueba-local-2026']))[0] === 401, 'Ignora cuentas locales antiguas');
    verify(request($client, $base, 'login', array_replace($login, ['_csrf_token' => '']))[0] === 419, 'Login exige CSRF');
    verify(request($client, $base, 'login', array_replace($login, ['password' => 'errada']))[0] === 401, 'Rechaza contraseña incorrecta');
    [$status, , $headers] = request($client, $base, 'login', $login);
    verify($status === 303 && str_contains(strtolower($headers), 'set-cookie:'), 'Login válido regenera sesión');
    [$status, $html] = request($client, $base, 'perfil');
    verify($status === 200 && str_contains($html, '&lt;b&gt;Socio independiente&lt;/b&gt;'), 'Perfil usa y escapa los datos propios');
    verify(!str_contains($html, '$2y$'), 'Perfil no expone hash');
    require __DIR__ . '/beneficios-integration.php';
    require __DIR__ . '/contribuyentes-integration.php';
    require __DIR__ . '/caracterizacion-integration.php';
    require __DIR__ . '/rut-integration.php';
    require __DIR__ . '/extractos-integration.php';
    require __DIR__ . '/ingresos-integration.php';
    require __DIR__ . '/simulador-integration.php';
    require __DIR__ . '/exogena-integration.php';
    require __DIR__ . '/exogena-1007-integration.php';
    require __DIR__ . '/exogena-1001-integration.php';
    require __DIR__ . '/exogena-catalog-integration.php';
    require __DIR__ . '/accesos-integration.php';
    require __DIR__ . '/socio-total-integration.php';
    require __DIR__ . '/cuentas-integration.php';
    require __DIR__ . '/socio-recuperacion-integration.php';
    require __DIR__ . '/registro-integration.php';
    [, $html]=request($client,$base,'perfil');
    verify(request($client, $base, 'logout')[0] === 405, 'Logout rechaza GET');
    verify(request($client, $base, 'logout', ['_csrf_token' => ''])[0] === 419, 'Logout exige CSRF');
    verify(request($client, $base, 'logout', ['_csrf_token' => token($html)])[0] === 303, 'Logout termina sesión');
    verify(request($client, $base, 'perfil')[0] === 303, 'Sesión cerrada no puede entrar');
    [, $html] = request($client, $base, 'login');
    $login['_csrf_token'] = token($html);
    verify(request($client, $base, 'login', array_replace($login, ['username' => 'hash-user', 'password' => 'Otra-clave-segura']))[0] === 303, 'También acepta contraseñas con hash');
    [, $html] = request($client, $base, 'perfil');
    verify(str_contains($html, 'Cuenta con hash'), 'Perfil corresponde a la cuenta propia autenticada');
    request($client, $base, 'logout', ['_csrf_token' => token($html)]);
    [, $html] = request($client, $base, 'login');
    $login['_csrf_token'] = token($html);
    verify(request($client, $base, 'login', $login)[0] === 303, 'Puede volver a entrar');
    verify($authDb->query("SELECT pass_others_apss FROM wp_jet_cct_funcionarios WHERE user_others_apss = 'admin'")->fetchColumn() === 'Prueba-segura-2026', 'No transforma la clave central existente');
    verify($authDb->query("SELECT pass_others_apss FROM wp_jet_cct_funcionarios WHERE user_others_apss = 'hash-user'")->fetchColumn() === $hash, 'No reemplaza el hash central');
    verify($authDb->query("SHOW TABLES LIKE 'schema_migrations'")->fetchColumn() === false, 'No ejecuta migraciones en la base central');
    $authDb->exec("UPDATE wp_jet_cct_funcionarios SET activo = 'No'");
    verify(request($client,$base,'perfil')[0]===200,'La base central no interviene en la sesión');
    $server->exec('DROP DATABASE '.$authName);
    verify(request($client,$base,'perfil')[0]===200,'Funciona incluso sin existir la base de funcionarios');
    $db->exec('UPDATE accesos_tributarios SET activo=0 WHERE funcionario_id=100');
    verify(request($client, $base, 'perfil')[0] === 303, 'Desactivar cuenta invalida sesión abierta');
    [, $html] = request($client, $base, 'login');
    $login['_csrf_token'] = token($html);
    verify(request($client, $base, 'login', $login)[0] === 401, 'Cuenta inactiva no ingresa');
    for ($i = 0; $i < 12; $i++) { [$status, , $headers] = request($client, $base, 'login', $login); }
    verify($status === 429 && str_contains($headers, 'Retry-After:'), 'Limita intentos persistentes');
    mkdir($temp . '/migrations');
    mkdir($temp . '/checks');
    copy($root . '/database/schema_migrations.sql', $temp . '/schema_migrations.sql');
    file_put_contents($temp . '/migrations/002_test_column.sql', 'ALTER TABLE wp_jet_cct_funcionarios ADD test_column INT NULL');
    file_put_contents($temp . '/checks/002_test_column.sql', "SELECT COUNT(*) > 0 FROM information_schema.columns
        WHERE table_schema = DATABASE() AND table_name = 'wp_jet_cct_funcionarios' AND column_name = 'test_column'");
    $migrator = new Migrator($db, $temp);
    $migrator->run();
    $migrator->run();
    verify((int) $db->query('SELECT COUNT(*) FROM schema_migrations')->fetchColumn() === $migrationCount + 1, 'Columna nueva aplicada una sola vez');
    $db->exec("DELETE FROM schema_migrations WHERE version = '002_test_column'");
    $migrator->run();
    verify((int) $db->query('SELECT COUNT(*) FROM schema_migrations')->fetchColumn() === $migrationCount + 1, 'Recupera DDL confirmado sin registro');
    file_put_contents($temp . '/migrations/002_test_column.sql', '-- Cambio indebido', FILE_APPEND);
    $caught = false;
    try { $migrator->run(); } catch (RuntimeException $error) { $caught = str_contains($error->getMessage(), 'modificó'); }
    verify($caught, 'Detecta edición de migración aplicada');
    echo "Integración completa correcta.\n";
} finally {
    curl_close($client);
    if (is_resource($process)) { proc_terminate($process); proc_close($process); }
    if (is_file($local)) { unlink($local); }
    // Solo el nombre aleatorio generado por ESTA prueba; nunca una base del usuario.
    if (preg_match('/^mvc_test_[a-f0-9]{12}$/D', $name)) {
        $server->exec('DROP DATABASE IF EXISTS ' . $name);
        $server->exec('DROP DATABASE IF EXISTS ' . $authName);
    }
    echo "Registro temporal de la prueba: $temp/server.log\n";
}
