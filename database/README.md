# Migraciones automáticas

Base: **u350704768_tributaria**. No importar scripts manualmente ni entrar a phpMyAdmin.

En cada arranque, Migrator toma un bloqueo por base, lee schema_migrations y ejecuta los scripts pendientes en orden. Registra versión y SHA-256 tras comprobar el resultado. Si una migración ya aplicada cambia, la aplicación se detiene: hay que recuperar el archivo original y crear una versión nueva.

## Archivos

- 000_create_database.sql: plantilla para crear la base si no existe y el servidor permite CREATE DATABASE.
- schema_migrations.sql: crea el registro de versiones.
- migrations/001_funcionarios.sql: crea las cuentas con campos compatibles con el login comercial.
- checks/001_funcionarios.sql: confirma la presencia de los campos requeridos.

Cada migración contiene **una sentencia SQL**. Su archivo checks del mismo nombre contiene un SELECT que devuelve 1 si el cambio ya quedó aplicado y 0 si falta. Si hay un corte entre el DDL y el registro de versión, el siguiente arranque verifica el resultado y evita repetir un ALTER ya ejecutado. No usar procedimientos, DELIMITER ni varias sentencias por archivo.

## Ejemplo: columna nueva

Crea migrations/002_telefono_funcionario.sql:

```sql
ALTER TABLE wp_jet_cct_funcionarios ADD COLUMN telefono VARCHAR(30) NULL;
```

Crea checks/002_telefono_funcionario.sql:

```sql
SELECT COUNT(*) > 0 FROM information_schema.columns
WHERE table_schema = DATABASE()
  AND table_name = 'wp_jet_cct_funcionarios' AND column_name = 'telefono';
```

Sube ambos archivos con el código que usa la columna. Al abrir la app se ejecuta el cambio. Este ejemplo es solo documentación: la columna no se crea hasta agregar esos archivos.

## Reglas

- Número consecutivo con tres dígitos: 002, 003... Una operación por archivo.
- Nunca modificar una migración aplicada. El registro de versión lo inserta el motor, no el script.
- La comprobación debe verificar la postcondición real (tabla/columna/índice/tipo o dato), no solo devolver 1. Revisa ambos scripts en Git.
- Mantener cambios compatibles con la versión anterior durante el despliegue.
- Probar en una base aislada y respaldar antes de producción. No borrar datos como parte de la instalación.
- MySQL hace commits implícitos en DDL. Si falla una migración, el arranque se detiene; corrige el motivo y reintenta. La comprobación permite recuperarse de DDL confirmado sin registro.
- schema_migrations registra versiones, no reemplaza un respaldo.
- Esta instalación está pensada para bases nuevas. Una tabla preexistente incompatible necesita una migración de adaptación revisada.

El prefijo wp_ mantiene los nombres del login de referencia; no requiere WordPress. Las cuentas son locales e independientes en cada proyecto.
