<?php
declare(strict_types=1);

// Copia como local.php solo en desarrollo; conserva el archivo privado del servidor.
return [
    // En los subdominios actuales: '/public'. En servidor local con -t public: ''.
    'base_path' => '/public',
    'secure_cookies' => true,
    // Datos del proyecto: conservar esta conexión existente.
    'database' => [
        'host' => 'localhost',
        'port' => 3306,
        'name' => 'u350704768_tributaria',
        'username' => 'USUARIO_MYSQL_DEL_PROYECTO',
        'password' => 'CLAVE_MYSQL_DEL_PROYECTO',
    ],
    // Funcionarios existentes: conexión de solo lectura a la base central.
    // Estas son credenciales MySQL, NO user_others_apss/pass_others_apss.
    'auth_database' => [
        'host' => 'localhost',
        'port' => 3306,
        'name' => 'u350704768_M4FvI',
        'username' => 'USUARIO_MYSQL_BASE_CENTRAL',
        'password' => 'CLAVE_MYSQL_BASE_CENTRAL',
    ],
    // Misma compatibilidad de contraseñas que Servicios Comerciales.
    'auth_allow_legacy_passwords' => true,
];
