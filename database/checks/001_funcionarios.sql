SELECT COUNT(*) = 8 FROM information_schema.columns
WHERE table_schema = DATABASE() AND table_name = 'wp_jet_cct_funcionarios'
AND column_name IN ('_ID', 'id_empleado', 'nombre', 'rol', 'id_cargo', 'user_others_apss', 'pass_others_apss', 'activo');
