# Extractos por mes y cuenta

Acceso: **Registro de información → Ingresos → Pensiones**. Elegir
contribuyente y año muestra de inmediato los PDF guardados, el control de meses
y los acumulados bancarios. Inicio también incluye ese acceso. La pestaña
Pensiones de Ingresos presenta la misma información, antes de las casillas
fiscales y las preguntas de otras fuentes, que quedan plegadas.

La tabla de Pensiones incluye **Pensión acumulada (neta)** junto al valor de
cada mes. **Ingresos → Capital** muestra los rendimientos por banco y cuenta;
**Costos y gastos** muestra el 4×1000 cobrado, reintegrado y neto. Cada control
reutiliza los mismos PDF y muestra los doce meses; no volver a subirlos en otra
categoría. Después de cargar o buscar, se conserva el contexto de origen.
La consulta bancaria general conserva todas las columnas. Se suman solo lecturas
conciliadas de esa cuenta y año. Un mes sin PDF o sin lectura no aporta cero:
su fila muestra «—» y el total disponible se identifica como parcial.
La casilla 99 muestra «Bruto pendiente de registrar» mientras no exista captura
real de pensión bruta. El neto bancario no se transforma en bruto ni se mezcla
con los rendimientos de capital. No cambia lecturas, movimientos o soportes.

Desde cada mes se abre el detalle y el PDF dentro de Pensiones; la búsqueda
conserva persona, año y sección. «Volver a los meses guardados» regresa al
calendario. «Subir PDF» en un mes faltante abre la carga; después de guardar
desde Pensiones se regresa al documento en esa misma sección. El lector, sus
permisos y las copias privadas son los mismos; no se duplican datos.

La carga está incluida en Capital y Costos y gastos; admite banco y últimos
cuatro dígitos de cada cuenta. Cada banco necesita sus propios extractos.
Los diseños sin lector se conservan pendientes con mes manual: no se consideran
importes de cero ni se promete lectura automática universal. Se descartan
duplicados por cuenta/mes y huella. Los reintegros reducen el GMF, sin contar
dos veces un soporte. El 50% es referencia deducible con certificado y reglas
aplicables, no un descuento directo del impuesto ni un costo automático de 60.
No exige volver a crear el contribuyente ni preparar un perfil de renta para
conservar el soporte. Permisos: Socio global; Usuario solo su ficha asignada.

La cantidad de PDF guardados y los abonos bancarios se muestran por separado
de las casillas fiscales. Un valor pendiente o $0 en casilla 99 no significa
ausencia de extractos: el abono neto todavía requiere conciliar bruto, salud y
retenciones con el comprobante del pagador. No se trasladan abonos netos como
pensión bruta automáticamente. Esta mejora de navegación no altera el esquema,
los documentos ni sus lecturas existentes.

Seleccionar año, banco y últimos cuatro dígitos de la cuenta. El mes se detecta
desde el PDF reconocido; también puede elegirse manualmente para un soporte
pendiente de lectura. Un año o mes elegido que contradiga el PDF se rechaza.
La revisión visual del titular, año y cuenta es opcional al cargar: sin marcar
la casilla, el PDF se guarda con esa revisión pendiente. Se registra el estado
en la lectura JSON, sin alterar soportes anteriores ni afirmar revisiones antiguas.
Las comprobaciones automáticas de identidad, período y cuenta siguen siendo
obligatorias cuando esos datos se reconocen, incluso si el detalle no concilia.
Cargar el PDF completo (máximo 10 MB, sujeto al
límite PHP del servidor). Las páginas del archivo original se conservan intactas.
Se almacenan el PDF, sus referencias y la lectura conciliada, sin conservar claves.
No se exige firma digital. «Preparar lectura automática» admite PDF con o sin
contraseña y utiliza QPDF WebAssembly 0.0.2 en un Worker del
propio sitio. Usa el documento de la ficha (sin DV) por defecto. «Usar otra
contraseña del PDF» habilita expresamente el campo alternativo, desactivado al
abrir la página para evitar que Chrome rellene una clave del aplicativo. La
clave no tiene nombre de envío, no pasa a
PHP ni se guarda en sesión, base, logs o almacenamiento del navegador. El worker
se termina tras cada intento y se borra el campo. Clave incorrecta: no se envía
el PDF ni se guarda el mes. No se adivinan claves ni se consultan servicios externos.

