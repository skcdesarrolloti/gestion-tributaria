# Validación de la base MVC

Verificación local realizada el 13 de octubre de 2026 con PHP 8.2.12, MariaDB 10.4.32 y Apache de XAMPP, usando bases temporales aisladas. Las pruebas funcionales no usaron cuentas reales. La estructura central se consultó en modo de solo lectura para confirmar los campos del login; no se extrajeron contraseñas.

## Resultado

### Pestañas persistentes de Registro · 28/10/2026

Sintaxis, unitarias e integración aislada correctas. Las cuatro pestañas conservan
persona/año, marcan la sección elegida y permanecen fuera de paneles plegados.
Subir nómina conserva el contexto en selección de año, carga y confirmación;
una ficha inexistente se rechaza sin guardar. Se mantienen los controles previos
de permisos, CSRF, importación repetida y nómina ajena. No se cambia el esquema.

### Sección activa del menú · 28/10/2026

Sintaxis y unitarias correctas. Renderizado local de trece rutas: exactamente
una sección activa, Configuración resaltada en su ruta y salida secundaria.
Se conservan los permisos del menú, destinos, POST y CSRF de Cerrar sesión.
Publicación f427b1d verificada con sesión autenticada: Configuración es el único
elemento marcado como actual y tiene fondo azul; Cerrar sesión presenta borde
sin fondo azul. Se comprobó visualmente sin modificar parámetros ni cerrar sesión.

### Consulta compacta de retención en la nómina · 28/10/2026

Unitarias e integración aislada correctas: diferencia y explicación plegada
en cada mes, tabla DIAN dentro de la consulta y opciones de contador/reserva
conservadas pero plegadas. La revisión vigente, incluido procedimiento 2, tiene
prioridad sobre P1; sin soporte solo hay hipótesis explícita, nunca OK confirmado.
Meses parciales y pagadores permanecen separados. Los acumulados confirmados no
mezclan simulaciones. Consultar conserva movimientos y revisiones originales.
La barra de orientación conserva persona/año y acceso al menú principal.
Publicación 990ac3d comprobada con sesión autenticada: enero muestra diferencia
provisional y despliega su cuenta bajo la fila; la tabla DIAN abre dentro de la
misma nómina. «Menú principal» vuelve a la cabecera (posición superior 0) y
«Volver a ingresos» conserva contribuyente y 2026. No se guardaron supuestos,
revisiones ni movimientos en producción para esta comprobación.

### Extractos sin clave y reintegros de GMF · 28/10/2026

Sintaxis, unitarias, apertura QPDF WASM e integración en base aislada correctas.
Se prueba PDF sin contraseña, AES-256, contraseña incorrecta, anulación explícita
«ANU. COM DECRETO», abonos desconocidos rechazados en conciliación, guardado sin
revisión visual y conservación de ese estado. Si el detalle falla, se conserva
el período reconocido sin publicar importes y se siguen rechazando cuenta o mes
contradictorios. CSRF, permisos, duplicados y limpieza de copias siguen pasando.
El archivo señalado por el responsable se inspeccionó localmente, sin subirlo a
servicios externos ni copiarlo a Git o a la base de pruebas: abre sin contraseña
y concilia sus dos páginas con el reconocimiento del reintegro corregido.
Publicación d3c97d1 comprobada con sesión autenticada en Hostinger: formulario
con explicación de PDF sin clave, casilla opcional y siete soportes anteriores
visibles. La carga del archivo real no se realizó: la extensión de Chrome
bloquea la selección de archivos locales. Queda pendiente que el responsable
seleccione ese PDF y guarde; no se marcaron revisiones ni se sustituyeron soportes.

### Parámetros anuales y desglose P1 · 28/10/2026

