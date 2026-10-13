<?php
declare(strict_types=1);

// Copia como local.php. Ese archivo es privado y Git lo ignora.
return [
    // Local: ''. Hostinger en subcarpeta: '/gestion-tributaria/public'.
    'base_path' => '',
    // false SOLO para pruebas locales por HTTP; true en Hostinger con HTTPS.
    'secure_cookies' => true,
    // Genera una clave con: php bin/setup-key.php. Pégala aquí y en el primer acceso.
    'setup_key' => '',
    'database' => [
        'host' => 'localhost',
        'port' => 3306,
        'name' => 'u350704768_tributaria',
        'username' => 'COMPLETAR_USUARIO_MYSQL',
        'password' => 'COMPLETAR_CLAVE_MYSQL',
    ],
];
