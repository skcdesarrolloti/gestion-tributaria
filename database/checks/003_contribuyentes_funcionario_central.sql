SELECT
    (SELECT COUNT(*) = 1 FROM information_schema.columns
     WHERE table_schema = DATABASE() AND table_name = 'contribuyentes'
       AND column_name = 'funcionario_id' AND data_type = 'bigint'
       AND column_type LIKE '%unsigned%')
    AND (SELECT COUNT(*) = 0 FROM information_schema.key_column_usage
         WHERE table_schema = DATABASE() AND table_name = 'contribuyentes'
           AND column_name = 'funcionario_id' AND referenced_table_name IS NOT NULL);
