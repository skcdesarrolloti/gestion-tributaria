SELECT (SELECT COUNT(*) = 16 FROM information_schema.columns
        WHERE table_schema = DATABASE() AND table_name = 'movimientos_ingresos'
          AND column_name IN ('id','perfil_id','concepto_id','fecha','estado','descripcion','tercero','pais',
                             'moneda','valor_original','conversion','valor_cop','soporte','justificacion','created_at','updated_at'))
   AND (SELECT COUNT(*) = 2 FROM information_schema.key_column_usage
        WHERE table_schema = DATABASE() AND table_name = 'movimientos_ingresos'
          AND referenced_table_name IN ('perfiles_renta','conceptos_ingresos'));