Unitarias: UVT y topes por año, tabla parametrizada realmente usada en el motor,
invalidez de revisiones con parámetros anteriores y cuenta en pesos con excedente
y redondeo. Integración aislada: migración 028, Socio activo, CSRF, rechazo de
terceros, no invención de UVT futura, protección de años anteriores, publicaciones
concurrentes rechazadas, historial preservado y fuentes escapadas. No se publican
valores ficticios ni se modifican parámetros reales para el ensayo.
En Chrome con la base aislada se comprobaron Configuración, sus valores iniciales
y la explicación P1 expandida: tabla de pasos, detalle de aportes, UVT, tramo,
excedente y operación hasta el redondeo. El detalle se abre bajo el mes a todo
el ancho, sin desbordamiento horizontal de página en escritorio. La base y el
servidor temporales se retiraron al terminar; las capturas usan datos ficticios.
Publicación 7b94043 comprobada en Hostinger con sesión autenticada: Configuración
abre los valores iniciales de 2026 y su fuente; la simulación conserva nóminas y
abre la explicación completa de enero a todo el ancho. Se comprobaron los aportes
de origen, la operación del tramo y el redondeo sin publicar parámetros ni
confirmar revisiones reales. El guardado y la concurrencia se probaron solo en
la base aislada; no se cambiaron valores oficiales para ensayar en producción.

### Simulación provisional de procedimiento 1 · 28/10/2026

Unitarias: cálculo por pagador y año, acumulados de los mismos meses completos,
consumo cronológico del cupo anual, exclusión de períodos parciales/retroactivos,
bloqueo tras un hueco, cero calculado distinto de pendiente y origen intacto.
La simulación no devuelve estado OK ni reutiliza revisiones como hechos.
Integración aislada comprueba tabla provisional visible, ausencia de formularios
de confirmación en la simulación y conservación de movimientos y revisiones
al consultarla. No se escribe en la base real para probar la hipótesis.

### Tabla laboral y comparación acumulada · 28/10/2026

Sintaxis, unitarias e integración aislada correctas. La tabla visible comparte
los siete rangos ET 383 con el motor y convierte a pesos según UVT del año.
Pruebas: rango personal solo tras revisión vigente del procedimiento 1;
subtotales con meses comparables; pagadores separados; revisión vencida, huecos
y meses parciales pendientes; esperado cero confirmado distinto de desconocido.
No cambia descuentos, movimientos ni reglas de depuración. El texto para el
contador no se envía y la consulta no guarda ni confirma supuestos del usuario.

### Ayudas y comparación visible de retención · 28/10/2026

Sintaxis, unitarias e integración aislada correctas: acceso directo al último
mes completo, selector limitado a pagadores del perfil, ayuda asociada a cada
control, opción desconocida rechazada sin confirmar datos, enero con acumulado
anterior 0 y ejemplo que no devuelve OK ni altera la nómina. Se conservan las
pruebas de permisos, CSRF, revisión concurrente y movimientos sin duplicados.
Chrome con datos ficticios: ayuda cambia al seleccionar procedimiento 1/2 o
desconocido, porcentaje visible solo en 2, aviso de cambios sin comparar y
guardado que regresa al bloque visible del resultado. No se guardan supuestos
ni cambios de revisión en las cuentas reales para el ensayo.
Publicación 755d685 comprobada en Hostinger: el botón real de nómina abre el
último mes completo; enero presenta datos recuperados, resultado pendiente y
ayudas de cada campo. Se desplegó la explicación de procedimientos sin guardar
ni cambiar opciones en producción. El ejemplo permanece separado del resultado
confirmado; la comparación real no se certificó con supuestos.

### Acceso directo a Pensiones y extractos · 28/10/2026

Unitarias, sintaxis e integración aislada correctas. Pensiones muestra los
documentos ya existentes antes de exigir perfil anual, abre su visor y permite
buscar sin cambiar de sección. Se prueban año vacío, documento de otro año o
persona, carga con retorno a Pensiones y conservación íntegra de registros,
huellas y lecturas tras consultar. No modifica esquema ni calcula una pensión
bruta desde abonos bancarios netos. El acceso general a extractos se conserva.
Chrome con PDF ficticio de dos páginas: menú, calendario, consulta mensual,
búsqueda y regreso al calendario correctos, sin desbordamiento de página en
escritorio. La comprobación autenticada previa en Hostinger localizó seis
soportes de 2026; no se cargaron ni modificaron archivos reales para el ensayo.
Publicación f372a93 comprobada en Hostinger con sesión autenticada: el enlace
real del menú abre Pensiones y muestra los seis soportes existentes; abrir
enero conserva sus movimientos y visor, y «Volver a los meses guardados»
regresa al calendario sin cambiar de persona/año ni salir de Pensiones.

