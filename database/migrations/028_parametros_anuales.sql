CREATE TABLE parametros_anuales (
    anio SMALLINT UNSIGNED NOT NULL,
    revision INT UNSIGNED NOT NULL,
    datos LONGTEXT NOT NULL,
    creado_por BIGINT UNSIGNED NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (anio, revision),
    CONSTRAINT parametros_anuales_json CHECK (JSON_VALID(datos)),
    CONSTRAINT parametros_anuales_actor FOREIGN KEY (creado_por) REFERENCES usuarios_tributarios(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
