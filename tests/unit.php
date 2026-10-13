<?php
declare(strict_types=1);

require dirname(__DIR__) . '/bootstrap/autoload.php';
require dirname(__DIR__) . '/app/Support/helpers.php';
use App\Services\{Csrf, LoginLimiter};

function check(bool $condition, string $message): void {
    if (!$condition) { throw new RuntimeException($message); }
}
$_SESSION = [];
check(!Csrf::valid(''), 'No se acepta un token vacío.');
$token = Csrf::token();
check(Csrf::valid($token) && !Csrf::valid('incorrecto'), 'Validación CSRF.');
check(e('<script>"') === '&lt;script&gt;&quot;', 'Escape HTML.');
check(input(['route' => ['inicio']], 'route', 'inicio') === 'inicio', 'Rechazo de arrays.');
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
