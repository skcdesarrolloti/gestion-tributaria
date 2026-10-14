SELECT (SELECT COUNT(*) = 11 FROM information_schema.columns
        WHERE table_schema = DATABASE() AND table_name = 'perfiles_renta'
          AND column_name IN ('id','contribuyente_id','anio','tipo','residencia','regimen',
                             'servicios','soporte_perfil','formulario','version','created_at'))
   AND (SELECT COUNT(*) = 2 FROM information_schema.statistics
        WHERE table_schema = DATABASE() AND table_name = 'perfiles_renta'
          AND index_name = 'perfiles_contribuyente_anio' AND non_unique = 0)
   AND (SELECT COUNT(*) = 1 FROM information_schema.key_column_usage
        WHERE table_schema = DATABASE() AND table_name = 'perfiles_renta'
          AND column_name = 'contribuyente_id' AND referenced_table_name = 'contribuyentes');
