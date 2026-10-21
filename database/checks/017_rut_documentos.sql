SELECT (SELECT COUNT(*) = 9 FROM information_schema.columns WHERE table_schema = DATABASE()
    AND table_name = 'rut_documentos' AND column_name IN ('id','contribuyente_id','anio','titulo','archivo','sha256','bytes','metadatos','created_at'))
    AND (SELECT COUNT(*) = 3 FROM information_schema.statistics WHERE table_schema = DATABASE()
    AND table_name = 'rut_documentos' AND index_name = 'rut_documento_huella')
    AND (SELECT COUNT(*) = 1 FROM information_schema.key_column_usage WHERE table_schema = DATABASE()
    AND table_name = 'rut_documentos' AND referenced_table_name = 'contribuyentes')
    AND (SELECT COUNT(*) = 1 FROM information_schema.table_constraints WHERE constraint_schema = DATABASE()
    AND table_name = 'rut_documentos' AND constraint_name = 'rut_documento_metadatos_json');
