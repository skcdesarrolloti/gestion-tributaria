# Migraciones automáticas

Base: **u350704768_tributaria**. No importar scripts manualmente ni entrar a phpMyAdmin.

En cada arranque, Migrator toma un bloqueo por base, lee schema_migrations y ejecuta los scripts pendientes en orden. Registra versión y SHA-256 tras comprobar el resultado. Si una migración ya aplicada cambia, la aplicación se detiene: hay que recuperar el archivo original y crear una versión nueva.

## Archivos

- 000_create_database.sql: plantilla para crear la base si no existe y el servidor permite CREATE DATABASE.
- schema_migrations.sql: crea el registro de versiones.
- migrations/001_funcionarios.sql: migración histórica de la tabla local de cuentas. Se conserva intacta para respetar los checksums; esa tabla ya no se usa para autenticar.
- checks/001_funcionarios.sql: confirma la presencia de los campos requeridos.
- migrations/002_contribuyentes.sql: crea la identificación y fuentes de ingresos de contribuyentes por funcionario, sin borrar datos.
- checks/002_contribuyentes.sql: verifica sus columnas, el índice de documentos y la relación con funcionarios.
- migrations/003_contribuyentes_funcionario_central.sql: retira la relación con la tabla local histórica; funcionario_id guarda el ID central autenticado, sin copiar cuentas ni modificar la base central.
- checks/003_contribuyentes_funcionario_central.sql: comprueba la columna y la ausencia de esa relación local.

Cada migración contiene **una sentencia SQL**. Su archivo checks del mismo nombre contiene un SELECT que devuelve 1 si el cambio ya quedó aplicado y 0 si falta. Si hay un corte entre el DDL y el registro de versión, el siguiente arranque verifica el resultado y evita repetir un ALTER ya ejecutado. No usar procedimientos, DELIMITER ni varias sentencias por archivo.

## Ejemplo: columna nueva

El bloque inicial de ingresos incorpora migraciones y comprobaciones 004–007:
catálogo de casillas, carga inicial de conceptos, perfiles anuales y movimientos.
Los datos tributarios quedan en la base propia; no se escribe en auth_database.
La migración/comprobación 008 agrega No aplica al tratamiento de servicios,
separándolo de Pendiente. Los regímenes de consulta no se guardan como Ordinario.
Ver docs/INGRESOS.md para alcance y comprobación del flujo.

La migración/comprobación 009 incorpora `exogena_5247`, independiente de Renta,
con registros por empresa y año y los campos de la plantilla en JSON. Solo se ejecuta
en la base propia. Ver docs/EXOGENA-5247.md; no importa registros ni precarga datos reales.

La migración/comprobación 010 crea `exogena_1007`, con sus registros independientes
de `exogena_5247` y Renta. No carga datos de los auxiliares recibidos. Ver docs/EXOGENA-1007.md.

Migración/comprobación 011: `exogena_1001`, registros independientes AG2025 v10,
sin datos precargados. Ver docs/EXOGENA-1001.md; conservar intactas migraciones anteriores.

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

La migración/comprobación 013 crea escenarios_renta: entradas y resultado JSON,
versión de reglas, nombre, fecha y perfil anual. No copia importes de documentos
ni cambia los movimientos existentes. Cada guardado crea una versión; no hay
reemplazo ni eliminación de históricos. Solo afecta la base propia. Cualquier
reversión requiere respaldo y conservar estos escenarios; no borrar la tabla ni
modificar el script aplicado como mecanismo de reversión.

La migración 012 agrega exogena_registros sin precargar datos ni alterar 009–011.
Su comprobación revisa columnas, índice, clave foránea y validación JSON.
Ver [catálogo y alcance de Exógena](../docs/CATALOGO-EXOGENA.md).

- Número consecutivo con tres dígitos: 002, 003... Una operación por archivo.
- Nunca modificar una migración aplicada. El registro de versión lo inserta el motor, no el script.
- La comprobación debe verificar la postcondición real (tabla/columna/índice/tipo o dato), no solo devolver 1. Revisa ambos scripts en Git.
- Mantener cambios compatibles con la versión anterior durante el despliegue.
- Probar en una base aislada y respaldar antes de producción. No borrar datos como parte de la instalación.
- MySQL hace commits implícitos en DDL. Si falla una migración, el arranque se detiene; corrige el motivo y reintenta. La comprobación permite recuperarse de DDL confirmado sin registro.
- schema_migrations registra versiones, no reemplaza un respaldo.
- Esta instalación está pensada para bases nuevas. Una tabla preexistente incompatible necesita una migración de adaptación revisada.

Las migraciones solo se ejecutan sobre database. La autenticación consulta la tabla central wp_jet_cct_funcionarios mediante auth_database y no ejecuta DDL ni escrituras allí. No borrar la tabla local histórica ni modificar 001: el login ya la ignora. Ver docs/ACCESO-FUNCIONARIOS.md.

## Caracterización y catálogos RUT

014 crea `caracterizaciones_tributarias` por contribuyente/año/bloque, con JSON y
revisión para evitar pérdida de cambios entre ventanas. 015 crea `catalogos_rut`;
016 inserta 631 códigos de referencia oficial DIAN (508 actividades/clasificaciones,
49 responsabilidades y 74 aduaneros), sin datos personales. El INSERT es un archivo
generado de catálogo, no lógica de negocio. Todo se ejecuta en la base propia.
No editar estos scripts tras aplicarlos: actualizar catálogos mediante otra migración.
Conservar tablas y respaldar antes de cualquier reversión.
