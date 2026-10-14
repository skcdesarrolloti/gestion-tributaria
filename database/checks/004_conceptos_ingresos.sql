SELECT (SELECT COUNT(*) = 12 FROM information_schema.columns
        WHERE table_schema = DATABASE() AND table_name = 'conceptos_ingresos'
          AND column_name IN ('id','formulario','version','casilla','bloque','nombre','explicacion',
                             'fundamento','ejemplo','soportes','clase','formula'))
   AND (SELECT COUNT(*) = 3 FROM information_schema.statistics
        WHERE table_schema = DATABASE() AND table_name = 'conceptos_ingresos'
          AND index_name = 'conceptos_formulario_version_casilla' AND non_unique = 0);
