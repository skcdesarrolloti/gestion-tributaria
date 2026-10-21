CREATE TABLE IF NOT EXISTS rut_documentos (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    contribuyente_id INT UNSIGNED NOT NULL,
    anio SMALLINT UNSIGNED NOT NULL,
    titulo VARCHAR(160) NOT NULL,
    archivo VARCHAR(80) NOT NULL,
    sha256 CHAR(64) NOT NULL,
    bytes INT UNSIGNED NOT NULL,
    metadatos LONGTEXT NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY rut_documento_huella (contribuyente_id, anio, sha256),
    CONSTRAINT rut_documento_contribuyente_fk FOREIGN KEY (contribuyente_id) REFERENCES contribuyentes (id),
    CONSTRAINT rut_documento_metadatos_json CHECK (JSON_VALID(metadatos))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
