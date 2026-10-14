# Alcance acordado: planeación tributaria anticipada

Actualizado el 14 de octubre de 2026. Este documento conserva los requisitos del
responsable para desarrollar por etapas sobre la aplicación MVC existente.
Es una especificación pendiente; no confirma que los cálculos estén implementados.

## Orden de trabajo

1. Contribuyente: identificación de persona natural, jurídica o sucesión ilíquida.
2. Perfil por año: residencia fiscal, régimen, obligaciones y formulario aplicable.
3. Explicación de cédulas y clasificación de fuentes de ingresos.
4. Patrimonio, deudas e información nacional y del exterior.
5. Ingresos detallados, devoluciones, rebajas, descuentos y depuración.
6. Costos, deducciones, rentas exentas y descuentos tributarios.
7. Revisión mensual de seguridad social y obligaciones relacionadas con UGPP.
8. Simulaciones, conciliación de resultados y plan de acciones con soportes.

Las fuentes del registro inicial son orientativas. Los movimientos y reglas deben
pertenecer a un año y, cuando corresponda, a un mes. Una persona puede combinar
salario, pensión, honorarios, arriendos, rendimientos y dividendos.

## Perfil fiscal y régimen

- Registrar año gravable y distinguirlo del año de presentación.
- Registrar residencia fiscal con evidencia, no inferirla de nacionalidad o domicilio.
- Para sucesiones, revisar la situación fiscal del causante según la regla aplicable.
- Registrar régimen ordinario, SIMPLE, especial u otra condición que corresponda,
  con soporte RUT, vigencia y estado pendiente de verificación.
- Registrar actividad CIIU, obligación de llevar contabilidad y responsabilidades.
- Definir el formulario aplicable por perfil: 210, 110 o 260, según corresponda.
- La pertenencia a un régimen no se presume elegible para los demás. Comparaciones
  de cambio de régimen deben revisar requisitos, exclusiones, plazos y efectos.
- Registrar o marcar pendiente cada dato indispensable. No calcular usando supuestos
  silenciosos ni aplicar el formulario 210 universalmente.

## Actividades económicas: requisito confirmado

Incorporar al perfil anual la actividad económica principal, código y denominación
oficial, y las actividades adicionales del RUT. Mantener fecha de vigencia y soporte.
El campo de referencia es la casilla 24 de los formularios de renta aplicables.

- Utilizar la clasificación oficial DIAN/CIIU vigente para el período, incluidos los
  códigos especiales de personas naturales; no limitar el catálogo a profesiones.
- Conservar el código como texto de cuatro dígitos para no perder ceros iniciales.
- Mostrar bajo la opción su definición, alcance, inclusiones/exclusiones y fuente.
- Ejemplos oficiales: 0010 asalariados, 0020 pensionados, 0090 rentistas de capital
  (según ámbito de aplicación). No asignar un código solo por marcar una fuente.
- Identificar y comparar los ingresos por actividad para revisar la principal del
  año conforme al instructivo. No reemplazar automáticamente la información del RUT.
- Distinguir actividad económica, fuente de ingreso, cédula y régimen. Un pensionado
  puede recibir otras rentas; su actividad principal no elimina esas fuentes.
- Relacionar cada movimiento con la actividad pertinente, conservando la justificación
  si no corresponde a una actividad económica ordinaria.
- Utilizar esa relación en revisiones futuras de SIMPLE, UGPP y costos, verificando
  las reglas específicas; el código por sí solo no acredita beneficios ni aportes.
- Permitir completar y corregir la actividad de perfiles ya registrados, con permisos,
  CSRF y trazabilidad. No obligar a duplicar el contribuyente o el perfil anual.

