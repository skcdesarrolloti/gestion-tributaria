<?php
declare(strict_types=1);

$router = require dirname(__DIR__) . '/bootstrap/app.php';
$router->dispatch($_SERVER['REQUEST_METHOD'], input($_GET, 'route', 'inicio'));
