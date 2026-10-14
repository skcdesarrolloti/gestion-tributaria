<?php
declare(strict_types=1);

$router->add('GET', 'inicio', [$homeController, 'index']);
$router->add('GET', 'perfil', [$homeController, 'profile']);
$router->add('GET', 'contribuyentes', [$contribuyenteController, 'index']);
$router->add('GET', 'contribuyentes/formulario', [$contribuyenteController, 'form']);
$router->add('POST', 'contribuyentes/formulario', [$contribuyenteController, 'form']);
$router->add('GET', 'ingresos', [$ingresoController, 'index']);
$router->add('POST', 'ingresos', [$ingresoController, 'index']);
$router->add('GET', 'ingresos/perfil', [$ingresoController, 'profile']);
$router->add('POST', 'ingresos/perfil', [$ingresoController, 'profile']);
$router->add('GET', 'ingresos/detalle', [$ingresoController, 'detail']);
$router->add('GET', 'ingresos/movimiento', [$ingresoController, 'movement']);
$router->add('POST', 'ingresos/movimiento', [$ingresoController, 'movement']);
$router->add('GET', 'login', [$authController, 'login']);
$router->add('POST', 'login', [$authController, 'login']);
$router->add('POST', 'logout', [$authController, 'logout']);
// Compatibilidad con pestañas antiguas: ya no existe un registro de administradores.
$router->add('GET', 'instalacion', static fn () => redirect($config, 'login'));
