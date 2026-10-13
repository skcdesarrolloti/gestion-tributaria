-- Ejecutado automáticamente por la aplicación. No borra datos.
-- Esquema local compatible con los campos del login de Servicios Comerciales.
CREATE TABLE IF NOT EXISTS wp_jet_cct_funcionarios (
    _ID BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    id_empleado VARCHAR(50) NOT NULL DEFAULT '',
    nombre VARCHAR(150) NOT NULL,
    rol VARCHAR(50) NOT NULL DEFAULT 'administrador',
    id_cargo VARCHAR(50) NOT NULL DEFAULT '',
    user_others_apss VARCHAR(100) NOT NULL,
    pass_others_apss VARCHAR(255) NOT NULL,
    activo ENUM('Si', 'No') NOT NULL DEFAULT 'Si',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (_ID),
    UNIQUE KEY funcionarios_usuario_unique (user_others_apss)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
