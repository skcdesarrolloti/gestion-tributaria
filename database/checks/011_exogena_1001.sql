SELECT (SELECT COUNT(*) = 7 FROM information_schema.columns
        WHERE table_schema = DATABASE() AND table_name = 'exogena_1001'
          AND column_name IN ('id','contribuyente_id','anio','estructura','datos','created_at','updated_at'))
   AND (SELECT COUNT(*) = 1 FROM information_schema.key_column_usage
        WHERE table_schema = DATABASE() AND table_name = 'exogena_1001'
          AND referenced_table_name = 'contribuyentes' AND column_name = 'contribuyente_id')
   AND (SELECT COUNT(*) = 2 FROM information_schema.statistics
        WHERE table_schema = DATABASE() AND table_name = 'exogena_1001'
          AND index_name = 'exogena_1001_empresa_anio')
   AND (SELECT COUNT(*) = 1 FROM information_schema.table_constraints
        WHERE constraint_schema = DATABASE() AND table_name = 'exogena_1001'
          AND constraint_name = 'exogena_1001_json' AND constraint_type = 'CHECK');
