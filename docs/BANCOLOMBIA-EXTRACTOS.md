# Bancolombia Ahorros: control mensual desde PDF trimestral

La carga única de Ingresos reconoce Estado de Cuenta / Cuenta de Ahorros de
Bancolombia con columna Valor firmada y Saldo. Se validaron localmente dos PDF
de dos páginas entregados por el responsable. No se promete lectura universal.

DESDE puede indicar el saldo al cierre anterior: 31/03–30/06 cubre abril, mayo
y junio; 30/06–30/09 cubre julio, agosto y septiembre. Se conserva esa fecha
impresa como fecha_saldo_anterior y se normaliza inicio al primer día cubierto.
Solo admite meses completos del mismo año y hasta tres meses por PDF.

Reconstruye filas por coordenadas también cuando el texto se extrae por columnas,
acepta centavos con formato .20/.00, y exige que cada saldo, los abonos, cargos,
intereses y retenciones concilien exactamente con el resumen del banco.
ExtractoPeriodo comparte la conciliación mensual con Caja Social. BBVA conserva
su lector. Un fallo mantiene soporte pendiente, sin publicar importes parciales.

PAGO PENSION positivo se clasifica como neto bancario de pensión; ABONO INTERESES
AHORROS como rendimiento. Débitos 4X1000, incluido CXC IMPTO, van a GMF. Un abono
PAGO CXC DESDE CTA se deja en otros abonos: no se presume pensión, rendimiento
ni ingreso fiscal. Compras y transferencias no se convierten en deducciones.

Un registro y PDF conserva todas las operaciones. Las vistas mensuales existentes
reutilizan el mismo ID, con filtro por mes e índices originales. Se mantienen
permisos, descarga privada, CSRF y rechazo de archivos repetidos o períodos
solapados. No modifica esquema ni documentos históricos. No crea capturas fiscales
99/100 ni duplica los rendimientos en casillas; el bruto requiere soporte del pagador.

Pruebas ficticias: bancolombia-fixtures/unit/integration. Validan reconocimiento
HTTP, bordes del trimestre, importes por mes, centavos sin cero inicial, separación
de CXC y GMF, descuadres, detalle de ambas páginas, duplicados y solapamientos.
Los archivos reales solo se leen localmente; no se copian a Git ni a pruebas.
