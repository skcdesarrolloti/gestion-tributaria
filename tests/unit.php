<?php
declare(strict_types=1);

require dirname(__DIR__) . '/bootstrap/autoload.php';
require dirname(__DIR__) . '/app/Support/helpers.php';
use App\Services\{AuthService, Csrf, LoginLimiter, PasswordVerifier};

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
check(PasswordVerifier::verify('clave-existente', 'clave-existente', true), 'Compatibilidad con clave institucional existente.');
check(!PasswordVerifier::verify('incorrecta', 'clave-existente', true), 'No acepta una clave distinta.');
check(!PasswordVerifier::verify('clave-existente', 'clave-existente', false), 'Respeta desactivación de compatibilidad.');
check(!PasswordVerifier::verify('', '', true), 'No acepta claves vacías.');
$hash = password_hash('clave-segura', PASSWORD_DEFAULT);
check(PasswordVerifier::verify('clave-segura', $hash, true), 'Valida hashes.');
check(!PasswordVerifier::verify($hash, $hash, true), 'Un hash no sirve como contraseña.');
$bcrypt = password_hash(str_repeat('a', 72), PASSWORD_BCRYPT);
check(!PasswordVerifier::verify(str_repeat('a', 73), $bcrypt, true), 'Evita truncamiento bcrypt.');
$users = new App\Models\Funcionario(new App\Core\Database(['name' => 'no_debe_conectarse'], false));
$auth = new AuthService($users, true, 'central');
$_SESSION = ['user_id' => 1, 'last_activity' => time()];
check($auth->user() === null && empty($_SESSION['user_id']), 'Invalida sesiones antiguas de cuentas locales.');
$_SESSION = ['user_id' => 1, 'last_activity' => time(), 'auth_source' => 'otra-fuente'];
check($auth->user() === null, 'Una sesión no se reutiliza al cambiar la fuente de funcionarios.');
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
echo "Pruebas de seguridad básicas correctas.\n";
