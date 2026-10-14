ALTER TABLE perfiles_renta MODIFY servicios ENUM('pendiente','costos','exencion','no_aplica') NOT NULL DEFAULT 'pendiente';
