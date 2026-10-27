ALTER TABLE extractos_bancarios ADD COLUMN lectura LONGTEXT NULL, ADD CONSTRAINT extracto_lectura_json CHECK (lectura IS NULL OR JSON_VALID(lectura));
