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

No necesitas volver a crear el contribuyente. Primero debe coincidir su documento
básico con el del PDF; para NIT se comprueba también el dígito si está registrado.
El archivo no cambia el nombre ni documento básicos, las fuentes marcadas, el
perfil de Ingresos por año ni los cálculos tributarios existentes.

## Alcance y vigencia

Lee PDF digitales del formulario 001 en su disposición carta reconocida: primera
hoja, identificación, ubicación, actividades y códigos de responsabilidades y
aduanas. Conserva los ceros iniciales. Usa catálogos locales versionados; un código
desconocido se informa para revisión. Hasta 5 MB y 10 páginas; escaneos, documentos
protegidos o diseños no reconocidos requieren transcripción manual. Las hojas
adicionales de representantes, entidades y establecimientos se revisan manualmente.
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
`RutPdfForm` y `RutPdfGrid` leen las coordenadas; `RutImportPlan` valida y compara;
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
descarga íntegra, deduplicación y conflicto entre ventanas. Nunca usar credenciales
de producción ni incorporar PDF personales a los fixtures.
