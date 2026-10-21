# Instrumento de seguimiento y proyección anual

Requisito confirmado el 14 de octubre de 2026: vigilar el año en curso y comparar
acciones antes del cierre. La declaración anterior sirve para conciliar y aprender;
no es la finalidad del instrumento ni se copia como pronóstico del año actual.

## Disponible en esta entrega

### Hoja de planeación · actualización 21/10/2026

La ruta `simulador` agrega una hoja separada del resumen de movimientos descrito
abajo. Se abre desde el menú o Proyección anual, por perfil propio de persona
natural residente ordinaria. Usa UVT de 2025 o 2026 según el perfil. Los datos no se
importan de movimientos ni del PDF. Las tablas tienen desplazamiento horizontal
interno en celular; la impresión abre los detalles y conserva los datos capturados.

Revisión del PDF de una página entregado por el responsable: estructura trabajo,
pensiones, capital, no laborales, beneficios, impuesto, anticipo y patrimonio.
No contiene fórmulas de Excel: sus dependencias se reconstruyen desde normas y
no puede garantizarse equivalencia con el libro original. La suma visible de
deudas no coincide con su total y el impuesto visible requiere conciliar el tramo
del art. 241 con la base mostrada. No se incorporan datos reales al repositorio
ni se toma ese saldo como prueba de exactitud del nuevo motor.

Archivos: SimuladorFields define celdas y cobertura; RentaDeducciones aplica
límites comunes; RentaTarifa implementa los siete tramos; RentaSimulador coordina.
No se redondean UVT para elegir tramo. La exención de pensión se limita por mes
efectivamente registrado, después de salud. Salario y pensión no son opciones
excluyentes; otras fuentes conservan su tratamiento propio.

Los costos de las 17 familias se imputan a honorarios, capital o no laborales,
nunca automáticamente a salario/pensión/dividendos. Se impide usar a la vez costos
y exención de honorarios. INCR, costo fiscal y bases certificadas son entradas que
debe verificar el responsable; no se calcula procedencia desde un certificado.
Dependientes art. 387 se calculan solo con salarios mensuales en esta versión;
72 UVT adicionales requieren trabajo y máximo cuatro personas. Prepagada se
controla por mes; FPV/AFC comparten cupo y además límite general. Para el 25%,
las deducciones comunes se imputan primero a trabajo: revisar su imputabilidad
individual. Cesantías se separan y no generan otra exención del 25%.

Se incluyen dividendos nacionales ordinarios de utilidades 2017+, impuesto previo
del 35% cuando corresponda, agregado art. 241 y descuento art. 254-1. Donaciones
generales al 25%, limitadas por art. 258 y cupo. Anticipo por 25/50/75% sobre neto
actual o promedio con el anterior, menos retenciones. Se separan impuesto, saldo
y patrimonio líquido. No incluye sanciones ni reducciones particulares de anticipo.
El redondeo inicial es de planeación: base agregada e importes finales a miles;
no certifica reproducción de todos los redondeos por casilla del formulario 210.

Cobertura de las 52 fichas: costo por fuente, regla común, dato previamente
verificado, control o regla pendiente. Las reglas particulares tienen celda para
conservar importe; si es positivo bloquea saldo final. También bloquean ganancias
ocasionales, rentas extranjeras/ECE, pérdidas/compensaciones, regímenes antiguos o
especiales de dividendos y pensiones retroactivas/sustitutivas. No ignorar estos
casos ni guardar un saldo como si fueran cero. Una deducción sin soporte no queda
aprobada por marcar una confirmación. El mapa de casillas oficiales está pendiente
de cerrar por versión: esta hoja no es un formulario DIAN listo para presentar.

Los vacíos solo pasan a cero al confirmar expresamente que no son pendientes.
Se pueden guardar borradores incompletos con resultado pendiente. Migración 013
crea versiones inmutables por perfil. El servidor recalcula al guardar, ignora
resultados enviados por el cliente y verifica propietario en lecturas, cálculos,
guardados y comparación. Se conserva versión de reglas; no recalcular una versión
histórica distinta silenciosamente. Cambiar retenciones puede cambiar el saldo
sin cambiar el impuesto; la comparación muestra ambos efectos por separado.

