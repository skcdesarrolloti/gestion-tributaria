# Nómina laboral por año

Registro de información → Ingresos → Trabajo → **Subir nómina**, o acceso directo
desde el mismo menú. Seleccionar 2025/2026, archivos XLS/XLSX, NIT del pagador,
hojas y empleados. Revisar antes de confirmar. Enero se preselecciona para la
revisión inicial solicitada; no se cambia el período que trae cada hoja.
Seleccionar un solo empleador por carga para confirmar un NIT de pagador.

## Adaptador y privacidad

El listado compartido incluye varias hojas históricas, no solo el período del
nombre del archivo. El lector reconoce el encabezado quincenal y columnas por
nombre, incluso cuando se desplazan y hay dos columnas de retroactivos. Básico
es referencia, no pago. Sobresueldo conserva su nombre, sin presumir comisión.
Hojas de primas, seguridad social u otro diseño no se importan; tampoco otro año.
Solo identificación exacta contra contribuyentes autorizados; no aproximar nombres.

SheetJS CE 0.20.3, Apache-2.0, se sirve desde assets/vendor/sheetjs con licencia.
Distribución oficial generada, sin modificar. No usa CDN durante la carga ni
ejecuta macros/fórmulas: lee los valores guardados. SHA-256 de la distribución:
cc015130aa8521e7f088f88898eba949ccdcbfb38df0bd129b44b7273c3a6f41.
Fuente: https://docs.sheetjs.com/docs/getting-started/installation/standalone/ .
Hasta seis libros de 4 MB, 80 hojas, 2000 filas y 80 columnas por hoja; 150 filas
seleccionadas por confirmación. Los XLS antiguos se leen en el navegador.

Solo las filas elegidas y autorizadas llegan al servidor. El libro completo y
empleados ajenos no se conservan. Se guardan referencia de archivo/hoja/huella,
identificación, empleador, períodos e importes del titular. No hay conexiones a
funcionarios, cuentas externas ni archivos reales versionados en Git.

## Cálculos y procedencia

Importes originales en centavos enteros. Se verifica suma de componentes,
descuentos y neto con tolerancia de $1. No se presupone pago bancario. El neto
calculado se compara con el neto del listado. No se recalculan tasas de nómina.

- 32: devengado de nómina; se suma una sola vez, no junto con su propio detalle.
- 33: salud, pensión y solidaridad del trabajador asociadas a estos salarios.
- 34: movimientos 32 menos 33, antes de exenciones y deducciones.
- 132: retenciones laborales, visibles en la tabla mensual; no se descuentan de
  ingresos ni se crean como una reducción de renta en el catálogo de ingresos.
- Otros descuentos: control del neto, sin beneficio fiscal automático.

La fuente detallada mantiene centavos; movimientos 32/33 a pesos por quincena
para mantener el contrato del módulo existente. El redondeo final a miles y el
impuesto corresponden a una fase posterior. 2026 usa la estructura del 210 de
referencia, pendiente de contrastar con la versión aplicable. La captura no
certifica la procedencia de cada beneficio ni completa una declaración.
Fuente DIAN: Formulario 210 e instructivo, 2025 (AG2023 y siguientes), casillas
32, 33, 34 y 132; ET 55/56/103. No se copian tasas ni cifras privadas del contador.

## Integridad y correcciones

Migraciones 024/025 y comprobaciones automáticas: fuentes de nómina e historial.
Clave única perfil/pagador/inicio/fin, huella del contenido y revisión optimista.
Una repetición idéntica no suma; una corrección confirmada actualiza ambos
movimientos y archiva la versión previa dentro de la misma transacción. Los
períodos superpuestos se rechazan. Lote completo atómico, sin guardados parciales.
Vista previa en sesión, con token de un uso, vencimiento de 20 minutos, CSRF,
cuenta/año y permisos comprobados nuevamente al confirmar.

Una captura manual laboral del mismo mes exige revisión antes de importar;
después de importar se bloquea la captura real manual de 32/33 en ese mes para
evitar sumas duplicadas. Las correcciones se hacen desde el archivo de origen.
No borrar automáticamente movimientos existentes. Para casos mixtos manuales
y nómina habrá que definir una conciliación por pagador antes de habilitarlos.

Reversión: conservar tablas, historial, catálogos y movimientos. No deshacer
024/025 ni borrar datos para retirar la interfaz. Conservar config/local.php.

Pruebas: sintaxis PHP, unitarias PHP, tests/nomina-parser.cjs, integración aislada
y navegador con fixtures ficticios. La consulta autenticada de producción debe
comprobarse después del despliegue; no importar las nóminas reales en pruebas.
