SELECT (SELECT COUNT(*) = 7 FROM information_schema.columns
        WHERE table_schema = DATABASE() AND table_name = 'escenarios_renta'
          AND column_name IN ('id','perfil_id','nombre','version','entradas','resultado','created_at'))
   AND (SELECT COUNT(*) = 1 FROM information_schema.key_column_usage
        WHERE table_schema = DATABASE() AND table_name = 'escenarios_renta'
          AND column_name = 'perfil_id' AND referenced_table_name = 'perfiles_renta')
   AND (SELECT COUNT(*) = 2 FROM information_schema.statistics
        WHERE table_schema = DATABASE() AND table_name = 'escenarios_renta' AND index_name = 'escenarios_renta_perfil')
   AND (SELECT COUNT(*) = 2 FROM information_schema.table_constraints
        WHERE constraint_schema = DATABASE() AND table_name = 'escenarios_renta'
          AND constraint_type = 'CHECK' AND constraint_name IN ('escenarios_renta_entradas_json','escenarios_renta_resultado_json'));
