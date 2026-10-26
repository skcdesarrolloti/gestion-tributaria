# Ingresos: primera etapa operativa

## Nómina laboral · 26/10/2026

Registro → Ingresos → Trabajo muestra tabla mensual y acceso a Subir nómina.
El lector XLS/XLSX permite escoger hojas y personas autorizadas, con vista previa
y confirmación. Se conserva detalle en centavos; dos movimientos derivados a
pesos por quincena alimentan 32/33 y su cálculo 34. La retención laboral 132 se
muestra separada en nómina. No se descuenta como reducción del ingreso.
Repeticiones, correcciones con historial y captura manual se concilian antes de
sumar. Las hojas de primas con otro diseño requieren un adaptador posterior.
Ver [NOMINA-LABORAL.md](NOMINA-LABORAL.md). La autenticación sigue siendo propia,
sin conectar con funcionarios; el Excel compartido solo es una fuente de datos.

## Cuestionario anual de fuentes · 22/10/2026

En Registro de información → Ingresos, la sección Fuentes de ingresos pregunta
lo que no está respondido en el bloque anual `ingresos` de caracterización. Se
reutiliza ese bloque, sin crear un catálogo paralelo ni una segunda identidad.

Tres grupos plegables, con un grupo abierto a la vez: actividad/trabajo/pensión,
capital/participaciones y operaciones especiales/exterior. Naturales y sucesiones
tienen 13 preguntas; jurídicas 11, sin salarios ni pensión personal. Cada respuesta
admite Sí, No o Pendiente por confirmar. Vacío nunca significa No. Se abre primero
el grupo con faltantes; al completar todo, el cuestionario queda plegado.

Guardar un grupo conserva respuestas, notas y campos de los demás grupos.
Revisión optimista impide perder cambios de otra pestaña; CSRF, sesión y permisos
se verifican en POST `registro/fuentes`. No crea importes, no modifica el RUT ni
cambia condiciones fiscales con movimientos. Las respuestas del año no se copian
a otro año. El bloque queda en borrador tras editarlo, sin certificación documental
automática. La preparación inicial reutiliza la ausencia explícita de honorarios;
se rechaza guardar un tratamiento personal contrario a la respuesta del mismo año.

El RUT y las fuentes generales de la ficha orientan la revisión, pero no prueban
qué ingresos existieron en cada año. Exterior, activos, criptoactivos, ECE y otros
casos sin clasificar requieren revisión específica; no se asignan silenciosamente
a No laborales ni se habilitan deducciones por contestar Sí.

## Preparación con el RUT ya registrado · 22/10/2026

Registro recupera los datos mediante PerfilAnualSugerencias, también reutilizado
por la entrada histórica de perfiles anuales. No crea perfiles ni escribe por GET.

- Usa residencia y régimen ya declarados en Condiciones tributarias del mismo año.
- Consulta la información del RUT guardada: número, fecha, actividades y
  responsabilidades. Si no está en ese año, busca el perfil del último PDF
  importado; sin PDF, usa la última caracterización disponible. Expone año y
  procedencia, sin copiar la residencia de otro año ni certificar vigencia DIAN.
- Responsabilidad 05 propone Ordinario y 47 propone SIMPLE (continúa bloqueado
  para cálculo). Código 04 requiere confirmar modalidad; no se convierte en ESAL
  ni en Ordinario automáticamente. Las discrepancias se muestran para revisión.
- Jurídicas: servicios personales No aplica. En naturales se reutiliza únicamente
  la respuesta explícita del mismo año de que no hay honorarios. Actividad 0020
  explica condición de pensionado, sin presumir ausencia de otras fuentes.
- Precarga la referencia documental y explica debajo de cada campo su origen o
  lo que falta confirmar. Las condiciones personales no contenidas en el RUT
  requieren respaldo del año y elección del titular; no se inventan desde la dirección.
- Preparación, movimiento y consulta de casillas usan «?» y el visor de artículos
  interno. Se retiraron enlaces externos de esas ayudas. La información queda
  plegada hasta consultarla y no abre pestañas nuevas. Fuentes dentro del visor
  se muestran como texto, junto a la fecha de consulta y límites de vigencia.

Los valores se guardan solo al confirmar el formulario, con los permisos y CSRF
existentes. No se sobrescriben condiciones anuales o movimientos ya guardados.

