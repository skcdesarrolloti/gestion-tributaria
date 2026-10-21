CREATE TABLE IF NOT EXISTS catalogos_rut (
    grupo VARCHAR(30) NOT NULL,
    codigo VARCHAR(10) NOT NULL,
    nombre VARCHAR(400) NOT NULL,
    fuente VARCHAR(250) NOT NULL,
    revisado DATE NOT NULL,
    PRIMARY KEY (grupo, codigo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