### Retención mensual y reserva salarial · 28/10/2026

Sintaxis y unitarias correctas: tabla mensual 383 separada de la anual 241,
base después de aportes/deducciones, topes anuales, porcentaje certificado de
procedimiento 2, redondeo y períodos completos por pagador. Integración aislada:
persistencia por contribuyente/año, revisión concurrente, huellas, CSRF,
conservación de importes, hipótesis y revisiones sin borrarse mutuamente,
acceso propio del Usuario y rechazo de fichas ajenas. Los acumulados previos
requieren soporte; no se da OK a una nómina incompleta o sin depuración.

Chrome con datos ficticios: guardar revisión mensual, desplegar Observaciones,
regresar a acumulados y guardar/reabrir hipótesis anual. Caso de ensayo con
impuesto $5.983.000, retenido real $30.000, futuro $4.000.000, anticipo $457.000,
faltante $2.410.000 y seis reservas de $401.667. Alcance salarial provisional
visible, sin desbordamiento horizontal de página en escritorio. Se corrigieron
la recarga de supuestos y el cierre HTML del enlace antes de publicar.
No se cambian ingresos ni cuentas reales. El guardado con datos reales en
Hostinger queda pendiente de confirmar los soportes del empleador.
Publicación fa8fa41 comprobada en sesión autenticada de Hostinger: aparecen
el control en los meses importados y el acceso a reserva abre su formulario
de 2026. Sin escribir hipótesis ni revisiones ficticias en producción. CSS
generado con versión por fecha para evitar que la caché oculte clases nuevas.

### Apertura de extractos protegidos · 28/10/2026

El navegador abre el PDF con el documento de la ficha o clave opcional, sin
enviarla al servidor. QPDF WebAssembly fijado en 0.0.2, assets del mismo sitio,
Worker aislado y terminado después de cada intento. Original y copia de lectura
privados; se retiran ambas copias ante error/duplicado. Sin cambios de esquema.
Prueba Node: AES-256 sintético, clave errónea rechazada y lectura de dos páginas
conciliada con mes automático. PHP sintaxis/unitarias/integración aislada pasan,
incluyendo envío de original/copia, persistencia, duplicados y limpieza.
Lectura local del original BBVA autorizado con RC4-128 y clave suministrada por
el responsable confirma septiembre 2026 y dos páginas conciliadas; sin datos ni
claves reales en Git o fixtures. Chrome con fixture cifrado AES-256: el Worker
abre el documento y la lectura PHP devuelve julio 2025 y dos páginas conciliadas.
La carga HTTP de ambas copias pasa en la integración aislada. La automatización
del selector de archivos de Chrome requiere permiso de la extensión; no se
activó ese permiso ni se usó para subir documentos reales. Publicación 91994f1
comprobada en sesión autenticada: aparecen la apertura protegida activada, la
clave opcional y el mes automático. El primer guardado protegido real en el
sitio queda para la carga manual del responsable; no se afirma haberlo importado.

### Extractos bancarios · 26/10/2026

Sintaxis, unitarias e integración en base aleatoria: carga HTTP de PDF,
conservación privada íntegra, CSRF, documentos ajenos, permisos revalidados al
guardar, copia renombrada, período repetido, años separados y visor inline del
propio origen. Migraciones 026/027 automáticas sin alterar las anteriores.
PDF sintético de dos páginas: mes detectado, titular y cuenta contrastados,
saldos conciliados, GMF/reintegros, rendimientos, pensión y búsqueda por comercio.
Páginas incompletas/repetidas no concilian; ningún dato real se copia al ensayo.
Pruebas de lectura local sin escritura con copias legibles del formato BBVA
confirman enero, febrero, agosto y septiembre; se detecta devolución de GMF y
se evita tomar otros abonos como pensión. PDF cifrados quedan pendientes.
Chrome con datos ficticios: calendario, acumulado, búsqueda de un comercio y
visor PDF de dos páginas dentro de la misma pantalla correctos. Publicación
932efbb comprobada en sesión autenticada de producción: abre Extractos por
contribuyente/año, muestra selector automático de mes y formulario de carga,
sin error de migraciones. No se cargaron soportes reales como prueba en el sitio;
su primer guardado en Hostinger sigue pendiente de la carga del responsable.

