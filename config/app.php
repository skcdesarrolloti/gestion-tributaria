<?php
declare(strict_types=1);

return [
    'name' => 'Gestión Tributaria',
    'base_path' => '',
    'secure_cookies' => true,
    // Huella de un código aleatorio entregado privadamente al responsable; nunca su valor.
    // Solo sirve mientras no exista ninguna cuenta propia y para un Socio ya autorizado.
    'activation_token_sha256' => '9c8cd2a0e87324e744161b39fc74c8bbd7295b216ee460e24f1aba43798b6705',
    'session_name' => 'gestion_tributaria',
    'database' => [
        'host' => 'localhost',
        'port' => 3306,
        'name' => 'u350704768_tributaria',
        'username' => '',
        'password' => '',
    ],
];
