CREATE TABLE IF NOT EXISTS contribuyentes (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    funcionario_id BIGINT UNSIGNED NOT NULL,
    nombre VARCHAR(150) NOT NULL,
    tipo ENUM('natural', 'juridica', 'sucesion') NOT NULL,
    documento_tipo ENUM('CC', 'CE', 'NIT', 'PAS') NOT NULL,
    documento_numero VARCHAR(30) NOT NULL,
    dv CHAR(1) NULL,
    fuentes TEXT NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY contribuyentes_documento_owner (funcionario_id, documento_tipo, documento_numero),
    CONSTRAINT contribuyentes_funcionario_fk FOREIGN KEY (funcionario_id)
        REFERENCES wp_jet_cct_funcionarios (_ID)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