### Nómina laboral · 26/10/2026

Selección por persona y alcance: en Edge, «Todos los períodos disponibles del
año» marca las quincenas reconocidas, quitar una persona desmarca sus filas y
volver a enero conserva solo ese mes. Se verificó con XLS y XLSX sintéticos,
en escritorio y móvil. No amplía permisos ni activa perfiles faltantes.

Migraciones 024/025 probadas automáticamente en base aleatoria aislada. Sintaxis
y unitarias PHP correctas; adaptador JS con cambio de columnas, retroactivos
duplicados, básico excluido, identidad exacta y omisión de hojas de otro diseño.
Integración completa: CSRF, vista previa sin escritura, dos quincenas a 32/33,
34 sin descontar retenciones, copia renombrada, corrección expresa con historial,
revisión concurrente, períodos superpuestos y reversión atómica de lote fallido,
bloqueo de edición de movimiento derivado y duplicación manual, años separados,
aislamiento de Usuario y acceso revocado entre vista previa y confirmación.
Edge a 1440px/390px con libro XLS sintético: lectura, pagador, preselección enero,
confirmación, consulta mensual y repetición sin duplicado. Sin errores JS ni
desbordamiento de página; tablas con desplazamiento horizontal propio.
No se importaron nóminas reales en la base de pruebas ni se cambiaron cuentas
reales. La comprobación autenticada en Hostinger queda pendiente tras publicar.

### Contraste RUT y preguntas anuales de ingresos · 22/10/2026

Sintaxis y unitarias correctas. Integración aislada completa: respuestas por
grupos, convivencia de salario/pensión/capital, vacíos pendientes, persistencia
por año, conservación de otros grupos, conflicto 409 de revisión, CSRF 419,
aislamiento de Usuario y rechazo de tratamiento contradictorio de honorarios.
Documentos muestra contraste y cobertura; un RUT de otro año se consulta sin
volver a subirlo ni compararlo contra campos del año equivocado.
RutPdfForm informa ahora hoja 1 y hojas 2–7; pruebas multipágina actualizadas.
RutRevision compara datos sin sobrescribir diferencias y trata cero como valor.
Edge 1440px/390px: guardado de actividad, apertura del siguiente grupo pendiente,
años separados, carga real de PDF sintético y comparación de su actividad y hoja
principal. Sin errores JS ni desbordamiento de página. CSS recompilado sin cambios
al tema. Sin migraciones nuevas ni datos privados en Git. Pendiente verificar
el recorrido autenticado en producción.

### Preparación anual desde el RUT y ayudas internas · 22/10/2026

Unitarias: RUT con código 05/47/04, responsabilidades contradictorias, condición
de pensionado sin presumir otras fuentes, residencia solo del año solicitado,
No aplica explícito y entidad jurídica, y artículos internos de todas las opciones.
Integración aislada: carga responsabilidad del RUT y residencia anual guardadas,
no crea perfil por GET, conserva permisos y sirve artículos 10 y 595 con sesión.
Edge 1440px/390px: régimen, residencia y servicios precargados según sus fuentes,
referencia documental completada, advertencia RUT de otro año, artículo 10 dentro
del mismo cuadro, cambio dinámico de ayuda y guardado del perfil. Sin enlaces
externos en preparación, nuevas pestañas, errores JS ni desbordamiento de página.
Colección legal generada desde DIAN con 126 artículos ET y cinco de otras leyes.
No hay cambios de esquema. Pendiente validación autenticada en producción.

### Ingresos por año y sección DIAN · 22/10/2026

