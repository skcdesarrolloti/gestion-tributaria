CREATE TABLE IF NOT EXISTS perfiles_renta (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    contribuyente_id INT UNSIGNED NOT NULL,
    anio SMALLINT UNSIGNED NOT NULL,
    tipo VARCHAR(20) NOT NULL,
    residencia ENUM('residente','no_residente') NOT NULL,
    regimen ENUM('ordinario') NOT NULL,
    servicios ENUM('pendiente','costos','exencion') NOT NULL DEFAULT 'pendiente',
    soporte_perfil VARCHAR(500) NOT NULL,
    formulario CHAR(3) NOT NULL,
    version VARCHAR(20) NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY perfiles_contribuyente_anio (contribuyente_id, anio),
    CONSTRAINT perfiles_contribuyente_fk FOREIGN KEY (contribuyente_id) REFERENCES contribuyentes (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
