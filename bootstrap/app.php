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
$parametroModel=new App\Models\ParametroAnual($database);
App\Services\ParametrosAnuales::load($parametroModel->latest());
$users = new App\Models\Usuario($database);
$authSource = hash('sha256','cuentas-tributarias-v1|'.$config['database']['name']);
$access=new App\Models\AccesoTributario($database);
$auth = new App\Services\AuthService($users, $authSource, $access);
$view = new App\Core\View(dirname(__DIR__) . '/app/Views', $config);
$parametroController=new App\Controllers\ParametroAnualController($auth,$parametroModel,$view,$config);
$cuentaController=new App\Controllers\CuentaController($auth,$users,$database,$view,$config);
$activation=new App\Services\ActivacionCuenta($database,($config['storage_path']??dirname(__DIR__).'/storage').'/activacion-inicial.json',$config);
$activacionController=new App\Controllers\ActivacionController($users,$activation,$view,$config);
// La activación es privada y de una sola vez; no es un registro público de Socios.
if (!$users->exists() && input($_GET,'route')!=='activar') { redirect($config,'activar'); }
if ($users->exists() && input($_GET,'route')!=='activar') {
    $currentUser=$auth->user();
    $allowedBeforeChange=['perfil','logout'];
    if ($currentUser && App\Services\AccesoRoles::socio($currentUser)) {
        // El Socio puede recuperar otra cuenta sin cambiar ahora su propia contraseña.
        array_push($allowedBeforeChange,'accesos','usuarios/crear','usuarios/restablecer');
    }
    if ($currentUser && $currentUser['cambiar_clave'] && !in_array(input($_GET,'route'),$allowedBeforeChange,true)) { redirect($config,'perfil'); }
}
$accesoController=new App\Controllers\AccesoController($auth,$access,$users,new App\Models\Contribuyente($database),$view,$config);
$limiter = new App\Services\LoginLimiter(($config['storage_path'] ?? dirname(__DIR__) . '/storage') . '/rate-limits');
$authController = new App\Controllers\AuthController($auth, $view, $limiter, $config);
$homeController = new App\Controllers\HomeController($auth, $view, $config);
$registroController=new App\Controllers\RegistroController($auth,new App\Models\Contribuyente($database),new App\Models\Ingreso($database),$view,$config,new App\Models\Caracterizacion($database),new App\Models\ExtractoBancario($database),new App\Models\ComprobantePension($database));
$retencionController=new App\Controllers\RetencionController($auth,new App\Models\Ingreso($database),new App\Models\Caracterizacion($database),$view,$config);
$extractoController=new App\Controllers\ExtractoController($auth,new App\Models\Contribuyente($database),new App\Models\ExtractoBancario($database),$view,$config,new App\Models\PrestamoPersonal($database));
$extractoLoteController=new App\Controllers\ExtractoLoteController($auth,new App\Models\Contribuyente($database),new App\Models\ExtractoBancario($database),$config,new App\Models\PrestamoPersonal($database));
$pensionComprobanteController=new App\Controllers\PensionComprobanteController($auth,new App\Models\Contribuyente($database),new App\Models\Ingreso($database),new App\Models\ComprobantePension($database),new App\Models\ExtractoBancario($database),$config);
$prestamoController=new App\Controllers\PrestamoController($auth,new App\Models\Contribuyente($database),new App\Models\PrestamoPersonal($database),new App\Models\Ingreso($database),new App\Models\ComprobantePension($database),new App\Models\ExtractoBancario($database),$view,$config);
$movimientoPersonalController=new App\Controllers\MovimientoPersonalController($auth,new App\Models\ExtractoBancario($database),new App\Models\Contribuyente($database),new App\Models\Caracterizacion($database));
$nominaController=new App\Controllers\NominaController($auth,new App\Models\Contribuyente($database),new App\Models\Ingreso($database),new App\Models\NominaLaboral($database),$view,$config);
$beneficioController = new App\Controllers\BeneficioController($auth, $view, $config);
$seguridadSocialController = new App\Controllers\SeguridadSocialController($auth, $view, $config);
$simuladorController = new App\Controllers\SimuladorController($auth, new App\Models\Ingreso($database), new App\Models\EscenarioRenta($database), $view, $config);
$contribuyenteController = new App\Controllers\ContribuyenteController(
    $auth, new App\Models\Contribuyente($database), $view, $config,
    new App\Services\RutRegistro($database,$config['storage_path']??dirname(__DIR__).'/storage')
);
$caracterizacionController = new App\Controllers\CaracterizacionController(
    $auth, new App\Models\Contribuyente($database), new App\Models\Caracterizacion($database), $view, $config, new App\Models\RutDocumento($database)
);
$rutController = new App\Controllers\RutController(
    $auth, new App\Models\Contribuyente($database), new App\Models\Caracterizacion($database),
    new App\Models\RutDocumento($database), $view, $config
);
$router = new App\Core\Router();
$ingresoController = new App\Controllers\IngresoController(
    $auth, new App\Models\Contribuyente($database), new App\Models\Ingreso($database), $view, $config,new App\Models\Caracterizacion($database)
);
$proyeccionController = new App\Controllers\ProyeccionController(
    $auth, new App\Models\Ingreso($database), $view, $config
);
$exogenaController = new App\Controllers\ExogenaController(
    $auth, new App\Models\Contribuyente($database), new App\Models\Exogena5247($database), $view, $config
);
$exogena1007Controller = new App\Controllers\ExogenaController(
    $auth, new App\Models\Contribuyente($database), new App\Models\Exogena1007($database), $view, $config, '1007'
);
$exogena1001Controller = new App\Controllers\ExogenaController(
    $auth, new App\Models\Contribuyente($database), new App\Models\Exogena1001($database), $view, $config, '1001'
);
$exogenaCatalogControllers=[];
foreach (App\Services\ExogenaSchemas::all() as $code=>$schema) {
    $exogenaCatalogControllers[$code]=new App\Controllers\ExogenaController(
        $auth, new App\Models\Contribuyente($database), new App\Models\ExogenaRecords($database,(string)$code,$schema),
        $view, $config, (string)$code, $schema
    );
}
require dirname(__DIR__) . '/routes/web.php';
return $router;
