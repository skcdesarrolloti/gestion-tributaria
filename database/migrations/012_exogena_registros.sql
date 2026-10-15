CREATE TABLE IF NOT EXISTS exogena_registros (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    contribuyente_id INT UNSIGNED NOT NULL,
    formato CHAR(4) NOT NULL,
    anio SMALLINT UNSIGNED NOT NULL,
    estructura VARCHAR(40) NOT NULL,
    datos LONGTEXT NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY exogena_registros_empresa_formato_anio (contribuyente_id, formato, anio),
    CONSTRAINT exogena_registros_empresa_fk FOREIGN KEY (contribuyente_id) REFERENCES contribuyentes (id),
    CONSTRAINT exogena_registros_json CHECK (JSON_VALID(datos))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
