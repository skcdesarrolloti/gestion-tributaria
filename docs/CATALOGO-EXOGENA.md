# Exógena por cliente · Preparación 2025

El menú y el panel comparten un catálogo de 14 formatos en seis grupos.
El panel presenta tarjetas compactas con código, nombre, grupo, registros y acceso:
tres formatos por fila en computador, dos en tablet y uno en celular. Los 14 formatos
comparten la misma cuadrícula para evitar columnas largas separadas por grupo.
Las ayudas completas permanecen dentro de cada formato.
Seleccionar una empresa propia en Exógena permite abrir sus formatos conservando
cliente y año. Los conteos indican registros guardados; no significan que el reporte
esté completo. Los informantes de esta etapa son personas jurídicas; los terceros
informados pueden ser personas naturales o jurídicas según el formato.

No se importan datos de los archivos recibidos. Los once formatos nuevos empiezan
sin registros y sus casillas de captura vacías, incluidos los valores monetarios.
Los registros anteriores de 1001, 1007 y 5247 se conservan.

## Menú y versiones de referencia

| Grupo | Formato | Nombre | Versión | Campos |
|---|---|---|---|---:|
| Pagos y retenciones | 1001 | Pagos o Abonos en Cuenta y Retenciones Practicadas | 10 | 20 |
| Pagos y retenciones | 1003 | Retenciones en la fuente que le practicaron | 7 | 14 |
| IVA | 1005 | IVA Descontable | 9 | 10 |
| IVA | 1006 | IVA Generado | 8 | 11 |
| Ingresos | 1007 | Ingresos Recibidos | 9 | 11 |
| Cuentas y socios | 1008 | Cuentas por Cobrar | 7 | 14 |
| Cuentas y socios | 1009 | Cuentas por Pagar | 7 | 14 |
| Cuentas y socios | 1010 | Información de Socios | 9 | 16 |
| Rentas de trabajo y pensiones | 2276 | Información de rentas de trabajo y pensiones | 4 | 45 |
| Contratos de colaboración | 5247 | Pagos o Abonos en cuenta | Plantilla recibida 2024 | 22 |
| Contratos de colaboración | 5249 | IVA descontable en contratos de colaboración empresarial | 2 | 14 |
| Contratos de colaboración | 5250 | IVA generado en contratos de colaboración empresarial | 2 | 15 |
| Contratos de colaboración | 5251 | Saldo de cuentas por cobrar en contratos de colaboración empresarial | 2 | 18 |
| Contratos de colaboración | 5252 | Saldo de cuentas por pagar en contratos de colaboración empresarial | 2 | 18 |

El 5247 conserva la plantilla entregada por el responsable, con su aviso de alcance;
no se convirtió silenciosamente a la versión 2. Antes de presentar, revisar esa
actualización y conciliar todos los formatos con el prevalidador del período.
El 1001 conserva versión 10 para 2025: la transición de 1001/1647 a 2026 está
indicada en el artículo 22, parágrafo 8, de la Resolución 233/2025.

## Fuentes y reglas específicas

Referencias oficiales revisadas el 14/10/2026:

- [Resolución DIAN 162/2023 y anexos](https://www.dian.gov.co/normatividad/Normatividad/Resoluci%C3%B3n%20000162%20de%2031-10-2023.pdf): estructuras de 1003, 1006, 1008, 1009, 1010 y 2276.
- [Resolución DIAN 233/2025 y anexos](https://www.dian.gov.co/normatividad/Normatividad/Resoluci%C3%B3n%20000233%20de%2030-10-2025.pdf): 1005 versión 9 y contratos 5249–5252 versión 2. Artículos 6 y 19.
- [Resolución 227/2025, compilación](https://normograma.dian.gov.co/dian/compilacion/docs/resolucion_dian_0227_2025.htm) y [Prevalidadores DIAN](https://www.dian.gov.co/impuestos/sociedades/ExogenaTributaria/Prevalidadores/Paginas/default.aspx).

Cada ficha muestra fundamento, versión, enlace al anexo y páginas del PDF.
Los diccionarios de resources/exogena conservan claves XML, orden, obligatoriedad,
longitud y ayuda por campo. No son archivos XML ni validadores completos DIAN.

- 1003: distingue base y valor de retención; no agrega un país ajeno al anexo.
- 1005 v9: tiene impuesto descontable e IVA de devoluciones; no conserva la columna
  eliminada de IVA como mayor valor del formato anterior.
- 1009: las etiquetas identifican al acreedor; el anexo repite «deudor» en algunas
  descripciones, pero el objeto del formato es el pasivo.
- 1010: porcentaje y número de posiciones decimales son campos separados. Para
  12,78915 %, registrar 1278915 y 5. Se verifica que no supere 100 %.
- 2276: 45 campos, agrupados sin cambiar su orden. Identificación del fideicomiso
  de hasta 14 dígitos; identificación de dependiente y participante opcionales en pares.
- 5249–5252: incluyen número de contrato y NIESPJ; NIESPJ obligatorio para tipos
  de contrato 3, 4 y 5. Los códigos siguen sujetos a los catálogos DIAN del período.

Valores en pesos enteros, sin separadores ni conversión a punto flotante. El cero
se registra expresamente. No se calculan automáticamente impuestos, retenciones,
porcentajes ni el total de pagos del 2276. No se certifican obligación de informar,
topes, conceptos, agrupación ni integridad tributaria del borrador.

## Persistencia, permisos y comprobación

La migración 012 y su comprobación crean automáticamente exogena_registros para
los once formatos nuevos, con empresa, formato, año y versión de estructura.
No cambian tablas ni datos anteriores. Las consultas verifican sesión, propietario
y tipo jurídico, y cada formato restringe sus IDs. Escrituras POST con CSRF;
todos los datos se escapan al mostrarlos.

Probar con php bin/check.php, php tests/unit.php y php tests/integration.php.
La integración crea bases temporales aisladas: recorre los once formatos, campos
vacíos, guardado, edición, permisos, CSRF y separación de clientes y formatos.
La inspección visual utiliza únicamente datos ficticios. La migración solo puede
retirarse tras respaldar y decidir cómo preservar sus borradores; no borrar tablas
como reversión automática ni modificar migraciones ya aplicadas.

## Preparación para la carga mensual posterior

No existe todavía importación, acumulación mensual ni exportación DIAN.
Al recibir los archivos, definir por cliente: período, origen, lote, cuentas,
conceptos, tercero, contrato, responsable y evidencia; controlar duplicados,
correcciones y conciliación con contabilidad antes de insertar registros.
Los movimientos pueden alimentar acumulados anuales; los saldos de 1008/1009
y 5251/5252 se toman por fecha de corte y no se suman entre meses. Los datos
de socios y trabajadores también requieren sus reglas de consolidación.
La captura actual conserva borradores anuales, no un historial de movimientos.