La API POST usa CSRF; JavaScript solo envía entradas y presenta resultado del
servidor. Cancela respuestas antiguas cuando se modifica una celda, elimina saldos
anteriores en estados pendientes y permite recalcular explícitamente. Guardar es
una acción separada del cálculo. Referencias legales revisadas en esta entrega:
ET 55, 56, 107, 126-1, 126-4, 206, 241, 242, 254-1, 257–259, 330–337, 373 y 807;
UVT: resoluciones 193/2024 y 238/2025. El catálogo completo no equivale a cobertura
automática de todos los incentivos ni al contenido íntegro del Excel del contador.

Pruebas nuevas: tramos y umbrales de tarifa, pensión mixta/exceso por mes,
límites mensuales, FPV+AFC/global, RAIS, incompatibilidad costos/exención, datos
desconocidos, casos no cubiertos, saldo versus impuesto, persistencia de versiones,
CSRF y aislamiento. Ejecutar integración con bases temporales; nunca producción.

### Resumen de movimientos existente

Desde Contribuyentes → Ingresos por año → Proyección anual, cada perfil tiene un
tablero privado alimentado por sus movimientos ya guardados. También se accede
desde el detalle de casillas. No se duplican datos ni se crean nuevas tablas.

- Resumen real, esperado adicional y cierre parcial (suma de ambos).
- Ingresos brutos y partidas que se restan del catálogo vigente, con resultado
  preliminar; no sumar de nuevo las casillas calculadas.
- Resumen mensual de ingresos brutos y número de registros de partidas. Un mes
  sin registros queda identificado, y un mes con registros no se presume completo.
- Aviso de proyecciones cuya fecha ya pasó: editar la partida existente, comprobar
  su soporte y cambiar a real cuando corresponda; no registrar una copia.
- Estructura visible de los bloques pendientes hasta llegar al impuesto, caja,
  comparación de alternativas y acciones.

No hay impuesto calculado, beneficios automáticos, escenarios guardados ni alertas
enviadas. El tablero se actualiza al consultarlo. No hay extrapolación silenciosa.
El formulario 210 aún no captura pensiones, dividendos, ECE o ganancias ocasionales;
el 110 tampoco cubre todas las casillas. Su resumen siempre se identifica como parcial.
Un valor desconocido debe ser pendiente, nunca cero confirmado.

## Flujo objetivo para una persona que no es contadora

1. Confirmar el año que quiere vigilar y el perfil, con explicación y soporte.
2. Preguntar qué fuentes recibe y guiar su captura mensual con lenguaje sencillo.
3. Preguntar qué pagos y situaciones pueden tener tratamiento fiscal (salud,
   dependientes, aportes, gastos de actividad, facturas electrónicas, etc.).
4. Traducir las respuestas a conceptos/casillas y mostrar definición, alcance,
   norma, vigencia, soporte y revisión pendiente debajo de la opción escogida.
5. Mostrar el escenario actual y alternativas explicadas antes de guardar elecciones.
   La elección costos/exención de servicios requiere comparación y revisión;
   el usuario no debe decidirla a ciegas. Este asistente todavía no se implementa.

## Modelo de la proyección a desarrollar

| Bloque | Datos y resultado | Control necesario |
|---|---|---|
| Perfil anual | Tipo, residencia, régimen, actividades RUT, formulario y reglas | Confirmación por año; no copiar automáticamente el año anterior |
| Ingresos | Fuente, actividad, mes, real/adicional, origen y soporte | Completar todas las fuentes y evitar dobles registros |
| Depuración | INCR, devoluciones, costos, exenciones, deducciones y compensaciones | Elegibilidad, límites individuales/globales e incompatibilidades |
| Impuesto | Bases por tratamiento, tarifa, descuentos e impuesto neto | Normativa y UVT del año gravable; redondeo y casillas verificadas |
| Caja | Retenciones, anticipos anteriores, saldo a favor y anticipo siguiente | Impuesto distinto del saldo por pagar o a favor |
| Alternativas | Escenario base y escenario con acción, cambio y ahorro | Comparar mismos ingresos y supuestos, justificar cada diferencia |
| Acciones | Acción, responsable, fecha, documentos, estado y costo | Acción realizada a tiempo y requisitos cumplidos, sin ahorro garantizado |
| UGPP | Ingresos/aportes mensuales y PILA | Separar base de aportes y base fiscal; validar por actividad y vinculación |

