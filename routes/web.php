<?php
declare(strict_types=1);

$router->add('GET', 'inicio', [$homeController, 'index']);
$router->add('GET', 'perfil', [$cuentaController, 'profile']);
$router->add('POST', 'perfil', [$cuentaController, 'profile']);
$router->add('GET', 'usuarios/crear', [$cuentaController, 'create']);
$router->add('POST', 'usuarios/crear', [$cuentaController, 'create']);
$router->add('POST', 'usuarios/restablecer', [$cuentaController, 'reset']);
$router->add('GET', 'activar', [$activacionController, 'index']);
$router->add('POST', 'activar', [$activacionController, 'index']);
$router->add('GET', 'accesos', [$accesoController, 'index']);
$router->add('POST', 'accesos', [$accesoController, 'index']);
$router->add('GET', 'beneficios', [$beneficioController, 'index']);
$router->add('GET', 'seguridad-social', [$seguridadSocialController, 'index']);
$router->add('GET', 'planeacion/datos', [$caracterizacionController, 'index']);
$router->add('POST', 'planeacion/datos', [$caracterizacionController, 'index']);
$router->add('GET', 'simulador', [$simuladorController, 'index']);
$router->add('POST', 'simulador', [$simuladorController, 'index']);
$router->add('POST', 'simulador/calcular', [$simuladorController, 'calculate']);
$router->add('GET', 'contribuyentes', [$contribuyenteController, 'index']);
$router->add('GET', 'contribuyentes/perfil', [$caracterizacionController, 'index']);
$router->add('POST', 'contribuyentes/perfil', [$caracterizacionController, 'index']);
$router->add('GET', 'contribuyentes/rut', [$rutController, 'index']);
$router->add('POST', 'contribuyentes/rut', [$rutController, 'index']);
$router->add('GET', 'contribuyentes/rut/documento', [$rutController, 'download']);
$router->add('GET', 'contribuyentes/formulario', [$contribuyenteController, 'form']);
$router->add('POST', 'contribuyentes/formulario', [$contribuyenteController, 'form']);
$router->add('GET', 'ingresos', [$ingresoController, 'index']);
$router->add('POST', 'ingresos', [$ingresoController, 'index']);
$router->add('GET', 'ingresos/perfil', [$ingresoController, 'profile']);
$router->add('POST', 'ingresos/perfil', [$ingresoController, 'profile']);
$router->add('GET', 'ingresos/detalle', [$ingresoController, 'detail']);
$router->add('GET', 'proyeccion', [$proyeccionController, 'index']);
$router->add('GET', 'exogena', [$exogenaController, 'index']);
foreach ($exogenaCatalogControllers as $code=>$formatController) {
    $router->add('GET', 'exogena/'.$code, [$formatController, 'format']);
    $router->add('GET', 'exogena/'.$code.'/registro', [$formatController, 'record']);
    $router->add('POST', 'exogena/'.$code.'/registro', [$formatController, 'record']);
}
$router->add('GET', 'exogena/1001', [$exogena1001Controller, 'format']);
$router->add('GET', 'exogena/1001/registro', [$exogena1001Controller, 'record']);
$router->add('POST', 'exogena/1001/registro', [$exogena1001Controller, 'record']);
$router->add('GET', 'exogena/1007', [$exogena1007Controller, 'format']);
$router->add('GET', 'exogena/1007/registro', [$exogena1007Controller, 'record']);
$router->add('POST', 'exogena/1007/registro', [$exogena1007Controller, 'record']);
$router->add('GET', 'exogena/5247', [$exogenaController, 'format']);
$router->add('GET', 'exogena/5247/registro', [$exogenaController, 'record']);
$router->add('POST', 'exogena/5247/registro', [$exogenaController, 'record']);
$router->add('GET', 'ingresos/movimiento', [$ingresoController, 'movement']);
$router->add('POST', 'ingresos/movimiento', [$ingresoController, 'movement']);
$router->add('GET', 'login', [$authController, 'login']);
$router->add('POST', 'login', [$authController, 'login']);
$router->add('POST', 'logout', [$authController, 'logout']);
// Compatibilidad con pestañas antiguas: ya no existe un registro de administradores.
$router->add('GET', 'instalacion', static fn () => redirect($config, 'login'));