## Captura organizada por año y formulario · 22/10/2026

Entrada principal: **Registro de información → Ingresos**. Selecciona contribuyente
y año 2025/2026. Si falta el perfil fiscal anual, se confirma en ese mismo registro.
Los perfiles y movimientos anteriores se conservan. Los accesos desde los años
guardados y desde Proyección abren esta captura, sin confundirla con el análisis.

- 210: General (Trabajo, Trabajo sin relación laboral con opción de costos,
  Capital, No laborales), Pensiones y Dividendos. Ganancias ocasionales es una
  sección independiente y no una cuarta cédula.
- 110: Actividades ordinarias, Financieros, Dividendos, Otros y Ganancias
  ocasionales. No muestra cédulas del 210. Naturales y sucesiones no residentes
  usan el 110 conforme a su perfil, no el 210 por el solo hecho de ser naturales.
- Se muestran filas con casilla, nombre, real, proyectado adicional y Registrar.
  Las ayudas de finalidad, norma y soporte se despliegan con «?»; los ajustes
  existentes de devoluciones e ingresos no constitutivos quedan plegados.
- 022 amplía el catálogo con 15 casillas: 210 (99,104,107,108,109,112),
  110 (49–56,80). 023 incluye 49–56 en el total bruto 58 del 110. No cambia
  identificadores ni importes existentes; no añade costos ni exenciones automáticas.
- El servidor limita el formulario al año y sección, incluidos POST manipulados.
  52/53 del 110 son exclusivas de naturales no residentes; 54/55 no se ofrecen
  al residente; 50 se reserva a entidades. Los supuestos especiales CHC y
  megainversión requieren evidencia y revisión particular, no se validan solos.

