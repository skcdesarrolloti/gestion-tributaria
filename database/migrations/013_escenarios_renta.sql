CREATE TABLE IF NOT EXISTS escenarios_renta (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    perfil_id INT UNSIGNED NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    version VARCHAR(40) NOT NULL,
    entradas LONGTEXT NOT NULL,
    resultado LONGTEXT NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY escenarios_renta_perfil (perfil_id, id),
    CONSTRAINT escenarios_renta_perfil_fk FOREIGN KEY (perfil_id) REFERENCES perfiles_renta (id),
    CONSTRAINT escenarios_renta_entradas_json CHECK (JSON_VALID(entradas)),
    CONSTRAINT escenarios_renta_resultado_json CHECK (JSON_VALID(resultado))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
