# Validación de la base MVC

Verificación local realizada el 13 de octubre de 2026 con PHP 8.2.12, MariaDB 10.4.32 y Apache de XAMPP, usando bases temporales aisladas. Las pruebas funcionales no usaron cuentas reales. La estructura central se consultó en modo de solo lectura para confirmar los campos del login; no se extrajeron contraseñas.

## Resultado

- Sintaxis PHP y límite de 250 líneas: correctos.
- Pruebas unitarias de CSRF, escape HTML y límite de intentos: correctas.
- 30 comprobaciones de integración: login directo sin instalador, dos bases aisladas, credenciales institucionales heredadas y con hash, rechazo de cuentas locales, sesión, perfil, logout, cuenta inactiva y migraciones de columnas.
- Comprobado que la autenticación no crea la base central, no ejecuta migraciones allí y no modifica contraseñas; las sesiones locales antiguas quedan invalidadas.
- Migraciones: repetición sin duplicados, recuperación tras DDL confirmado y detección de cambios en scripts ya aplicados.
- Apache en subcarpeta: login y perfil correctos; raíz redirige a public; configuración, SQL, logs y .git devuelven 403.
- Revisión en navegador: login, inicio y perfil; panel a 360px sin desbordamiento horizontal.
- CSS compilado con el tema original y Tailwind 3.4.17.

## Repetir integración

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

El acceso institucional utiliza auth_database; el login con una cuenta real sigue
pendiente de verificación. El módulo inicial de contribuyentes se describe en
`docs/CONTRIBUYENTES.md`. Las pruebas locales no confirman el funcionamiento en Hostinger.

`npm audit` reportó 7 avisos en dependencias de desarrollo de Tailwind 3.4.17 (2 moderados y 5 altos). Se conserva la versión del tema de referencia; no se hizo una migración mayor de estilos. Estas dependencias se usan solo para compilar CSS localmente y no se suben al hosting. Revisar los avisos antes de ampliar esa cadena de compilación; no procesar fuentes de terceros no confiables con ella.
