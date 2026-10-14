# Primer bloque de ingresos: diseño de casillas y respaldo

Fecha de revisión inicial: 14 de octubre de 2026. Diseño solicitado por el
responsable; las fichas, movimientos y cálculos descritos todavía no están
implementados. Mantener separados el diseño, la revisión normativa y la ejecución.

## Cómo verá el usuario cada concepto

Mostrar número, nombre oficial y bloque. Al abrir la ficha: explicación sencilla,
qué incluye y excluye, ejemplo, norma enlazada, condiciones, soportes, origen del
importe y efecto en la depuración. Los totales calculados no se editarán como
movimientos independientes.

El identificador completo incluye formulario, versión y número; por ejemplo,
`210/version-verificada/58`. El año gravable se registra aparte de la edición del
formulario. El mismo número no identifica el mismo concepto en formularios distintos.

La revisión del perfil por año es previa a guardar importes para una declaración.
La clasificación orientativa por fuente no basta para seleccionar un formulario.

## Una casilla puede contener varios conceptos y varias normas

Separar el fundamento en tres capas:

1. Clasificación del concepto y pertenencia al bloque o cédula.
2. Tratamiento: reconocimiento, exclusión, costos, beneficio y condiciones.
3. Instructivo: agrupación, casilla de destino y operación aritmética.

No forzar un único artículo por casilla. Asociar cada detalle con la norma que
justifica su tratamiento. Una norma general no autoriza cualquier exclusión.
La ficha permite otras normas, reglamentación, doctrina y jurisprudencia cuando
correspondan. Su vigencia y aplicabilidad deben revisarse antes de activar reglas.

## Mapa inicial del recorte: formulario 210

Las referencias siguientes orientan la revisión; no constituyen reglas suficientes
para todos los casos. Confirmar condiciones del concepto concreto y año gravable.

| Casilla | Concepto resumido | Fundamento inicial a revisar |
|---|---|---|
| 32 | Trabajo: importe bruto | ET 103 y 335; reconocimiento 27/28 e instructivo. |
| 33 | Trabajo: partidas no constitutivas | ET 336; norma particular de cada partida. |
| 34 | Resultado del bloque de trabajo | ET 336 e instructivo: 32 menos 33. |
| 43 | Servicios personales con opción de costos | ET 103, 206.10 y 336; condición del instructivo. |
| 44 | Partidas no constitutivas del bloque anterior | ET 336 y norma de cada partida. |
| 45 | Costos procedentes del bloque anterior | ET 107 y 336; requisitos documentales aplicables. |
| 46 | Resultado del bloque anterior | ET 336; operación y tratamiento del instructivo. |
| 58 | Capital: importe bruto | ET 335.2; reconocimiento 27/28 e instructivo. |
| 59 | Capital: partidas no constitutivas | ET 336 y norma particular de cada partida. |
| 60 | Capital: costos procedentes | ET 107 y 336; requisitos documentales aplicables. |
| 61 | Resultado del bloque de capital | ET 336 e instructivo: resultado positivo de 58 menos 59 menos 60. |
| 62 | Capital: rentas pasivas ECE | Libro VII ET, incluido art. 884; verificar sujeción. |
| 74 | No laborales: importe bruto | ET 335.3; reconocimiento 27/28 e instructivo. |
| 75 | Reducciones comerciales del bloque no laboral | ET 26 e instructivo; operación y soporte originales. |
| 76 | No laborales: partidas no constitutivas | ET 336 y norma particular de cada partida. |
| 77 | No laborales: costos procedentes | ET 107 y 336; requisitos documentales aplicables. |
| 78 | Resultado del bloque no laboral | ET 336; operación y tratamiento del instructivo. |
| 79 | No laborales: rentas pasivas ECE | Libro VII ET, incluido art. 884; verificar sujeción. |

La casilla 43 no es una ubicación automática de todos los honorarios: el instructivo
la vincula a la elección de costos y deducciones frente a la exención del art. 206.10.
La fila de devoluciones del recorte tiene casilla 75 en el bloque no laboral;
no inventar casillas equivalentes donde el formato no las contiene.

## Captura de detalle y resultado

Registrar cada movimiento con período, concepto, tercero, país, moneda, importe,
soporte, estado real/proyectado y revisión. Guardar la norma particular cuando
su tratamiento requiera justificación. Incorporar trazabilidad hasta su total.

Ejemplo ilustrativo: arriendos e intereses son movimientos diferentes que pueden
alimentar una misma casilla de capital. Si se registra una reducción admisible,
conservar su concepto, norma y soporte; no registrar un único "valor no gravado"
sin explicar cómo se obtuvo. No confundir saldo de una cuenta con su rendimiento.

La pantalla mostrará ingreso bruto, partidas que se restan y resultado. Explicará
el efecto antes de aplicar tarifa, retenciones y anticipos. No llamará impuesto
al resultado de esa primera depuración.

El detalle tendrá período mensual para poder revisarlo posteriormente frente a
UGPP. La clasificación y la base de aportes tendrán reglas separadas.

## Forma de desarrollar

1. Confirmar perfil fiscal anual y formulario/versiones.
2. Preparar las fichas de los conceptos de ingresos y sus partidas relacionadas.
3. Implementar captura de detalle, soportes y agregación a casillas.
4. Habilitar reglas únicamente después de validarlas con casos y límites.
5. Continuar con los demás bloques y el formulario 110 sin reutilizar números como
   identidades universales. No declarar un formulario completo a partir de este mapa.

Fuentes de la revisión inicial:

- [DIAN: formulario 210 e instructivo publicado en 2026](https://www.dian.gov.co/atencionciudadano/formulariosinstructivos/Formularios/2026/Formulario_210_2026.pdf).
- [Estatuto Tributario: arts. 26, 27, 28, 103, 107, 206 y 335–336; Libro VII](https://normograma.dian.gov.co/dian/compilacion/docs/estatuto_tributario.htm).

Completar las fichas particulares conforme a DICCIONARIO-CAMPOS.md. No guardar
declaraciones reales ni datos de contribuyentes en el repositorio.