Fuentes contrastadas: [DIAN 210 e instructivo publicado en 2026](https://www.dian.gov.co/atencionciudadano/formulariosinstructivos/Formularios/2026/Formulario_210_2026.pdf)
y [DIAN 110 e instructivo publicado en 2026](https://www.dian.gov.co/atencionciudadano/formulariosinstructivos/Formularios/2026/Formulario_110_2026.pdf).
El instructivo 210 indica año gravable 2023 y siguientes. Publicación 2026 no
equivale a año gravable 2026: este último se presenta como proyección con estructura
de referencia, pendiente de contrastar contra el formulario que corresponda al
presentar. El registro bruto no completa el cálculo de exenciones, tarifas,
ECE, dividendos ni ganancias ocasionales; la hoja de escenarios sigue separada.

Las secciones siguientes documentan la primera etapa y sus rutas históricas.

## Cómo comprobar el flujo

1. Inicia sesión y entra a **Contribuyentes**.
2. En el registro que creaste, pulsa **Ingresos por año**.
3. Crea el perfil anual: año 2025 o 2026, residencia fiscal, régimen ordinario,
   tratamiento de servicios personales y referencia de la evidencia utilizada.
4. La aplicación determina 210 para persona natural o sucesión residente, y 110
   para persona jurídica o persona natural/sucesión no residente. En sucesiones
   la residencia indicada corresponde al causante al fallecer.

Debajo de cada desplegable del perfil se muestra definición, alcance y fundamento
de la opción seleccionada. La ayuda también aparece debajo de la casilla y del
estado de movimiento. Las opciones se adaptan al tipo de contribuyente.

El régimen muestra Ordinario y SIMPLE (consulta) para personas naturales; para
entidades, incluye RTE de ESAL y del sector cooperativo (consulta). Las sucesiones
no reciben SIMPLE como opción predeterminada. Solo Ordinario permite crear perfil:
los demás motores siguen pendientes y el servidor los rechaza sin convertirlos.
No contribuyente, no declarante y otros tratamientos particulares requieren revisión
separada; estas listas no pretenden cubrir todas las condiciones tributarias.

En servicios personales se distinguen Pendiente, No aplica, Costos y Exención.
Para entidades se ofrecen únicamente Pendiente/No aplica; no se les aplica la
elección personal del art. 206.10. La migración 008 admite No aplica sin alterar
los valores anteriores. La elección no acredita por sí sola elegibilidad ni beneficio.
5. Abre las fichas para consultar explicación, artículos, ejemplo, soportes y fórmula.
6. Pulsa **Registrar movimiento** y selecciona una casilla capturable.
7. Registra fecha de reconocimiento fiscal, concepto, tercero, país, moneda original,
   importe original y valor fiscal revisado en COP. Si la moneda es COP, repite el
   mismo entero; no escribas puntos o comas como separadores de miles.
8. Marca **Real** o **Proyectado adicional**. Real exige referencia del soporte;
   una partida que se resta exige también norma particular y justificación.
9. Guarda y comprueba los totales por casilla. Para corregir, usa **Editar movimiento**.

El valor proyectado corresponde exclusivamente a lo adicional esperado. No repetir
allí el real acumulado. El escenario suma real y proyectado. Los importes fiscales
se registran en pesos enteros, hasta 12 dígitos por movimiento; el original admite
decimales con punto. No se calcula ni se impone una TRM de cierre para todos los casos.

## Alcance y límites visibles

- Primera depuración de 210: casillas 32–34, 43–46, 58–61 y 74–78, con captura
  solo de ingresos y partidas que se restan. Los resultados se calculan.
- Inicio de 110: 47, 48, 57, 59, 60 y 62–66; resultados 58, 61 y 67. Los totales
  de ingresos son parciales porque no incluyen las casillas de dividendos 49–56.
- Catálogo inicial de 29 fichas entre ambos formularios, referencia `2026-10`,
  basada en los instructivos publicados en 2026. El año gravable se guarda aparte.
- Las casillas 43–45 requieren opción de costos en el perfil del 210. Si el
  tratamiento está pendiente, revisar la clasificación antes de registrar servicios.
- Los perfiles guardados muestran año, selecciones y evidencia. **Ver o editar perfil**
  recupera sus campos. Mientras no tenga movimientos se pueden corregir las opciones,
  incluido el año; no se sobrescribe otro perfil ya existente. Con movimientos se
  permite actualizar la evidencia, pero se bloquean cambios de año y clasificación.
- **Crear otro perfil anual** queda plegado cuando hay perfiles guardados; es un
  formulario nuevo, no la edición del anterior. No crear otro contribuyente para
  ocultar un error. Los cambios se guardan al pulsar el botón, no al navegar.
- No se calcula impuesto, límite de exenciones personales, pérdida fiscal, UGPP,
  ganancias ocasionales, dividendos especiales ni liquidación final.
- Si una operación aritmética queda negativa, se muestra y se pide revisión.
  No se convierte automáticamente en una pérdida fiscal aceptada.
- Valores sin redondeo al millar para presentación DIAN; no son archivo de declaración.
- Se guardan referencias de soportes, no archivos adjuntos. Las referencias y
  justificaciones ingresadas no equivalen a aprobación de procedencia tributaria.
- Los datos de fallecimiento y representación detallada de sucesiones siguen
  en el alcance pendiente. SIMPLE y otros regímenes requieren su propio motor.

## Seguridad y estructura

Todos los accesos se filtran por el propietario autenticado del contribuyente,
incluidos perfil, movimientos y edición. El rol no abre registros ajenos.
Escrituras POST con CSRF, consultas preparadas y salida HTML escapada.
No se permite usar conceptos de otro formulario ni capturar resultados calculados.

`IngresoController` coordina, `Ingreso` consulta, `IngresoData` valida e
`IngresoSummary` agrega movimientos y aplica fórmulas del alcance inicial.
La base central de funcionarios conserva su acceso de solo lectura.

Migraciones nuevas: 004 crea catálogo, 005 lo carga, 006 crea perfiles anuales y
007 movimientos. Cada una incluye comprobación y se ejecuta automáticamente.
Conservar todas las migraciones históricas y config/local.php. Desplegar código,
migraciones, comprobaciones y CSS juntos; no importar SQL manualmente.

Reversión de código: conservar tablas, catálogo y registros. No eliminar datos;
una reversión de esquema requiere respaldo y un cambio posterior revisado.

## Validación

`php bin/check.php`, `php tests/unit.php` y `php tests/integration.php` en bases
aisladas. Incluyen fórmulas, separación real/proyectado, rechazo de conceptos o
años ajenos, privacidad entre cuentas, CSRF, escape, creación y corrección de
movimientos, justificación de costos y selección 210/110. Revisión en Edge a
1280px y 360px de perfil, fichas y captura, con datos ficticios.

Referencias normativas: ver [INGRESOS-DISENO.md](INGRESOS-DISENO.md).
