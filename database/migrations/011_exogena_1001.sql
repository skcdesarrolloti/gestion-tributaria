CREATE TABLE IF NOT EXISTS exogena_1001 (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    contribuyente_id INT UNSIGNED NOT NULL,
    anio SMALLINT UNSIGNED NOT NULL,
    estructura VARCHAR(30) NOT NULL,
    datos LONGTEXT NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY exogena_1001_empresa_anio (contribuyente_id, anio),
    CONSTRAINT exogena_1001_empresa_fk FOREIGN KEY (contribuyente_id) REFERENCES contribuyentes (id),
    CONSTRAINT exogena_1001_json CHECK (JSON_VALID(datos))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
