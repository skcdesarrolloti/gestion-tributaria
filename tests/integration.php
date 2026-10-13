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
$key = bin2hex(random_bytes(32));
$config = ['database' => $dbConfig, 'secure_cookies' => false, 'setup_key' => $key,
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
    [$status, $html] = request($client, $base, 'instalacion');
    verify($status === 200 && token($html) !== '', 'Base y tablas creadas automáticamente');
    $db = new PDO(sprintf('mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4', $dbConfig['host'], $dbConfig['port'], $name),
        $dbConfig['username'], $dbConfig['password'], [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    verify((int) $db->query('SELECT COUNT(*) FROM schema_migrations')->fetchColumn() === 1, 'Migración registrada una vez');
    $form = ['_csrf_token' => token($html), 'setup_key' => $key, 'name' => '<b>Administrador</b>',
        'username' => 'admin', 'password' => 'Prueba-segura-2026', 'confirmation' => 'Prueba-segura-2026'];
    verify(request($client, $base, 'instalacion', array_replace($form, ['_csrf_token' => '']))[0] === 419, 'Instalación exige CSRF');
    verify(request($client, $base, 'instalacion', array_replace($form, ['setup_key' => 'incorrecta']))[0] === 403, 'Instalación exige clave privada');
    verify(request($client, $base, 'instalacion', array_replace($form, ['confirmation' => 'otra']))[0] === 422, 'Valida confirmación de contraseña');
    verify(request($client, $base, 'instalacion', $form)[0] === 303, 'Primer administrador creado desde web');
    verify(request($client, $base, 'instalacion')[0] === 404, 'Instalador cerrado después del primer usuario');
    verify(request($client, $base, 'inicio')[0] === 303, 'Inicio exige autenticación');
    [$status, $html] = request($client, $base, 'login');
    $login = ['_csrf_token' => token($html), 'username' => 'admin', 'password' => 'Prueba-segura-2026'];
    verify(request($client, $base, 'login', array_replace($login, ['_csrf_token' => '']))[0] === 419, 'Login exige CSRF');
    verify(request($client, $base, 'login', array_replace($login, ['password' => 'errada']))[0] === 401, 'Rechaza contraseña incorrecta');
    [$status, , $headers] = request($client, $base, 'login', $login);
    verify($status === 303 && str_contains(strtolower($headers), 'set-cookie:'), 'Login válido regenera sesión');
    [$status, $html] = request($client, $base, 'perfil');
    verify($status === 200 && str_contains($html, '&lt;b&gt;Administrador&lt;/b&gt;'), 'Perfil escapa los datos del usuario');
    verify(!str_contains($html, '$2y$'), 'Perfil no expone hash');
    verify(request($client, $base, 'logout')[0] === 405, 'Logout rechaza GET');
    verify(request($client, $base, 'logout', ['_csrf_token' => ''])[0] === 419, 'Logout exige CSRF');
    verify(request($client, $base, 'logout', ['_csrf_token' => token($html)])[0] === 303, 'Logout termina sesión');
    verify(request($client, $base, 'perfil')[0] === 303, 'Sesión cerrada no puede entrar');
    [, $html] = request($client, $base, 'login');
    $login['_csrf_token'] = token($html);
    verify(request($client, $base, 'login', $login)[0] === 303, 'Puede volver a entrar');
    $db->exec("UPDATE wp_jet_cct_funcionarios SET activo = 'No'");
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
    verify((int) $db->query('SELECT COUNT(*) FROM schema_migrations')->fetchColumn() === 2, 'Columna nueva aplicada una sola vez');
    $db->exec("DELETE FROM schema_migrations WHERE version = '002_test_column'");
    $migrator->run();
    verify((int) $db->query('SELECT COUNT(*) FROM schema_migrations')->fetchColumn() === 2, 'Recupera DDL confirmado sin registro');
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
    if (preg_match('/^mvc_test_[a-f0-9]{12}$/D', $name)) { $server->exec('DROP DATABASE IF EXISTS ' . $name); }
    echo "Registro temporal de la prueba: $temp/server.log\n";
}
