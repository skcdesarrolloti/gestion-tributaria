# Control mensual de retención y reserva salarial

## Consulta sencilla en la nómina

En Ingresos → Trabajo, la misma fila mensual muestra OK (solo con revisión
vigente), diferencia en pesos o pendiente. Mientras falta confirmar el método,
se muestra la diferencia **provisional P1** con los supuestos ya autorizados.
La fila muestra retención descontada, calculada y diferencia de más/de menos.
No se repite una explicación debajo de cada mes.
Los pagadores se mantienen separados y los meses incompletos no se calculan.
Una sola consulta plegada ofrece la tabla DIAN del año en esa misma página.
El acumulado P1 sigue siendo provisional y compara exactamente los mismos meses.

La confirmación de soportes y la reserva anual quedan en «Opciones para el
contador y proyección anual», cerrado inicialmente. Se conservan sus rutas,
datos y permisos. La barra «Menú principal / Volver a ingresos» permanece visible
al desplazarse; conserva persona y año y no usa el historial del navegador.

Las secciones siguientes documentan las opciones de revisión del contador.

UVT, rangos y topes por año se administran en **Configuración** por un Socio.
Las cuentas P1 muestran el detalle de origen y la operación de cada tramo dentro
de **Ver cuenta provisional** o **Ver la cuenta paso a paso**. Ver
[parámetros anuales, firmas e historial](PARAMETROS-ANUALES.md).

Acceso: Registro de información → Ingresos → contribuyente y año → Cédula
general → Trabajo. El resumen mensual añade «Control retención», con OK,
Revisar o Pendiente de soporte. Las observaciones conservan el detalle.
«Revisar soportes de retención por mes y empleador» permite reabrir incluso OK.

Los enlaces de **Opciones para el contador** abren la revisión del mes elegido.
La ruta anterior de retenciones sigue abriendo el último mes completo disponible.
Su selector «Mes y empleador» permite cambiar de período sin regresar al listado.

## Captura guiada para quien no es contador

**Ver simulación provisional P1** muestra una hipótesis automática, sin guardar
supuestos ni alterar la revisión del contador. Usa pagos/aportes del empleador,
procedimiento 1, pagos ordinarios, redondeo al peso y sin dependientes, vivienda,
prepagada, AFC ni aportes voluntarios. Supone ausencia de otros pagos que consuman
cupos. Reutiliza RetencionEjemplo/RetencionLaboral y arrastra el beneficio aplicado
entre meses consecutivos desde enero. No reinicia ni divide topes anuales.
Retroactivos, meses incompletos o huecos excluyen ese mes y los posteriores.
El acumulado compara descuentos y simulación de exactamente los mismos meses;
los excluidos conservan visible su descuento real, fuera de ese subtotal.
No genera OK, casillas DIAN, pagos ni cambios de procedimiento. La tabla se
recalcula al consultar, a partir de los datos autorizados actuales. No estima
el impuesto anual ni confirma cuánto estaba obligado a retener el empleador.

En **Validar retención mensual** se ve la tabla del artículo 383 en UVT y en
pesos del año seleccionado. Comparte los rangos con el motor de cálculo.
Solo marca «Tu rango» con una revisión vigente de procedimiento 1; para el 2
muestra el porcentaje confirmado, sin aplicar otra vez la tabla al pago mensual.
La tabla explica base depurada, tarifa marginal y componente fijo.

**Comparación y acumulados al corte** lista enero hasta el último mes importado
del empleador elegido. Recupera descuentos reales y revisiones vigentes de cada
mes. No combina pagadores ni toma meses faltantes, parciales o sin soporte como
cero esperado. Si hay pendientes, el esperado total queda pendiente; cualquier
subtotal compara únicamente los mismos meses revisados. Incluye un texto para
pedir la liquidación al contador sin enviárselo automáticamente. No modifica
nómina, no descuenta importes ni presenta pagos a la DIAN. Tabla y fuente legal
contrastadas en DIAN el 28/10/2026 (ET 383/385/386 y resolución 238/2025).

La revisión comienza con datos recuperados del archivo y una explicación de
retención, ingreso bruto y aportes. Cada campo de revisión y reserva contiene
una explicación visible y, cuando corresponde, una ayuda desplegable con el
origen del dato, ejemplos y significado de cada opción. Hay un texto que puede
pedirse a nómina; el aplicativo no lo envía a terceros. Los artículos se siguen
consultando dentro de la aplicación. Una UVT se explica y se muestra en pesos
para el año elegido; no se pide al usuario calcular límites.

Procedimiento, dependientes y redondeo incluyen «No lo sé todavía». El servidor
rechaza su confirmación con un mensaje explicativo: no lo transforma en un
hecho ni en OK. Un campo monetario vacío sigue pendiente. Enero propone 0 en
acumulados de meses anteriores; desde febrero no se inventan acumulados.
El porcentaje solo se habilita para procedimiento 2, con ayuda y ejemplo de
formato. Sin JavaScript las explicaciones y el campo siguen disponibles.

