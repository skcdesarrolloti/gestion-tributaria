CREATE TABLE historial_nominas (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    nomina_id INT UNSIGNED NOT NULL,
    revision INT UNSIGNED NOT NULL,
    datos LONGTEXT NOT NULL,
    huella CHAR(64) NOT NULL,
    actor_id INT UNSIGNED NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    CONSTRAINT historial_nomina_fk FOREIGN KEY (nomina_id) REFERENCES nominas_laborales(id),
    UNIQUE KEY historial_nomina_revision (nomina_id,revision)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
