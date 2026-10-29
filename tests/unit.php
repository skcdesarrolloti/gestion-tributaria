<?php
declare(strict_types=1);

require dirname(__DIR__) . '/bootstrap/autoload.php';
require dirname(__DIR__) . '/app/Support/helpers.php';
use App\Services\{AuthService, Csrf, LoginLimiter, ClaveCuenta};

function check(bool $condition, string $message): void {
    if (!$condition) { throw new RuntimeException($message); }
}
session_start();
$_SESSION = [];
check(!Csrf::valid(''), 'No se acepta un token vacío.');
$token = Csrf::token();
check(Csrf::valid($token) && !Csrf::valid('incorrecto'), 'Validación CSRF.');
check(e('<script>"') === '&lt;script&gt;&quot;', 'Escape HTML.');
check(input(['route' => ['inicio']], 'route', 'inicio') === 'inicio', 'Rechazo de arrays.');
foreach (['','corta',str_repeat('a',73),"frase-larga-con\0nulo"] as $badPassword) {
    $rejected=false;try { ClaveCuenta::validate($badPassword); } catch (InvalidArgumentException) { $rejected=true; }
    check($rejected,'Rechaza clave vacía, débil, truncable o con bytes nulos.');
}
ClaveCuenta::validate('Mi frase propia y extensa');
check(ClaveCuenta::identity('Usuario.EXTERNO','Persona ficticia','')[0]==='usuario.externo','Normaliza identidad de cuenta independiente.');
$users = new App\Models\Usuario(new App\Core\Database(['name' => 'no_debe_conectarse'], false));
$auth = new AuthService($users, 'cuentas-propias');
$_SESSION = ['user_id' => 1, 'last_activity' => time()];
check($auth->user() === null && empty($_SESSION['user_id']), 'Invalida sesiones antiguas de cuentas locales.');
$_SESSION = ['user_id' => 1, 'last_activity' => time(), 'auth_source' => 'otra-fuente'];
check($auth->user() === null, 'Una sesión de otro origen no autentica una cuenta propia.');
$directory = sys_get_temp_dir() . '/mvc-limiter-' . bin2hex(random_bytes(8));
mkdir($directory);
try {
    $limiter = new LoginLimiter($directory);
    check($limiter->consume('ip', 2) === 0, 'Primer intento.');
    check($limiter->consume('ip', 2) === 0, 'Segundo intento.');
    check($limiter->consume('ip', 2) > 0, 'Bloqueo persistente.');
    $file = $directory . '/' . hash('sha256', 'ip') . '.json';
    file_put_contents($file, json_encode([time() - 1000]));
    check($limiter->consume('ip', 2) === 0, 'Ventana vencida.');
} finally {
    foreach (glob($directory . '/*.json') as $file) { unlink($file); }
    rmdir($directory);
}
require __DIR__ . '/contribuyentes-unit.php';
require __DIR__ . '/ingresos-unit.php';
require __DIR__ . '/nomina-unit.php';
require __DIR__ . '/retenciones-unit.php';
require __DIR__ . '/extractos-unit.php';
require __DIR__ . '/perfil-anual-sugerencias-unit.php';
require __DIR__ . '/ingreso-preguntas-unit.php';
require __DIR__ . '/proyeccion-unit.php';
require __DIR__ . '/exogena-unit.php';
require __DIR__ . '/exogena-1007-unit.php';
require __DIR__ . '/exogena-1001-unit.php';
require __DIR__ . '/exogena-catalog-unit.php';
require __DIR__ . '/beneficios-unit.php';
require __DIR__ . '/normas-unit.php';
require __DIR__ . '/simulador-unit.php';
require __DIR__ . '/caracterizacion-unit.php';
require __DIR__ . '/perfil-alcance-unit.php';
require __DIR__ . '/rut-unit.php';
require __DIR__ . '/rut-multi-unit.php';
require __DIR__.'/parametros-unit.php';
require __DIR__.'/renta-acumulada-unit.php';
require __DIR__.'/colpensiones-unit.php';
require __DIR__.'/prestamos-unit.php';
require __DIR__.'/caja-social-unit.php';
echo "Pruebas de seguridad y contribuyentes correctas.\n";