Migraciones 022/023 y pruebas completas en base aislada correctas. Pensiones
99, Dividendos 104/107/108/109 y Ganancias ocasionales 112 se capturan por
separado. 110 organiza ingresos y dividendos propios, sin cédulas ni casillas
exclusivas de naturales para sociedades. POST de otra cédula se rechaza;
guardado vuelve al mismo año/sección; repetir migraciones conserva movimientos.
Unitarias y sintaxis correctas; CSS recompilado con el tema existente.
Edge 1440px y 390px: registro real de pensión, retorno, año 2026 sin importes
2025, cuatro filas de dividendos, sociedad y configuración anual dentro de
Registro. Sin errores JS ni desbordamiento de página; tablas con scroll propio.
Pendiente validación autenticada en producción.

### Entrada a Ingresos desde el contribuyente · 22/10/2026

El menú conserva contribuyente y año al abrir Ingresos o Costos y gastos.
Se retiró del registro el enlace para completar el RUT. Integración aislada
completa correcta: se extrae el enlace real del menú de una ficha 2025, se abre
y se comprueba que devuelve Registro de información, sin redirección al RUT.
Sintaxis y unitarias correctas. Pendiente comprobarlo en la sesión de producción.

### Recuperación desde otro Socio · 22/10/2026

Por instrucción del responsable, el cambio inicial pendiente de un Socio permite
abrir Usuarios y accesos, crear cuentas y restablecer otra contraseña. El resto
de módulos conserva el bloqueo pendiente. La recuperación sigue exigiendo
sesión de Socio, clave actual y CSRF, sin alterar el hash ni el estado de cambio
de contraseña del Socio que ayuda. Prueba aislada en socio-recuperacion-integration.php.
Integración completa, sintaxis y unitarias correctas. Edge verifica entrada con
clave pendiente, enlace directo desde Mi perfil, restablecimiento de otra cuenta
y nuevo ingreso con la misma clave del Socio auxiliar; móvil a 390px sin desbordamiento.

### Artículos dentro de las fichas · 22/10/2026

Colección generada desde Normograma DIAN: 66 artículos del ET y cinco de leyes
citadas, con fecha y fuente. Unitarias e integración aislada completas correctas:
cobertura de referencias, expansión de rangos sin confundir numerales, separación
de artículos, conservación de tablas/textos tachados, ruta con sesión y rechazo
de rutas arbitrarias. No cambian fórmulas fiscales ni tablas de la base.
Edge a 1440px y 390px: carga solo al elegir artículo, misma URL sin pestañas nuevas,
cierre y reutilización del texto, scroll interno y reintento tras fallo simulado;
sin desbordamiento ni errores JavaScript. Ver ARTICULOS-EN-FICHAS.md.

### Menú y registro de información · 22/10/2026

Menús ordenados por el recorrido solicitado, con Exógena independiente y sin
cambios de esquema. Registro y análisis usan consultas autorizadas existentes;
2025/2026 se seleccionan por perfil, sin copiar importes. Las pestañas incompletas
se identifican expresamente. Ver [MENU-Y-REGISTRO.md](MENU-Y-REGISTRO.md).
Integración aislada completa correcta: costos separados de ingresos, conservación
de año/componente al guardar, casilla preseleccionada y rechazo de fichas ajenas.
Revisión en Edge a 1440px y 390px: menús, Escape, registro de costo y retorno a la
pestaña, acceso a la hoja de escenarios; sin desbordamiento ni errores JavaScript.

### Cuentas independientes · 22/10/2026

Integración completa correcta en bases temporales: migración 021, activación
privada con secreto de un solo uso, mantenimiento del ID del Socio, creación de
terceros sin conexión a funcionarios, contraseñas con hash, cambio inicial
obligatorio, CSRF, duplicados, aislamiento, cambio y restablecimiento con clave
actual, revocación de sesiones y suspensión. Las pruebas tributarias existentes
siguen pasando. No se modificaron cuentas ni contraseñas reales.
Ver [primera entrada](CUENTAS-INDEPENDIENTES.md).
Revisión visual en Edge a 1440px y 390px: activación, login independiente, creación
de Socio externo, cambio inicial obligatorio y acceso global; sin desbordamiento
ni errores JavaScript. CSS recompilado con el tema existente. La activación y
creación de cuentas reales se completan en la sesión del responsable.