Referencia inicial: [Resolución DIAN 000227 de 2025](https://normograma.dian.gov.co/dian/compilacion/docs/resolucion_dian_0227_2025.htm)
y el instructivo del formulario aplicable. Validar versión e historial al cargar el
catálogo y registrar su migración/comprobación. Este requisito todavía no implementa
los campos ni el catálogo en la pantalla actual.

## Causantes fallecidos y sucesiones: requisito confirmado

Pendiente para la etapa de perfil fiscal. No crear un tipo independiente llamado
"fallecido": registrar esa condición y relacionar al causante con la sucesión.

- Datos previstos: nombre y NIT del causante, fecha de fallecimiento, residencia
  fiscal al fallecer y soporte de esa condición.
- Identificar al representante y su calidad según el RUT, con vigencia y soporte.
- Registrar estado de la sucesión, fecha y documento de adjudicación o terminación.
- Distinguir obligaciones pendientes del causante, obligaciones durante la sucesión
  y cierre; revisar los períodos y la eventual declaración por fracción de año.
- Mantener separado el patrimonio y los ingresos del causante, la sucesión y los
  herederos. Verificar titularidad, reconocimiento y tratamiento de cada operación.
- Determinar el formulario según la residencia del causante al fallecer y demás
  condiciones aplicables, conforme a FORMULARIOS-DIAN.md.
- Preparar una lista de soportes y obligaciones pendientes. No trasladar beneficios,
  ingresos o aportes automáticamente por el solo hecho del fallecimiento.
- Validar las normas vigentes y las fechas relevantes antes de habilitar cálculos.

## Campos alineados con la DIAN

Los bloques, nombres, orden, totales y reglas seguirán el formulario e instructivo
oficial correspondiente a la versión utilizada. Cada concepto tendrá explicación,
norma, vigencia, soportes y una relación verificable con la casilla de destino.
El contrato de documentación está en [DICCIONARIO-CAMPOS.md](DICCIONARIO-CAMPOS.md).
La comparación inicial de formularios está en [FORMULARIOS-DIAN.md](FORMULARIOS-DIAN.md).

El formulario muestra totales; el sistema debe guardar el detalle que los respalda.
No crear una sola columna por total como única fuente de información. Conservar
importe original, procedencia, fecha, soportes y cálculo hasta llegar a la casilla.

Crear una matriz de cobertura de TODAS las casillas de cada formulario soportado:
implementada, no aplicable con motivo, o pendiente. No afirmar cobertura completa
si hay conceptos sin revisar. Versionar por separado año gravable y formulario.

## Patrimonio nacional y del exterior

El inventario contemplará cuentas y depósitos, inversiones, inmuebles, vehículos,
participaciones, cuentas por cobrar y otros bienes o derechos. Cada activo tendrá
ubicación, titularidad, moneda, fechas, valoración fiscal y soporte. Las deudas se
registrarán por separado con acreedor, valor y evidencia.

Para cuentas en el exterior: país, entidad, referencia de cuenta, participación del
titular, moneda, saldo documentado y valoración fiscal en COP con trazabilidad de
la tasa y fecha utilizadas. No almacenar credenciales bancarias. No aplicar una
TRM de cierre universal a todos los activos: comprobar la norma de valoración.

La inclusión de bienes del exterior en patrimonio depende del alcance fiscal del
contribuyente (ET, art. 261). Además, evaluar la declaración de activos en el exterior
(art. 607, formulario 160): su corte al 1 de enero se distingue del corte de renta.
El umbral de obligación no significa que activos inferiores puedan omitirse de renta.

Los intereses y otros rendimientos de una cuenta son ingresos distintos del saldo
patrimonial. Identificar transferencias entre cuentas propias para evitar duplicar
patrimonio o tratarlas automáticamente como nuevos ingresos.

## Ingresos y beneficios

- Conservar detalle por fuente, período, país, moneda y tratamiento fiscal.
- Contemplar ingresos de trabajo, servicios, pensiones, capital, negocios,
  dividendos y eventos que puedan constituir ganancias ocasionales.
- Revisar ingresos en dinero y especie, causados o recibidos, según corresponda.
- Distinguir ingresos gravados, no constitutivos, exentos y operaciones que no sean
  ingreso; requerir la justificación correspondiente.
- Separar devoluciones, rebajas y descuentos comerciales de costos y descuentos
  tributarios. Evitar registrar dos veces una reducción.
- Registrar costos, deducciones, exenciones, compensaciones y descuentos con límites,
  incompatibilidades, relación con el ingreso, fecha y evidencia.
- Conservar retenciones, anticipos y saldos anteriores aparte del impuesto calculado.
- Mostrar importes reales, proyectados y pendientes sin confundirlos.

## UGPP y seguridad social

Construir una revisión mensual: tipo de vinculación o contrato, ingresos por
actividad, condición de pensionado y su clase de pensión, empleadores si aplica,
costos admisibles para aportes, IBC (ingreso base de cotización), salud, pensión,
riesgos laborales y demás componentes aplicables, novedades y pagos PILA.

La UGPP distingue prestación de servicios personales, otros contratos, cuenta
propia y rentistas de capital. Las reglas de aportes deben validarse por categoría
y período. No copiar la renta líquida anual como IBC ni usar una regla única para
todos los ingresos. Los costos fiscales y los admisibles para aportes requieren
validación separada; la presunción de costos de aportes no acredita costos en renta.

Comparar obligaciones estimadas con pagos y soportes mensuales; señalar diferencias,
meses sin información y posibles omisiones o inexactitudes. Revisar tratamiento
del pensionado con otros ingresos por subsistema, sin eximirlo automáticamente de
todos los aportes. No sustituir la revisión de contratos con una casilla del RUT.

Contemplar intereses y sanciones como alcance futuro, condicionado a norma vigente,
período, conducta y etapa del procedimiento. No fijar multas genéricas ni presentar
una alerta como sanción determinada por la autoridad.

## Simulación y aceptación

Cada escenario mostrará impuestos, aportes, saldo por pagar o a favor, desembolsos,
ahorro estimado y efecto en caja. Identificará acciones posibles antes del cierre,
soportes faltantes y beneficios que ya no generan ahorro por límites alcanzados.

Antes de habilitar un cálculo: revisar norma y versión, probar casos y límites en
aislamiento, verificar sus casillas y comprobar su resultado. Una declaración
anterior ayuda a conciliar, pero no demuestra la procedencia de cada tratamiento.
Los PDF reales no se incorporan al repositorio ni como datos de prueba.

## Fuentes oficiales de referencia

Consultadas el 13 de octubre de 2026; revisar nuevamente al implementar cada regla.

- [Estatuto Tributario: arts. 26, 27, 28, 261, 269, 288 y 607](https://normograma.dian.gov.co/dian/compilacion/docs/estatuto_tributario.htm).
- [DIAN: activos en el exterior](https://www.dian.gov.co/impuestos/sociedades/Paginas/declaracion_activos_exterior.aspx).
- [DIAN: formulario 210 e instructivo, versión publicada en 2026](https://www.dian.gov.co/atencionciudadano/formulariosinstructivos/Formularios/2026/Formulario_210_2026.pdf).
- [DIAN: régimen SIMPLE](https://micrositios.dian.gov.co/ruta-de-la-formalizacion/regimen-simple-de-tributacion/).
- [UGPP: trabajador independiente](https://www.ugpp.gov.co/trabajador-independiente/).
- [UGPP: Resolución 532 de 2024](https://www.ugpp.gov.co/normas/resolucion-532-de-2024/).
