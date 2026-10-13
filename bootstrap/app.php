<?php
declare(strict_types=1);

require __DIR__ . '/autoload.php';
require dirname(__DIR__) . '/app/Support/helpers.php';

ini_set('display_errors', '0');
ini_set('log_errors', '1');
ini_set('error_log', dirname(__DIR__) . '/storage/logs/app.log');
error_reporting(E_ALL);
set_exception_handler(static function (Throwable $error): void {
    error_log((string) $error);
    http_response_code(500);
    header('Content-Type: text/html; charset=UTF-8');
    echo '<h1>No fue posible completar la solicitud.</h1>';
    echo '<p>Revisa la configuración y el registro privado storage/logs/app.log.</p>';
});

$config = App\Core\Config::load(dirname(__DIR__));
date_default_timezone_set('America/Bogota');
ini_set('session.use_strict_mode', '1');
ini_set('session.use_only_cookies', '1');
ini_set('session.gc_maxlifetime', '1800');
session_name($config['session_name']);
session_start([
    'cookie_httponly' => true,
    'cookie_secure' => $config['secure_cookies'],
    'cookie_samesite' => 'Lax',
    'cookie_path' => $config['base_path'] ?: '/',
]);
header('Content-Type: text/html; charset=UTF-8');
header('Cache-Control: no-store');
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('Referrer-Policy: same-origin');

$database = new App\Core\Database($config['database']);
(new App\Services\Migrator($database->connection(), dirname(__DIR__) . '/database'))->run();
$authDatabase = new App\Core\Database($config['auth_database'], false);
$users = new App\Models\Funcionario($authDatabase);
$authSource = hash('sha256', json_encode([
    $config['auth_database']['host'], $config['auth_database']['port'], $config['auth_database']['name'],
]));
$auth = new App\Services\AuthService($users, $config['auth_allow_legacy_passwords'], $authSource);
$view = new App\Core\View(dirname(__DIR__) . '/app/Views', $config);
$limiter = new App\Services\LoginLimiter(($config['storage_path'] ?? dirname(__DIR__) . '/storage') . '/rate-limits');
$authController = new App\Controllers\AuthController($auth, $view, $limiter, $config);
$homeController = new App\Controllers\HomeController($auth, $view, $config);
$router = new App\Core\Router();
require dirname(__DIR__) . '/routes/web.php';
return $router;