Los apartados de autenticación central siguientes describen versiones anteriores.

### Perfiles Socio y Usuario · 22/10/2026

Ampliación solicitada: Socio habilitado puede consultar y editar todos los
contribuyentes sin asignación individual. Integración aislada completa correcta:
edición de contacto ajeno, importación RUT, escenario y Exógena; Usuario conserva
la limitación a su ficha aunque haya creado otra anteriormente. Se comprueba
que guardar Socio ignora el selector de contribuyente y conserva CSRF.
Revisión en Edge: selector oculto para Socio y visible para Usuario, edición
global persistida, Usuario sin acceso a ficha ajena ni administración; escritorio
y móvil de 390px sin desbordamiento ni errores de JavaScript.

Sintaxis, unitarias e integración completa en bases aleatorias correctas. Se
comprobaron migraciones 018–020, vinculación inicial explícita, asignación,
consulta global de Socio, edición propia, usuario con una sola ficha, rechazo
de IDs y descargas ajenas, importación RUT, escenario propio, Exógena,
reasignación y suspensión en sesión abierta, CSRF, concurrencia, último Socio
y auditoría. No se modificaron cuentas ni contraseñas de producción.
Revisión de navegación y formularios en Edge a 1440px y 390px, sin desbordamiento.
La activación y asignaciones de las cuentas reales deben comprobarse en su sesión
después de publicar. Ver [PERFILES-Y-ACCESOS.md](PERFILES-Y-ACCESOS.md).

- Sintaxis PHP y límite de 250 líneas: correctos.
- Pruebas unitarias de CSRF, escape HTML y límite de intentos: correctas.
- 30 comprobaciones de integración: login directo sin instalador, dos bases aisladas, credenciales institucionales heredadas y con hash, rechazo de cuentas locales, sesión, perfil, logout, cuenta inactiva y migraciones de columnas.
- Comprobado que la autenticación no crea la base central, no ejecuta migraciones allí y no modifica contraseñas; las sesiones locales antiguas quedan invalidadas.
- Migraciones: repetición sin duplicados, recuperación tras DDL confirmado y detección de cambios en scripts ya aplicados.
- Apache en subcarpeta: login y perfil correctos; raíz redirige a public; configuración, SQL, logs y .git devuelven 403.
- Revisión en navegador: login, inicio y perfil; panel a 360px sin desbordamiento horizontal.
- CSS compilado con el tema original y Tailwind 3.4.17.

## Repetir integración

### Exógena 1001: AG2025 versión 10

Verificado el 14/10/2026 con el anexo técnico DIAN (162/2023, anexo 18): veinte
campos y ocho importes. Sintaxis y unitarias correctas; integración aislada confirma
migración 011, captura vacía 2025, guardado/edición, CSRF, privacidad, ubicación
Colombia/exterior, rechazo de otro año y formato exacto de importes de 18 dígitos.
1007 y 5247 mantienen sus pruebas de funcionamiento. Revisión visual de listado,
captura y menú en Edge a 1280px/360px sin desbordamiento; CSS recompilado.
No se cargaron datos reales. Se identificó la actualización posterior v11 y su
vigencia, sin aplicarla al módulo AG2025. Ver EXOGENA-1001.md.
La consulta autenticada en Hostinger debe comprobarse tras publicar.

### Exógena 1007 y nombre del 5247

Verificado el 14/10/2026: migración 010, captura vacía 2025 (incluido país), guardado
y edición independientes, once campos/identificaciones conservados, CSRF, sesión,
importes explícitos y protección por empresa/propietario. Unitarias e integración
aislada correctas, incluido el 5247 tras reutilizar su acceso a registros y controlador.
Revisión visual de lista/captura y menú con ambos nombres en Edge a 1280px y 360px,
sin desbordamiento; CSS recompilado. El archivo recibido era un auxiliar 2024, no
una plantilla DIAN 1007; no se importaron datos reales. Ver EXOGENA-1007.md.
Las rutas en producción y el flujo autenticado deben comprobarse tras publicar.

