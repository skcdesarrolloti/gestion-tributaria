SELECT (SELECT COUNT(*) = 8 FROM information_schema.columns
        WHERE table_schema = DATABASE() AND table_name = 'exogena_registros'
          AND column_name IN ('id','contribuyente_id','formato','anio','estructura','datos','created_at','updated_at'))
   AND (SELECT COUNT(*) = 1 FROM information_schema.key_column_usage
        WHERE table_schema = DATABASE() AND table_name = 'exogena_registros'
          AND referenced_table_name = 'contribuyentes' AND column_name = 'contribuyente_id')
   AND (SELECT COUNT(*) = 3 FROM information_schema.statistics
        WHERE table_schema = DATABASE() AND table_name = 'exogena_registros'
          AND index_name = 'exogena_registros_empresa_formato_anio')
   AND (SELECT COUNT(*) = 1 FROM information_schema.table_constraints
        WHERE constraint_schema = DATABASE() AND table_name = 'exogena_registros'
          AND constraint_name = 'exogena_registros_json' AND constraint_type = 'CHECK');
