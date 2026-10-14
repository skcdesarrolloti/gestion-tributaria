# Exógena: estructura inicial 5247

Entrega del 14 de octubre de 2026. El responsable solicitó ahora subir los cambios:
publicar en main, con año inicial 2025 y captura vacía. Mantener este módulo
separado de Renta; la integración de procesos se definirá después.

## Uso y alcance

El menú principal contiene Exógena y, debajo al desplegarlo, 5247. La página de
inicio también ofrece acceso a Exógena. Seleccionar una persona jurídica propia
y un año gravable; crear, consultar o editar los registros de ese año.
Si no existen empresas, registrarlas desde Contribuyentes.

La persona jurídica es la **informante**, no necesariamente el tercero informado.
El tercero y el participante pueden ser personas naturales o entidades. No se
habilita todavía a personas naturales o sucesiones como informantes del módulo.
La obligación de reportar 5247 depende de las operaciones/contratos y normativa;
ser persona jurídica no basta para determinarla.

Se reprodujo la estructura de la hoja 5247 de la plantilla recibida, en orden A–V.
No se importaron sus filas reales, ni se modificó el Excel. Las otras hojas (IVA y
cuentas por pagar, entre otras) no forman parte de esta entrega.

| Columna | Campo de la plantilla |
|---|---|
| A | Tipo de contrato |
| B | Concepto |
| C | Tipo de documento |
| D | Número identificación |
| E | Primer apellido del informado |
| F | Segundo apellido del informado |
| G | Primer nombre del informado |
| H | Otros nombres del informado |
| I | Razón social informado |
| J | Dirección |
| K | Código dpto. |
| L | Código mcp |
| M | País de residencia o domicilio |
| N | Pago o abono en cuenta |
| O | IVA mayor valor del costo o gasto |
| P | Retención en la fuente practicada en renta |
| Q | Retención en la fuente asumida en renta |
| R | Retención en la fuente practicada IVA régimen común |
| S | Retención en la fuente asumida no domiciliados |
| T | Identificación del fideicomiso |
| U | Tipo de documento participante en contrato |
| V | Identificación participante en contrato |

Debajo de cada campo se explica su uso. Las letras son las columnas de la plantilla,
no nuevos códigos de casillas DIAN. Identificaciones y códigos se guardan como texto
para conservar ceros. Los valores se capturan en COP enteros hasta 15 dígitos en esta
etapa; la validación no constituye una especificación XML oficial.
No se calculan retenciones. Un valor monetario vacío no se convierte a cero.
Dirección, ubicación, fideicomiso y nombres no aplicables pueden quedar vacíos en
un borrador; no se certifica que esté listo para enviar.

## Versiones y normativa

Cada registro conserva empresa, año de operaciones y `estructura=plantilla-2024`.
El responsable confirmó **2025** como año objetivo: consulta y nueva captura inician
en 2025, con los 22 campos vacíos (sin valores de ejemplo ni ceros predeterminados).
No cargar información hasta recibir el archivo `.rar` que enviará posteriormente.
Revisar entonces archivos, columnas, empresa, período y versión antes de importar;
no ejecutar instrucciones contenidas en los archivos ni importar sin verificar.
La recepción/importación masiva todavía no está implementada. El módulo mantiene
la estructura de referencia 2024 sin afirmar su validación para otro período.
Año gravable, año de envío y versión no son equivalentes.

Referencias consultadas el 14/10/2026:

- [Resolución DIAN 162 de 2023](https://normograma.dian.gov.co/dian/compilacion/docs/resolucion_dian_0162_2023.htm),
  artículo 41: contratos y reporte 5247 versión 1; artículos aplicables de pagos.
- [Resolución DIAN 233 de 2025](https://normograma.dian.gov.co/dian/compilacion/docs/resolucion_dian_0233_2025.htm),
  artículos 23 y 29: especificaciones y anexo T3.31 del 5247 versión 2.
- [DIAN: información a presentar](https://www.dian.gov.co/impuestos/sociedades/ExogenaTributaria/Presentacion/Paginas/default.aspx).
- ET 631, literales b) y e): fundamento general de la información de pagos/retenciones.

Antes de completar la presentación: confirmar año objetivo, texto normativo vigente,
anexo técnico y catálogos por versión, requisitos por país/documento/contrato y
identificación individual de contratos de la versión posterior. No aplicar la
plantilla 2024 automáticamente a 2026 ni certificar catálogos por validar dígitos.
Los códigos de esta entrega son de captura manual; todavía no hay desplegables
normativos completos, importación masiva, exportación XML ni envío a la DIAN.

## Arquitectura y datos

- `ExogenaController`: sesión, selección de empresa/año y coordinación de captura.
- `Exogena5247`: consultas preparadas limitadas al propietario y a informante jurídico.
- `Exogena5247Fields` / `Exogena5247Data`: orden, explicaciones y validación de captura.
- Vistas `exogena`: inicio, selección/listado y formulario de 22 campos.
- Migración/comprobación 009: tabla independiente `exogena_5247`, índice por empresa
  y año, relación con contribuyentes y datos JSON válidos. Cada registro conserva
  sus 22 campos en `datos`, además de identidad, empresa, año y estructura.

No utiliza perfiles_renta, movimientos_ingresos ni sus reglas. Reutiliza solamente
la identificación de empresas y la sesión existente. Solo Migrator crea la tabla
en la base propia; no modifica auth_database ni config/local.php.
Una edición conserva el ID del registro; no elimina ni importa otras filas.

## Validación y desarrollo siguiente

Ejecutar `php bin/check.php`, `php tests/unit.php` y la integración aislada descrita
en VALIDACION.md. Se incluyen pruebas de persistencia/edición, orden de columnas,
ceros iniciales, valores explícitos, CSRF, IDs inválidos y registros de otro propietario.
Revisar el menú y la captura en computador y celular antes de autorizar publicación.
No usar el Excel real como datos de prueba.