### Exógena 5247

Presentación del menú y estructura revisada el 14/10/2026: panel de formatos,
tarjetas y resumen de los 22 campos en Edge a 1280px y 360px, sin desbordamiento.
Comprobados apertura, cierre con Escape/clic exterior y navegación por enlace.
Sintaxis PHP/JS y unitarias correctas; CSS recompilado. Este cambio de presentación
no modifica tablas ni registros. El flujo autenticado de producción sigue pendiente.

Verificada el 14 de octubre de 2026 con datos ficticios: migración 009 automática
en la base temporal, orden de 22 campos, preservación de códigos/identificaciones,
captura y edición persistentes, importes explícitos, CSRF, sesión, y aislamiento
por propietario e informante jurídico. Pasaron sintaxis, unitarias e integración.
Menú Exógena con submenú 5247, selección y captura revisados en Edge a 1280px y
360px, sin desbordamiento horizontal; CSS recompilado con el tema compartido.
No se importaron datos del Excel real. La referencia es la plantilla 2024, no una
validación de presentación de otro período. Año objetivo confirmado: 2025, con
captura inicial vacía; faltan catálogos y versión técnica antes de exportar/enviar.
El responsable solicitó publicar esta estructura en main. El flujo autenticado en
Hostinger debe comprobarse después del despliegue.

### Estructura de proyección anual

Verificada el 14 de octubre de 2026: sintaxis, unitarias de agregación mensual,
separación real/proyectado, exclusión de casillas derivadas y aviso de proyecciones
pasadas. La integración aislada confirma sesión obligatoria, tablero propio con
importes y meses sin registros, rechazo de perfiles ajenos/IDs inválidos y mensaje
de cálculo pendiente. Revisión visual de la vista con datos ficticios en Edge a
1280px y 360px, sin desbordamiento horizontal. Se reutilizaron clases ya existentes.
El tablero no calcula impuesto ni ahorro y no confirma que sus datos estén completos.
La consulta autenticada en Hostinger queda pendiente de comprobación.

### Consulta y edición de perfiles guardados

Verificado el 14 de octubre de 2026: selecciones y evidencia visibles al volver,
edición precargada (incluido año), corrección de perfiles sin movimientos, rechazo
de años duplicados y protección de clasificación cuando ya hay movimientos.
Pasaron integración aislada, CSRF y privacidad; editar el perfil no borra movimientos.
Se bloquea el perfil durante las escrituras para evitar cambios concurrentes de
clasificación. Revisión visual de listado y edición a 1280px y 360px.

### Ingresos: primera etapa

El 14 de octubre de 2026 pasaron sintaxis PHP, unitarias e integración aislada del
perfil anual y movimientos, incluida creación automática de catálogo y tablas,
selección 210/110, cálculos por casilla, edición, CSRF y aislamiento por propietario.
Se revisaron perfil, fichas y formulario en Edge a 1280px y 360px, sin desbordamiento;
se verificó la apertura de explicaciones. CSS recompilado. Ver docs/INGRESOS.md.
El flujo autenticado en producción debe comprobarse después del despliegue.

Ayuda contextual de desplegables verificada el 14 de octubre: perfiles de persona
y entidad, selección de régimen, servicios y casillas/estado de movimiento en Edge
a 1280px y 360px. Se comprobó el bloqueo de regímenes de consulta, su explicación
sin JavaScript tras un POST y el guardado de No aplica mediante la migración 008.

### Primer módulo: Contribuyentes

Verificado localmente el 13 de octubre de 2026: sintaxis, pruebas unitarias e
integración en base temporal, incluyendo registro, edición, fuentes múltiples,
duplicados, CSRF y separación entre cuentas. La tabla se crea automáticamente.
Revisión visual de listado y formulario con datos ficticios en Edge a 1280px y
360px, sin desbordamiento horizontal. CSS recompilado con el tema existente.
La ruta original `C:\Workspace\desarrollo-skc\control-servicios-comerciales`
no estaba disponible; se conservó su copia versionada en `resources/theme/comerciales.cjs`.
El funcionamiento del módulo en Hostinger queda pendiente de despliegue y comprobación.

