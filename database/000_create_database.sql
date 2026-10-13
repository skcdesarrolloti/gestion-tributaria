-- Plantilla ejecutada por la aplicación si la base aún no existe.
-- El nombre se valida antes de sustituir el marcador.
CREATE DATABASE IF NOT EXISTS `{{database}}`
    CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
