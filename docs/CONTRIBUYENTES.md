# Contribuyentes · ficha única

El objetivo de este módulo es conocer a la persona, sociedad o sucesión que se va
 a analizar. Aquí se organiza su perfil; ingresos, costos y escenarios se trabajarán
por separado. No se presentan declaraciones ni se actualiza el RUT ante la DIAN.

## Recorrido

1. **Inicio → Ir a contribuyentes**.
2. En cada registro, pulsa **Abrir perfil**. Es la única entrada de consulta/edición;
   también funciona con los contribuyentes creados antes de disponer del RUT.
3. Pulsa **Subir o actualizar RUT**. Selecciona el PDF completo y pulsa
   **Subir RUT y completar perfil**.
4. El archivo se lee una sola vez: sus datos reconocidos completan campos vacíos y
   vuelves a la misma ficha. Los valores diferentes ya guardados se conservan.
5. Abre la pestaña que necesitas y corrige/completa los datos que no figuren en el
   documento. **Guardar información** guarda esa pestaña, sin borrar las demás.

Para una persona nueva, **Registrar contribuyente** permite subir el RUT antes de
crear la ficha o diligenciar la identificación manualmente. Si el RUT pertenece a
un registro propio existente, completa y abre ese perfil sin duplicarlo.

## Pestañas

| Información | Contenido |
| --- | --- |
| Identificación | Tipo, nombre/razón social, documento/NIT, DV y detalle de identificación del RUT; un solo guardado |
| Contacto | Dirección, país, municipio, correo y teléfonos |
| Actividades económicas | Principal, secundaria, otras, fechas, ocupación y establecimientos; catálogo y explicación del código seleccionado |
| Responsabilidades | Casilla 53 y códigos fuera de catálogo pendientes de verificar |
| Sociedad / constitución o situación personal / sucesión | Naturaleza, constitución, reforma, capital, vinculación, exterior y estados; grupos plegables |
| Representantes | Representantes legales, apoderados y representación sucesoral |
| Socios y participaciones | Socios/accionistas, capital, porcentaje, ingreso y retiro; en naturales se llama Participaciones |
| Junta directiva | Miembros de junta en su propio detalle |
| Contador y revisor | Profesionales, tarjeta, nombramiento y firma designada |
| Personas relacionadas | Cónyuge, dependientes y otras relaciones |
| Condiciones personales | Para naturales/sucesiones: vinculación, pensión, afiliaciones y soporte; sin liquidación de aportes |
| Establecimientos | Agencias, sucursales, oficinas, actividad, ubicación y matrícula |
| Aduanas | Códigos y datos aduaneros del RUT |
| Condiciones tributarias | Residencia, régimen y obligaciones informadas; sin calcular renta |
| RUT y documentos | Carga única del PDF y descarga privada de sus soportes |

Se muestra una pestaña a la vez, mediante enlaces que funcionan también sin JS.
Las ayudas se abren solo si se necesitan. Se retiraron la explicación repetida
«¿Qué es un contribuyente?», las tarjetas de trece bloques y las fuentes/importes de
 ingresos del recorrido. En Inicio y Contribuyentes la navegación se concentra en
esta ficha; no se eliminaron módulos ni datos anteriores de planeación o exógena.

El RUT no necesariamente contiene las participaciones de una persona natural en
otras empresas, sus dependientes, su condición pensional o todos sus hechos fiscales.
Esos vacíos se completan con sus documentos. La hoja 4 agrupa varias calidades: se
conserva «socio o junta, por precisar»; al elegir Junta en esa fila, pasa a su pestaña.
Ningún dato de relaciones concede por sí solo una deducción o beneficio.

## Datos y permisos

La ficha recuerda el último año guardado cuando el enlace no especifica uno. Puede
cambiarse el año en la cabecera; nunca se mezclan o copian snapshots sin instrucción.
Los bloques JSON históricos se reutilizan: no hace falta crear nuevamente personas,
importar SQL o borrar sus respuestas. La migración 017 conserva los soportes RUT.
Ver [IMPORTACION-RUT.md](IMPORTACION-RUT.md) para lectura de hojas 1–7 y límites.

Cada consulta y POST verifica sesión y funcionario propietario. No se pueden ver,
modificar o importar datos de otro funcionario mediante un ID o pestaña alterados.
El guardado exige CSRF, validación de campos y revisión vigente.

Las pestañas de representantes, socios, junta y profesionales usan el bloque
histórico `relacionados`, filtrado por rol. El servidor conserva los renglones de
las otras pestañas y verifica la revisión del bloque entero para evitar perder
cambios entre ventanas. El formulario no elimina datos ocultos al filtrar vacíos.

La identidad básica y el detalle RUT se guardan en una transacción: si el documento
se duplica o la revisión cambió, ambos quedan intactos. Las fuentes antiguas se
conservan, aunque ya no se capturen aquí. Los enlaces anteriores de Editar datos
redirigen a la pestaña Identificación. Las rutas históricas siguen disponibles para
compatibilidad, con los mismos controles de propietario.

## Continuidad y verificación

`PerfilContribuyente` define pestañas y separación de personas; `CaracterizacionController`
coordina la ficha; `Caracterizacion` guarda bloques y la identidad conjunta.
`RutAplicacion` reutiliza comprobación, conservación privada y transacción del PDF.
No modificar la configuración privada del servidor ni usar credenciales de producción
para pruebas. No hay cambios de esquema en esta reorganización.

Ejecutar `php bin/check.php`, `php tests/unit.php` y la integración aislada de
[VALIDACION.md](VALIDACION.md). Se comprueba ficha sin módulos ajenos, importación
directa de siete hojas, último año, separación de roles sin pérdida de personas,
rechazo de roles ajenos a la pestaña, conflicto de revisión y reversión de identidad.
Completar la revisión visual en escritorio/móvil y comprobar la sesión de producción.

Fuentes de las secciones del RUT:

- [DIAN · Secciones del RUT](https://www.dian.gov.co/impuestos/Documents/Secciones-RUT-DIAN.pdf).
- [DIAN · Formulario 001, hojas](https://www.dian.gov.co/Documents/RUT_1_4_0.pdf).
- [DIAN · Responsabilidades y aduanas](https://www.dian.gov.co/impuestos/RUT/Paginas/Responsabilidades-y-Usuarios-Aduaneros.aspx).
