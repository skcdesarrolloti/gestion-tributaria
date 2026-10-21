SELECT (SELECT COUNT(*) = 7 FROM information_schema.columns WHERE table_schema = DATABASE()
    AND table_name = 'caracterizaciones_tributarias' AND column_name IN ('id','contribuyente_id','anio','bloque','datos','revision','updated_at'))
    AND (SELECT COUNT(*) = 3 FROM information_schema.statistics WHERE table_schema = DATABASE()
    AND table_name = 'caracterizaciones_tributarias' AND index_name = 'caracterizacion_bloque')
    AND (SELECT COUNT(*) = 1 FROM information_schema.key_column_usage WHERE table_schema = DATABASE()
    AND table_name = 'caracterizaciones_tributarias' AND referenced_table_name = 'contribuyentes')
    AND (SELECT COUNT(*) = 1 FROM information_schema.table_constraints WHERE constraint_schema = DATABASE()
    AND table_name = 'caracterizaciones_tributarias' AND constraint_name = 'caracterizacion_datos_json');
