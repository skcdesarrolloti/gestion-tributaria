# Importación del RUT · 21/10/2026

## Uso

1. En Contribuyentes, abre **Completar perfil tributario y RUT** y elige el año.
2. Pulsa **Subir RUT en PDF**, selecciona el archivo y **Leer PDF y mostrar datos**.
3. Abre cada grupo para comparar el valor leído con el dato actual. Desmarca los
   campos que no quieres importar. Los campos vacíos del PDF no borran información.
4. **Aplicar datos seleccionados al perfil** completa vacíos. Si deseas sustituir
   valores diferentes, marca expresamente la opción de reemplazo. Los grupos de
   responsabilidades/aduanas se reemplazan como grupo, no se suman silenciosamente.
5. Revisa los bloques modificados, que quedan en borrador. El PDF queda disponible
   como soporte privado en la misma pantalla de importación.
6. Abre el bloque y marca **Mostrar solo campos vacíos** para completar lo que falta.
   El filtro no elimina ni desactiva los valores guardados; al guardar se conservan.
   Un campo opcional vacío no necesariamente exige una respuesta.
   En personas relacionadas se ocultan los campos vacíos ajenos a la relación:
   un contador no necesita responder preguntas de dependientes o capital de socios.
   Cualquier valor ya guardado permanece disponible y no se borra por el filtro.

**Importado del RUT · pendiente de revisión** significa que los datos ya están
guardados y falta comprobar su correspondencia con el soporte. **Completo · pendiente
de revisión documental** reemplaza el texto ambiguo «Diligenciado · falta contraste».
La revisión se registra en Estado, soporte y notas del bloque; no obliga a digitar
nuevamente todos los valores ni equivale a una certificación de la DIAN.

No necesitas volver a crear el contribuyente. Primero debe coincidir su documento
básico con el del PDF; para NIT se comprueba también el dígito si está registrado.
El archivo no cambia el nombre ni documento básicos, las fuentes marcadas, el
perfil de Ingresos por año ni los cálculos tributarios existentes.

## Alcance y vigencia

Lee PDF digitales del formulario 001 en su disposición carta reconocida. Recorre
todas las páginas y reconoce la hoja por su encabezado, aunque falten hojas intermedias:

| Hoja | Datos que completa |
| --- | --- |
| 1 | Identificación, ubicación, actividades, responsabilidades y aduanas |
| 2 | Naturaleza, constitución, registro, reforma, capital, vinculación, exterior y estados |
| 3 | Representantes, documentos, fechas y firmas designadas |
| 4 | Socios/junta, documentos, nombres, nacionalidad, capital, participación y fechas |
| 5 | Revisor principal/suplente, contador, tarjeta, nombramiento y firma |
| 6 | Establecimientos, actividad, ubicación, matrícula, teléfono y cierre |
| 7 | Continuación de estados y beneficios 89–92 |

La hoja 4 agrupa varias calidades: el sistema conserva «socio o junta, por precisar»
para que el usuario elija la relación real. Los estados 89–92 no son una consulta
del estado actual de inscripción. La fecha 167 se guarda como matrícula, sin
suponer que sea apertura. Los campos de entidad se presentan en seis grupos plegables.

Cada hoja legible debe coincidir en NIT y número de formulario; una discrepancia
impide aplicar el PDF. Si no se logra identificar una hoja, se informa para revisión
sin inventar sus datos. Personas y establecimientos se incorporan por renglón:
se conservan los renglones manuales y se completan coincidencias sin duplicarlas.

Conserva los ceros iniciales. Usa catálogos locales versionados; un código
desconocido se informa para revisión. Hasta 5 MB y 10 páginas; escaneos, documentos
protegidos o diseños no reconocidos requieren transcripción manual. No se pueden
recuperar hojas ausentes del archivo: debe subirse el RUT completo para leerlas.
No se promete importar las 13 categorías de planeación desde el RUT.

La inscripción tiene vigencia indefinida. La antigüedad del trámite o de la copia
no significa vencimiento. Se muestran por separado generación del PDF y fecha del
trámite; no se deduce de ellas una renovación obligatoria. Fuente:
[DIAN · Conozca el RUT](https://www.dian.gov.co/impuestos/RUT/Paginas/Conozca-el-RUT.aspx).

La lectura valida identidad, estructura y NIT/DV; no autentica el documento ni
certifica el estado actual de la inscripción. El estado permanece **pendiente de
consulta oficial**. Abre [Consultas RUT DIAN](https://www.dian.gov.co/impuestos/RUT/Paginas/Consultas-RUT.aspx),
usa **Consulte su estado RUT** y registra el resultado y soporte en Identificación.
No se eluden controles de acceso o verificación del portal.

Residencia fiscal, régimen anual, dependientes, pensión, patrimonio y cuentas
exteriores requieren datos y revisión propios. Un código de responsabilidad no
concede automáticamente un beneficio ni determina las condiciones de otro año.

## Implementación y protección

`RutController` coordina carga, vista previa y aplicación; `RutPdfReader`,
`RutPdfForm`, `RutPdfDetails`, `RutPdfPeople` y `RutPdfGrid` leen las coordenadas;
`RutImportPlan` valida y compara, y `RutImportRows` conserva renglones manuales;
`RutDocumento` guarda documentos y bloques en una transacción. `RutUpload` recibe
solo cargas HTTP, comprueba tamaño, extensión, firma y MIME y usa nombres aleatorios.

La biblioteca local Smalot/PdfParser 2.13.1 está en `third_party/pdfparser`, con
licencia LGPL-3.0 y procedencia en NOTICE.md. No requiere Composer en el servidor;
usa las extensiones PHP mbstring, iconv, fileinfo y zlib. Los PDF se procesan en
el servidor de la aplicación, sin enviarse a servicios externos de IA. No se
ejecutan acciones o instrucciones contenidas en los documentos.

Sesión, propietario y CSRF se comprueban en el servidor. La vista previa está ligada
al contribuyente/año, vence en 30 minutos y se consume al aplicar. Un cambio de
revisión entre lectura y guardado produce conflicto, sin sobrescribirlo.
Los archivos se almacenan fuera de public en `storage/rut-importaciones`, con
nombres aleatorios y acceso por descarga autenticada. Los temporales abandonados
se limpian después de dos horas. Los soportes confirmados y sus metadatos son
privados y requieren respaldo; están excluidos de Git.

La migración 017 y su comprobación se aplican automáticamente sobre la base propia.
No modificar scripts aplicados ni reemplazar config/local.php del servidor.

## Validación

Ejecutar `php bin/check.php`, `php tests/unit.php` y `php tests/integration.php`
con la base aislada de docs/VALIDACION.md. Las pruebas usan PDF sintéticos: lectura,
ceros iniciales, fechas, identidad, preservación/reemplazo, CSRF, propietario,
descarga íntegra, deduplicación, siete hojas, roles ambiguos, vigencia de entidad
prolongada, coincidencia de identidad por página y conflicto entre ventanas. Nunca usar credenciales
de producción ni incorporar PDF personales a los fixtures.
