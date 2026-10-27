SELECT COUNT(*)=1 FROM information_schema.columns WHERE table_schema=DATABASE() AND table_name='extractos_bancarios' AND column_name='lectura';
