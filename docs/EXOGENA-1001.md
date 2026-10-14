# 1001 · Pagos o Abonos en Cuenta y Retenciones Practicadas

Para el año gravable 2025 acordado, estructura inicial vacía de versión 10.
Menú: Exógena → 1001. Seleccionar empresa jurídica propia y año 2025; crear,
consultar o editar borradores. Los 20 campos inician vacíos, incluidos país e importes.
No se cargan datos de archivos anteriores ni se mezclan con 5247 o 1007.

## Fuentes y período

Revisadas el 14/10/2026:

- [Resolución DIAN 162 de 2023, anexo 18](https://www.dian.gov.co/normatividad/Normatividad/Resoluci%C3%B3n%20000162%20de%2031-10-2023.pdf),
  páginas PDF 240–247: estructura del formato 1001 versión 10 y XSD. La tabla del
  contenido está en páginas 241–242 y contiene veinte campos.
- [Artículo 20 de la Resolución 162 de 2023](https://normograma.dian.gov.co/dian/compilacion/docs/resolucion_dian_0162_2023.htm),
  con modificaciones para 2025, incluida [Resolución 188 de 2024](https://normograma.dian.gov.co/dian/compilacion/docs/resolucion_dian_0188_2024.htm).
- [Resolución 227 de 2025](https://normograma.dian.gov.co/dian/compilacion/docs/resolucion_dian_0227_2025.htm),
  artículo 1.3.5.2.1: compilación de pagos, con historial de modificaciones.
- [Resolución 233 de 2025](https://normograma.dian.gov.co/dian/compilacion/docs/resolucion_dian_0233_2025.htm),
  artículos 22 (parágrafo 8), 23 y 29: cambios de pagos para 2026 y siguientes y
  actualización del anexo T3.18 a versión 11. No tomar el texto consolidado más
  reciente como si todos sus ajustes aplicaran a 2025.
- [DIAN: información a presentar](https://www.dian.gov.co/impuestos/sociedades/ExogenaTributaria/Presentacion/Paginas/default.aspx).

Fundamento general: ET 631 literales b) y e) y 631-2. Este módulo solo admite
consulta/guardado AG2025; versión 11 u otro año requieren desarrollo y verificación
separados. Año de envío y año gravable no son equivalentes.

## Campos del contenido v10

| Orden de pantalla | Campo |
|---|---|
| A | Concepto |
| B | Tipo de documento |
| C | Número identificación |
| D | Primer apellido del informado |
| E | Segundo apellido del informado |
| F | Primer nombre del informado |
| G | Otros nombres del informado |
| H | Razón social informado |
| I | Dirección |
| J | Código departamento |
| K | Código municipio |
| L | País de residencia o domicilio |
| M | Pago o abono en cuenta deducible |
| N | Pago o abono en cuenta NO deducible |
| O | IVA mayor valor del costo o gasto deducible |
| P | IVA mayor valor del costo o gasto no deducible |
| Q | Retención en la fuente practicada Renta |
| R | Retención en la fuente asumida Renta |
| S | Retención en la fuente practicada IVA a responsables del IVA |
| T | Retención en la fuente practicada IVA a no residentes o no domiciliados |

Los pagos en especie (por ejemplo concepto 5092) son un concepto del reporte;
no crear una columna adicional que no figure en el anexo v10. No llamar asumida
a la retención de IVA del campo T: el anexo dice practicada. El GMF de 2025 debe
revisarse en sus columnas deducible/no deducible y con el agente del gravamen,
según las reglas aplicables; no aplicar sin revisión nuevos conceptos de 2026.

## Alcance de validación y desarrollo

Cada campo tiene explicación. La captura valida longitud, sintaxis, identificación,
importes y país explícito; para Colombia exige dirección y códigos departamental
y municipal con ceros iniciales. Los ocho importes aceptan COP enteros hasta 18
dígitos y cero expreso. Listado de pagos conserva los 18 dígitos al mostrar valores.
No convertir desconocidos en cero ni importes a float para almacenarlos.

Los códigos se capturan manualmente: pertenencia al catálogo, obligación de reportar,
deducibilidad, reconocimiento, cuantías menores, exclusiones de otros formatos y
reglas de agrupación requieren revisión. Los borradores pueden ser varias partidas;
antes de exportar se deberá conciliar y agrupar para respetar la llave oficial
concepto/tipo de documento/identificación por año. No hay XML ni envío automático.

Migración/check 011 crea tabla independiente `exogena_1001`, sin cambiar scripts
ya aplicados. Reutiliza ExogenaRecords y ExogenaController; Exogena1001Fields/Data
conservan las reglas de este período. ExogenaData comparte la validación básica
con 1007. No altera la base central ni config/local.php.

Comprobar sintaxis, unitarias e integración aislada: sesión, CSRF, empresa/propietario,
creación, edición sin duplicar, valores de 18 dígitos, ubicación Colombia/exterior,
rechazo de 2026 y conservación de 1007/5247. Revisar presentación a 1280px/360px
y comprobar despliegue en Hostinger antes de afirmar funcionamiento en producción.