Cada escenario deberá guardar fecha de corte, versión de reglas, hipótesis y origen
de sus importes. Separar realizado, proyectado y pendiente de confirmar. El horizonte
es el 31 de diciembre del año seleccionado (revisar fracciones de año en sucesiones).
Si cambia un dato, recalcular y conservar historial; no sobreescribir el pasado como
si hubiera sido conocido entonces. Estas funciones de escenarios son pendientes.

La comparación deberá mostrar ahorro de impuesto, desembolso de la acción, efectos
en aportes y liquidez. No recomendar gastar una suma superior al ahorro solo para
reducir renta. Un beneficio que alcanza su límite puede no producir ahorro adicional.
Más retención puede reducir el saldo a pagar, pero no reduce el impuesto por sí sola.

## Reglas antes de habilitar el cálculo

Construir y probar primero un alcance explícito del régimen ordinario de personas
naturales residentes, sin hacer que ese motor calcule empresas, no residentes,
SIMPLE o sucesiones con particularidades sin validar. Los perfiles existentes se
conservan; fuera del alcance se mostrará la razón de cálculo pendiente.

Confirmar casillas/versión y reglas de reconocimiento. Completar pensiones y demás
fuentes o confirmar su ausencia, depuración y beneficios, retenciones, anticipos y
descuentos. Tener patrimonio nacional/exterior y deudas para revisar obligaciones y
coherencia; la deuda no se deduce directamente del impuesto. No certificar una
declaración completa sin cubrir sus casillas. Probar topes, incompatibilidades,
casos negativos, tramos de tarifa, redondeos, titularidad y permisos con datos ficticios.

Referencias iniciales verificadas el 14/10/2026: [Estatuto Tributario](https://normograma.dian.gov.co/dian/compilacion/docs/estatuto_tributario.htm),
arts. 26–28, 107, 126-1, 126-4, 206, 240–241, 330–341, 387 y 807–810.
La [Resolución DIAN 238 de 2025](https://normograma.dian.gov.co/dian/compilacion/docs/resolucion_dian_0238_2025.htm)
fija UVT 2026 en $52.374; no utilizar la UVT 2025 para planear 2026.
Estas referencias no habilitan cálculos ni reemplazan la revisión particular.

## Comprobación

La primera sección de la **Hoja de planeación** ofrece nombres de escenario
desplegables, conservando también cualquier nombre de una versión anterior. Las
ayudas «?» explican pensión, honorarios, historia de declaraciones y base del
anticipo. Se acceden con ratón, toque o teclado, sin depender de JavaScript.
El nombre es una etiqueta: no cambia ingresos ni aplica automáticamente una acción.
La opción vacía se muestra como «No lo sé / por confirmar» y mantiene pendiente el
resultado. No se sustituyen respuestas desconocidas por ceros ni se selecciona un
porcentaje de anticipo por defecto. El campo libre opcional indica documentos o
datos pendientes; no modifica importes. Revisar ET 807 para los dos métodos y los
porcentajes; ayuda contrastada el 21/10/2026 con el Normograma DIAN.

Verificar primera/segunda/tercera declaración, guardar y reabrir un escenario con
nombre nuevo y uno histórico, comprobar que las ayudas no envían el formulario y
que un dato por confirmar sigue bloqueando el saldo final. Estas mejoras de captura
no cambian las fórmulas ni la cobertura del motor.

Abrir un perfil propio → Proyección anual → registrar una partida real o proyectada
→ volver al tablero y comprobar mes e importe. Editar una proyección para convertirla
en real: el cierre permanece igual si el importe no cambia. Intentar abrir un perfil
de otro funcionario debe devolver 404; sin sesión debe ir al login. Las pruebas
unitarias verifican agregación, exclusión de totales derivados y avisos de revisión.
