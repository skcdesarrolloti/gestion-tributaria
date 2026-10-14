SELECT COUNT(*) = 1 FROM information_schema.columns
WHERE table_schema = DATABASE() AND table_name = 'perfiles_renta'
  AND column_name = 'servicios'
  AND column_type = 'enum(''pendiente'',''costos'',''exencion'',''no_aplica'')';