«Resultado de la validación mensual» permanece visible: retención descontada,
esperada (o motivo del pendiente) y comparación. Tras guardar se llega a ese
bloque mediante un ancla; la cuenta paso a paso puede desplegarse. Cambiar
campos avisa que aún no se ha actualizado la comparación guardada.

Un ejemplo didáctico separado reutiliza el motor con supuestos expresos:
procedimiento 1, sin deducciones adicionales, cupos disponibles y redondeo al
peso. RetencionEjemplo devuelve únicamente importes, sin estado OK, y no guarda
ni modifica datos. No se presenta como validación de la nómina. Las ayudas
se contrastaron con ET 385, 386 y 387 en la compilación DIAN el 28/10/2026.

Se agrupan períodos del mismo empleador, sin compensar diferencias de pagadores
distintos. La cobertura de fechas debe llegar desde el día 1 hasta el cierre
de nómina (30 o último día de febrero), sin huecos; además se confirma que no
faltan otros pagos. Importar dos filas no demuestra integridad por sí solo.

## Alcance del cálculo mensual

Solo natural residente, ordinario, formulario 210, 2025/2026. Valores internos
en centavos. La UVT corresponde al año del perfil. Pagos ordinarios del mes
menos salud, pensión y solidaridad importadas. Vivienda: 100 UVT/mes;
prepagada: 16 UVT/mes; dependientes art. 387: 10% bruto, máximo 32 UVT/mes.
Exención 25% después de deducciones, con saldo anual de 790 UVT; conjunto
limitado al 40% y saldo anual de 1.340 UVT. Los saldos consumidos anteriores
requieren depuración certificada, no se inventan desde meses faltantes.

Procedimiento 1: tabla ET 383, umbrales 95/150/360/640/945/2300 UVT,
tarifas 19/28/33/35/37/39 y fijos 0/10/69/162/268/770 UVT. No usar tabla 241.
Procedimiento 2: aplica el porcentaje fijo certificado del semestre; NO
audita su determinación histórica (ET 386). La ayuda distingue este alcance.
Redondeo al peso o millar confirmado; tolerancia de comparación: $1.

Fuera de alcance: AFC/FPV, primas con tratamiento separado, indemnizaciones,
cesantías, pagos parciales y demás situaciones especiales. El responsable
debe confirmar su ausencia. Retroactivos detectados bloquean el OK.
No se reescribe lo retenido ni se afirma que una diferencia prueba un error
del empleador sin contrastar sus soportes. La tabla no aplica topes anuales /12.

Referencias consultadas el 28/10/2026:

- https://normograma.dian.gov.co/dian/compilacion/docs/estatuto_tributario.htm
  ET 206.10, 383, 385, 386, 387, 388, 241 y 807.
- https://normograma.dian.gov.co/dian/compilacion/docs/oficio_dian_3966_2023.htm
- https://normograma.dian.gov.co/dian/compilacion/docs/oficio_dian_11383_2024.htm
- Decreto 1625/2016, art. 1.2.4.1.6, modificado por Decreto 2231/2023.
- Resoluciones DIAN 193/2024 y 238/2025: UVT 2025 y 2026, catálogo existente.

## Reserva al corte

El botón «Ver reserva estimada al corte de nómina» utiliza exclusivamente
nómina importada, totales futuros expresos y saldos anteriores confirmados.
No extrapola quincenas ni toma meses vacíos como cero realizado. Reutiliza
RentaSimulador/RentaDeducciones/RentaTarifa para impuesto anual, retenciones,
anticipo y saldo; distribuye el faltante entre 1–12 cuotas de reserva.

Es una hipótesis salarial con los demás componentes en cero, NO la obligación
completa del contribuyente: pensión, rendimientos y demás ingresos/beneficios
deben incorporarse en el simulador anual ya existente. Muestra este pendiente
incluso con faltante $0. No incluye cesantías ni indemnizaciones. La mayor
retención voluntaria se solicita por escrito al pagador para el mes siguiente
(ET 383 par. 3); no se genera una orden de abono directo a DIAN.

## Persistencia y seguridad

Sin esquema nuevo: bloque JSON `retenciones_laborales` en caracterizaciones,
por contribuyente y año. `meses[mes-NIT]` guarda depuración, soportes, actor,
fecha y huella; `reserva` guarda hipótesis independiente. Revisión optimista
del bloque evita sobrescrituras. Una corrección del mismo pagador en un mes
anterior invalida el control posterior por afectar topes. Cualquier cambio en
nómina invalida la reserva. POST contrasta huella, CSRF y permisos actuales.
Originales y movimientos permanecen intactos. Usuario solo ficha asignada,
Socio todos. Nunca se guardan claves ni se modifican cuentas para estas pruebas.

Pruebas: tests/retenciones-unit.php y tests/retenciones-integration.php.

## Explicación al elegir una opción

En la revisión mensual y en la reserva, cada selector muestra su explicación
automáticamente en un recuadro justo debajo. Cambia al elegir otra opción, sin
abrir ayudas ni guardar el formulario. Al volver a abrir valores guardados, su
explicación aparece desde la carga inicial, también antes de ejecutar JavaScript.

