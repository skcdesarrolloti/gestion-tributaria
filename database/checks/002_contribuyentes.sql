SELECT
    (SELECT COUNT(*) = 10 FROM information_schema.columns
     WHERE table_schema = DATABASE() AND table_name = 'contribuyentes'
       AND column_name IN ('id', 'funcionario_id', 'nombre', 'tipo', 'documento_tipo',
                           'documento_numero', 'dv', 'fuentes', 'created_at', 'updated_at'))
    AND (SELECT COUNT(*) = 3 FROM information_schema.statistics
         WHERE table_schema = DATABASE() AND table_name = 'contribuyentes'
           AND index_name = 'contribuyentes_documento_owner' AND non_unique = 0
           AND ((seq_in_index = 1 AND column_name = 'funcionario_id')
             OR (seq_in_index = 2 AND column_name = 'documento_tipo')
             OR (seq_in_index = 3 AND column_name = 'documento_numero')))
    AND (SELECT COUNT(*) = 1 FROM information_schema.key_column_usage
         WHERE table_schema = DATABASE() AND table_name = 'contribuyentes'
           AND column_name = 'funcionario_id' AND referenced_table_name = 'wp_jet_cct_funcionarios'
           AND referenced_column_name = '_ID');
