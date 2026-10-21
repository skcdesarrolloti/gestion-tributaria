CREATE TABLE IF NOT EXISTS caracterizaciones_tributarias (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    contribuyente_id INT UNSIGNED NOT NULL,
    anio SMALLINT UNSIGNED NOT NULL,
    bloque VARCHAR(40) NOT NULL,
    datos LONGTEXT NOT NULL,
    revision INT UNSIGNED NOT NULL DEFAULT 1,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY caracterizacion_bloque (contribuyente_id, anio, bloque),
    CONSTRAINT caracterizacion_contribuyente_fk FOREIGN KEY (contribuyente_id) REFERENCES contribuyentes (id),
    CONSTRAINT caracterizacion_datos_json CHECK (JSON_VALID(datos))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
