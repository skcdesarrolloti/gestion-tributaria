<?php
declare(strict_types=1);

$router->add('GET', 'inicio', [$homeController, 'index']);
$router->add('GET', 'perfil', [$homeController, 'profile']);
$router->add('GET', 'login', [$authController, 'login']);
$router->add('POST', 'login', [$authController, 'login']);
$router->add('POST', 'logout', [$authController, 'logout']);
$router->add('GET', 'instalacion', [$setupController, 'index']);
$router->add('POST', 'instalacion', [$setupController, 'index']);
