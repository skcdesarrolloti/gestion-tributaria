CREATE TABLE IF NOT EXISTS conceptos_ingresos (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    formulario CHAR(3) NOT NULL,
    version VARCHAR(20) NOT NULL,
    casilla SMALLINT UNSIGNED NOT NULL,
    bloque VARCHAR(100) NOT NULL,
    nombre VARCHAR(200) NOT NULL,
    explicacion TEXT NOT NULL,
    fundamento VARCHAR(250) NOT NULL,
    ejemplo TEXT NOT NULL,
    soportes TEXT NOT NULL,
    clase ENUM('ingreso','reduccion','calculado') NOT NULL,
    formula VARCHAR(80) NOT NULL DEFAULT '',
    PRIMARY KEY (id),
    UNIQUE KEY conceptos_formulario_version_casilla (formulario, version, casilla)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
