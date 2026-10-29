# Salarios, pensiones y decisiones antes del cierre

Análisis y acciones conserva persona/año desde el menú y muestra un resumen
provisional, explicado dentro de la misma página. No crea escenarios guardados
ni altera movimientos al comparar un aporte. Solo PN residentes ordinarias.

## Entradas y alcance

- Los movimientos 32 y 33 alimentan salarios y aportes una sola vez; no se
  vuelve a sumar el bruto de la nómina. Esta aporta únicamente las retenciones.
- Pensión 99 y aportes 100 se agrupan por mes. La exención simulada supone
  pensión reconocida conforme a Ley 100; todas las pensiones de un mes comparten
  el límite 1.000 UVT. Pensión del exterior o retroactivos bloquean este resumen.
- Nunca se convierte neto bancario en bruto. Se indica si faltan meses de bruto
  y si se ha usado cero de salud sin confirmación. No se afirma un total integral.
- Se reutiliza RentaSimulador: exención salarial, límites y tarifa anual 241;
  UVT del año desde ParametrosAnuales. Se muestran pasos, fórmula y artículos
  desplegables. La tabla mensual de retención no sustituye la tarifa anual.
- No se estima automáticamente el resto del año. Si hay movimientos proyectados
  de salario/pensión/aportes, aparece un escenario adicional real + proyectado.
- «Por cubrir» = máximo de impuesto del escenario menos retención de nómina y
  cero. No es saldo de declaración: no incluye otros ingresos, beneficios ya
  utilizados, anticipos, saldos a favor ni retenciones de otros orígenes.

Los rendimientos bancarios son un control de Capital. No se suman junto con la
casilla 58 sin conciliación, pues puede incluirlos: el resumen avisa que quedan
fuera. El escenario completo existente conserva sus entradas independientes.

## Comparaciones opcionales, sin escrituras

El GMF leído, neto de reintegros, sirve para una comparación con el 50%
deducible, condicionada a certificado y procedencia. No se aplica en el impuesto
base ni se registra como costo de arrendamientos.

Un POST con CSRF permite comparar un único aporte hipotético FPV/AFC en la misma
página. Aplica 30% sobre el salario del escenario (base conservadora), 3.800 UVT,
límite conjunto y recálculo del 25%. Supone cero aportes previos: no promete cupo
real ni ordena operaciones. Estar pensionado no habilita retiro inmediato con
beneficio. Deben confirmarse producto, permanencia/retiro y situación completa.
Las reducciones de GMF y FPV/AFC se comparan por separado; no se suman como si
fueran independientes. No se recomienda gastar para crear deducciones.

Fuentes oficiales revisadas el 29/10/2026: Estatuto Tributario DIAN, artículos
115, 126-1, 126-4, 206.5/10, 241, 336, 337 y 387; instructivo DIAN 210 publicado
en 2026, casillas 99 y 100; Concepto DIAN 6606/2024 (límite mensual pensional) y
Concepto 912/2018, descriptor de aportes voluntarios por pensionados.

Pruebas: renta-acumulada-unit.php y bancos-renta-integration.php cubren origen,
redondeo, pensiones simultáneas, desconocidos, hipótesis y separación bancaria.
