# Recorrido de trabajo

Menú reorganizado el 22/10/2026 por instrucción del responsable:

1. Usuarios y accesos (solo Socios), antes de Contribuyentes.
2. Contribuyentes: abrir perfil, subir RUT, completar vacíos y guardar cada pestaña.
3. Beneficios tributarios: las 52 fichas existentes por categoría, con sus fuentes.
4. Registro de información: Ingresos / Costos y gastos, por contribuyente y año.
5. Análisis y acciones: seguimiento anual, hoja de cálculos y escenarios.

Inicio permanece como acceso general; Exógena es independiente y Mi perfil conserva
el cambio de contraseña. Los apartados de trabajo se numeran de 1 a 4 desde
Contribuyentes. No se elimina información ni se cambia la política Socio/Usuario.

## Registro de información

Ruta `registro`, atendida por RegistroController. Seleccionar contribuyente y
2025 o 2026. Solo se consultan fichas autorizadas con los modelos existentes.
Al abrir Ingresos o Costos y gastos desde una ficha, el menú conserva su
contribuyente y año. Registro no muestra el enlace para completar el RUT:
esa edición permanece en Contribuyentes, separada de la captura de valores.
Si falta el perfil fiscal del año, se enlaza su formulario existente con el año
preseleccionado. Guardarlo devuelve al registro. No se crean perfiles mediante GET
ni se copian valores entre años.

Para formulario 210 hay pestañas General, Pensiones y Dividendos y participaciones.
General contiene Trabajo, Servicios personales, Capital y No laborales. Formulario
110 conserva su propio catálogo sin presentar a las sociedades como cedulares.
La separación sigue la [explicación DIAN AG2025](https://micrositios.dian.gov.co/renta-personas-naturales-ag-2025/todo-lo-que-usted-debe-saber-sobre-renta/).

RegistroNavegacion filtra el catálogo existente: costos 45/60/77 para 210 y bloque
Costos y deducciones para 110; el resto de partidas no calculadas se muestra en
Ingresos. No se cambian reglas fiscales, fórmulas ni clasificación almacenada.
Los conceptos de servicios personales respetan el tratamiento del perfil.

Cada fila muestra sus valores reales y proyectados y ayuda desplegable de
definición, norma y soportes. Registrar valor reutiliza el formulario de movimientos
con su casilla seleccionada. Después de guardar regresa al contexto de origen;
editar desde el detalle plegable conserva también ese contexto.

**Actualización de ingresos:** captura por filas y sección, con ayudas plegadas.
Pensiones admite ingreso bruto 99; Dividendos separa 104, 107, 108 y 109.
Ganancias ocasionales se presenta como sección independiente (112 en 210,
80 en 110). No se habilitan costos genéricos en esas secciones. Las exenciones,
tarifas y depuraciones especiales no se calculan por el hecho de registrar un ingreso.
El 110 separa actividades ordinarias, financieros, dividendos (49–56 según el
tipo/condición), otros y ganancias ocasionales. Ver INGRESOS.md.

Cuando falta el perfil del año, sus condiciones se confirman en el mismo Registro;
el guardado reutiliza el controlador existente y vuelve al año y sección elegidos.
El formulario de movimiento limita las opciones a esa sección y rechaza por POST
una casilla de otra sección. No mezcla las operaciones de años distintos.

## Análisis y acciones

Ruta `analisis`, con la misma selección autorizada de contribuyente/año. Enlaza
Proyección anual y la hoja de escenarios existente cuando corresponde a persona
natural residente. La hoja mantiene valores independientes y no importa
automáticamente los movimientos del registro. Seguridad social sigue disponible
como revisión del aplicativo recibido. No se prometen acciones automatizadas.

La ampliación de ingresos utiliza migraciones 022/023. Probar sintaxis, unitarias y registro-integration.php
dentro de la integración completa: años separados, filtro de costos, casilla
preseleccionada, retorno después de guardar y rechazo de fichas ajenas.
