<?php
declare(strict_types=1);

$router->add('GET', 'inicio', [$homeController, 'index']);
$router->add('GET', 'perfil', [$homeController, 'profile']);
$router->add('GET', 'login', [$authController, 'login']);
$router->add('POST', 'login', [$authController, 'login']);
$router->add('POST', 'logout', [$authController, 'logout']);
// Compatibilidad con pestañas antiguas: ya no existe un registro de administradores.
$router->add('GET', 'instalacion', static fn () => redirect($config, 'login'));