El 14 de octubre de 2026 se repitieron antes del commit la revisión PHP, las pruebas
unitarias y las 49 comprobaciones de integración en una base temporal: todas correctas.

Después de integrar con main y su autenticación central, se repitió la suite completa
con dos bases temporales. Registro y edición funcionan con IDs centrales ausentes
de la tabla local. La migración 003 elimina la referencia local sin modificar los
scripts históricos ni la base central. Autenticación central, separación de cuentas,
CSRF y migraciones pasaron; producción sigue pendiente de verificación.

El 14 de octubre se validó también el cambio de fuentes según tipo: en Edge a
1280px y 360px se ocultan/deshabilitan salario y pensión para empresas, cambian
las etiquetas y la guía, se advierte sobre selecciones excluidas y se conservan
al volver al tipo anterior antes de guardar. Pasaron sintaxis PHP/JS, unitarias
y la integración aislada, incluida la validación de fuentes por el servidor.

La prueba requiere MySQL/MariaDB local, extensiones pdo_mysql y curl, y permiso para crear bases temporales. Se ejecuta sin config/local.php: guarda ese archivo privado en otro lugar antes y restáuralo al terminar.

```powershell
$env:TEST_DB_HOST = '127.0.0.1'
$env:TEST_DB_PORT = '3306'
$env:TEST_DB_USER = 'root'
php tests/integration.php
```

Si el usuario local tiene contraseña, configura TEST_DB_PASSWORD en el entorno de la terminal. No guardes credenciales reales en este documento. La prueba genera una base mvc_test_ aleatoria y otra con sufijo _auth para los funcionarios sintéticos; elimina exclusivamente esas dos bases al terminar. No apuntes estas pruebas a producción.

GitHub Actions ejecutará lint, pruebas unitarias e integración con un servicio MySQL 8.0 temporal. El resultado remoto debe consultarse en la pestaña Actions; la verificación local no confirma por sí sola esa ejecución.

## Alcance y dependencia de compilación

La hoja de planeación agrega tests/simulador-unit.php e integración privada con
tests/simulador-integration.php. Requiere base aislada por migración 013. Verificar
también con navegador que cambiar una celda actualiza el impuesto sin guardar,
que una respuesta atrasada no reaparece, y que guardar conserva versiones previas.
Los fixtures son ficticios; no copiar importes ni identificación de la plantilla real.

Catálogo de beneficios de personas naturales (21/10/2026): pruebas unitarias e
integración aislada verifican UVT 2025/2026, búsqueda, filtros, rechazo de otro
año, escape de búsquedas, autenticación y ausencia de rutas de escritura.
No son pruebas de procedencia individual de beneficios ni de cálculo del impuesto.

El catálogo ampliado de Exógena pasó sintaxis, unitarias e integración aislada:
once formatos nuevos, migración 012, campos vacíos, captura y edición, CSRF,
separación de propietarios y de formatos, y conservación de los registros previos.
En Edge se inspeccionaron panel, menú y formularios 1010/2276/5252 a 1280 y 360 px,
sin desbordamiento horizontal; el menú desplaza sus seis grupos dentro del panel.
Estas pruebas usan empresas ficticias. No confirman captura autenticada ni
presentación de información en producción. Ver docs/CATALOGO-EXOGENA.md.

El acceso institucional utiliza auth_database; el login con una cuenta real sigue
pendiente de verificación. El módulo inicial de contribuyentes se describe en
`docs/CONTRIBUYENTES.md`. Las pruebas locales no confirman el funcionamiento en Hostinger.

`npm audit` reportó 7 avisos en dependencias de desarrollo de Tailwind 3.4.17 (2 moderados y 5 altos). Se conserva la versión del tema de referencia; no se hizo una migración mayor de estilos. Estas dependencias se usan solo para compilar CSS localmente y no se suben al hosting. Revisar los avisos antes de ampliar esa cadena de compilación; no procesar fuentes de terceros no confiables con ella.
