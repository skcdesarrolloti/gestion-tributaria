CREATE TABLE accesos_tributarios (
    funcionario_id BIGINT UNSIGNED NOT NULL PRIMARY KEY,
    login_hash CHAR(64) NOT NULL,
    perfil ENUM('socio','usuario') NOT NULL,
    contribuyente_id INT UNSIGNED NULL,
    activo TINYINT(1) NOT NULL DEFAULT 1,
    asignado_por BIGINT UNSIGNED NULL,
    revision INT UNSIGNED NOT NULL DEFAULT 1,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY acceso_login_hash (login_hash),
    CONSTRAINT acceso_contribuyente FOREIGN KEY (contribuyente_id) REFERENCES contribuyentes(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
