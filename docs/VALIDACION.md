# Validación de la base MVC

Verificación local realizada el 13 de octubre de 2026 con PHP 8.2.12, MariaDB 10.4.32 y Apache de XAMPP, usando bases temporales aisladas. Las pruebas funcionales no usaron cuentas reales. La estructura central se consultó en modo de solo lectura para confirmar los campos del login; no se extrajeron contraseñas.

## Resultado

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
