# Alcance acordado: planeación tributaria anticipada

Actualizado el 14 de octubre de 2026. Este documento conserva los requisitos del
responsable para desarrollar por etapas sobre la aplicación MVC existente.
Es una especificación pendiente; no confirma que los cálculos estén implementados.

## Orden de trabajo

Prioridad del 21/10/2026: socios como personas naturales y miembros de la familia.
La ruta protegida `beneficios` incorpora un catálogo de consulta sin datos personales:
17 familias de costos/gastos, 8 deducciones, 6 rentas exentas, 4 conceptos no
constitutivos de renta, 6 descuentos, 5 incentivos particulares y 6 controles.
Fuentes: ET consolidado DIAN; leyes 361/1997, 814/2003 y 1715/2014; UVT 2025
según Resolución 193/2024 y UVT 2026 según Resolución 238/2025. Consulta: 21/10/2026.
Las fichas están en resources/beneficios, agrupadas por naturaleza; el servicio
BeneficioCatalog arma su listado y filtros sin admitir rutas de archivos del usuario.

No se aplica una deducción por ser socio. Se distinguen origen del ingreso,
residencia, régimen, sujeto que soporta el gasto y contabilidad; gastos de la
sociedad y su pérdida no se trasladan al socio. La cédula de dividendos no admite
costos/deducciones, y la pensión tiene su depuración propia. El catálogo señala
las incompatibilidades, topes individuales y conjunto, sin atribuir cupos personales.

La etapa actual no implementa captura de egresos, cálculos, carga de PDF ni
simulaciones de beneficios. El PDF del contador se contrastará ficha por ficha:
coincide, falta concepto, cambia por año, requiere soporte o interpretación.
Conservar identificadores al modificar fichas y fechar futuras revisiones legales.
No afirmar exhaustividad de incentivos sectoriales ni aplicar este catálogo a
SIMPLE, sociedades o no residentes. El porcentaje anual del componente inflacionario
no está parametrizado; se mantiene pendiente de verificar para el año aplicable.
Destino inicial por categoría, sin inventar números de casilla: confirmar cada
casilla con el formulario 210 e instructivo del período antes de habilitar cálculos.

La prioridad confirmada es el seguimiento del año en curso y la proyección antes
del cierre. Ver [PROYECCION-ANUAL.md](PROYECCION-ANUAL.md): tablero inicial disponible
y contrato de escenarios, caja y acciones pendientes. El instrumento debe guiar
al usuario sin exigir conocimientos contables para escoger tratamientos.

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

## Revisión del aplicativo de seguridad social recibido · 21/10/2026

Archivo: `calculadora-seguridad-social-rentas-mixtas_para aportes de Independientes.html`.
SHA-256: `7096F0A0CBC5F3A2AD18DD02EC8F8477CBA8E340F236D8D9298996FCC26CC1F2`.
Revisión inicial de código y ejecución local en Edge. El original permanece intacto;
no se copia a public, no se ejecuta desde la aplicación ni se importan sus valores.

Hallazgos reproducidos con valores ficticios:

- Todos los ingresos en cero generan un IBC mínimo de $1.750.905 y aportes mostrados
  de $499.008. Falta evaluar primero la obligación, separándola del piso aplicable.
- IBC de exactamente 4 SMLMV: el archivo calcula FSP 0% porque usa `mult <= 4`.
  Ley 100, arts. 20 y 27, incluye el límite igual a cuatro. Revisar también 16/17/18/19.
- No hay mes/año. El esquema nuevo no puede trasladarse indiscriminadamente a 2025
  ni a todos los meses de 2026. La UGPP informa vigencia desde el 09/05/2026.
- Los valores iniciales de actividad para arriendos e intangibles no validan la
  clasificación. No aprobar automáticamente todo el catálogo de coeficientes.
- El consolidado salarial incluye la parte patronal, como advierte el propio HTML.
  No equivale a lo pendiente por pagar por el independiente. Falta conciliar nómina,
  tope concurrente, tipo de pensión, novedades, ARL cuando corresponda y redondeos PILA.

Fuentes revisadas:

- [UGPP: obligación de rentistas y mínimo](https://www.ugpp.gov.co/abc_rentistas_capital/).
  Su explicación del esquema de costos antiguo debe distinguirse del período nuevo.
- [UGPP: Decreto 379/2026 y vigencia](https://www.ugpp.gov.co/enterate-con-la-ugpp/).
- [Ley 100, artículos 18, 20 y 27](https://cancilleria.gov.co/normograma/compilacion/docs/ley_0100_1993.htm).
- [Calculadora orientadora oficial](https://www.ugpp.gov.co/calculadora-IBC/).

Decisión: no habilitar todavía el cálculo recibido. En Planeación → Seguridad
social se publica únicamente la revisión protegida por sesión. Reutilizar después
su separación de fuentes y captura por filas, corrigiendo lo anterior y contrastando
casos normales, cero, por debajo del mínimo, límites exactos, nómina concurrente,
pensión y cambio de período. La revisión inicial no certifica las demás fórmulas.
Los costos presuntos de aportes no pasan automáticamente a deducciones de renta.

## Navegación por categorías

Planeación → Beneficios por categoría ofrece siete grupos; la portada muestra solo
las categorías. Cada categoría/búsqueda limita las fichas a nueve por página y conserva
el año y los filtros. La academia, límites, soportes y norma se abren dentro de cada
ficha. Las 52 fichas siguen disponibles; no se agregan 52 enlaces a la cabecera.
La hoja conserva sus siete bloques plegables de captura para no perder valores al
cambiar de categoría. No se modifican fórmulas ni escenarios existentes.
