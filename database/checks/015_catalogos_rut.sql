SELECT (SELECT COUNT(*) = 5 FROM information_schema.columns WHERE table_schema = DATABASE()
    AND table_name = 'catalogos_rut' AND column_name IN ('grupo','codigo','nombre','fuente','revisado'))
    AND (SELECT COUNT(*) = 2 FROM information_schema.statistics WHERE table_schema = DATABASE()
    AND table_name = 'catalogos_rut' AND index_name = 'PRIMARY');