Se suben el original y una copia abierta para lectura/consulta, ambos privados y
con huellas. La referencia de consulta se guarda en el JSON existente; no cambia
el esquema. El visor abre esa copia sin pedir otra vez la clave. La comprobación
de saldos acredita coherencia de los datos leídos, no autenticidad bancaria de un
PDF: los soportes siguen sujetos a revisión. El servidor valida permisos, tipo,
tamaño y titular/período, y retira ambas copias ante un rechazo.

Cada cuenta muestra doce meses: leído y conciliado, PDF pendiente de lectura,
falta PDF o por recibir. Un mes sin lectura no equivale a cero. El formato
mensual BBVA Colombia reconocido lee todas las páginas y compara cada saldo,
el saldo final y los totales con el resumen. Se verificó el diseño con copias
legibles disponibles, sin copiarlas a Git ni a la base de pruebas. Usa enteros
de centavos para conciliar sin errores de coma flotante. Los ejemplos de prueba
versionados son sintéticos. El formato admitido por ahora es un PDF
mensual por cuenta; extractos trimestrales, cuentas de un mismo banco con igual
terminación y sustituciones de documentos requieren ampliar esa identificación.

El extracto será soporte común para abonos de pensión, rendimientos, GMF y otros
movimientos. No todo abono es ingreso ni todo débito es deducible. Conciliar la
pensión neta con el comprobante del pagador para recuperar bruto, salud y
retenciones. Transferencias propias, préstamos y devoluciones se clasifican por
separado. No duplicar nómina ni rendimientos entre extractos y certificados.
La tabla suma por cuenta y año la pensión neta recibida, rendimientos abonados
y GMF neto de reintegros; también informa GMF debitado y reintegros separados.
El detalle conserva fecha operación/valor, concepto original, cargo, abono,
saldo, página y categoría propuesta. Se busca por comercio/concepto sin salir.
Las devoluciones de compras y transferencias no se clasifican como pensiones.
La corrección explícita de GMF del formato reconocido se concilia como reintegro,
no como rendimiento. Se admiten «CORRECCION IMPTO DECRETO» y «ANU. COM DECRETO»;
se mantienen la conciliación exacta de cada saldo y los totales netos del resumen.
Si se reconocen identidad y período mensual, pero falla el detalle, se conserva
ese mes automáticamente y el PDF queda pendiente, sin importes parciales. El
mensaje diferencia esa situación de no poder leer el período. No se infiere el
mes del nombre del archivo. PDF no desbloqueado, escaneado, incompleto o desconocido queda
pendiente, sin importes parciales. No se afecta todavía ninguna casilla DIAN ni
el cálculo de deducciones: confirmar bruto/retenciones y soporte tributario antes
de hacer el traslado sin duplicar movimientos.

## Almacenamiento y continuidad

Migración/comprobación 026 crea extractos_bancarios, sin modificar datos previos.
027 agrega lectura JSON nullable para conservar compatibilidad con soportes
anteriores. Las referencias privadas de estos soportes siguen protegidas.
El soporte se guarda con nombre aleatorio en storage/extractos-bancarios, fuera
de public, denegado mediante .htaccess y excluido de Git. Respaldar esa carpeta
junto con la base. La descarga consulta permisos en cada solicitud, no publica
el nombre privado; permite incrustar el PDF solo en el propio origen.

La huella SHA-256 evita el mismo PDF renombrado, incluso con otro año. Un índice
adicional impide reemplazar silenciosamente la cuenta/mes ya guardada. Un error
revierte el registro y retira la nueva copia. Al revertir la interfaz conservar
tabla y archivos; nunca borrar soportes ni editar la migración aplicada.

Pruebas: tests/extractos-unit.php y tests/extractos-integration.php incluidos en
las suites generales. Usar exclusivamente PDF sintéticos y base aleatoria.
`node tests/extractos-pdf-unit.cjs` comprueba apertura AES-256, rechazo de clave
incorrecta y lectura conciliada de las dos páginas. Node solo se usa para probar;
Hostinger sirve los archivos estáticos del motor, con licencia y versión fijada.
