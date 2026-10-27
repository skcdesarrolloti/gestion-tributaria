# Extractos por mes y cuenta

Registro de información → elegir contribuyente y año → **Subir o consultar
extractos bancarios**. El mismo acceso aparece en Ingresos y en Costos y gastos.
No exige volver a crear el contribuyente ni preparar un perfil de renta para
conservar el soporte. Permisos: Socio global; Usuario solo su ficha asignada.

Seleccionar año, banco y últimos cuatro dígitos de la cuenta. El mes se detecta
desde el PDF reconocido; también puede elegirse manualmente para un soporte
pendiente de lectura. Un año o mes elegido que contradiga el PDF se rechaza.
Confirmar titular y período al cargar el PDF completo (máximo 10 MB, sujeto al
límite PHP del servidor). Las páginas del archivo original se conservan intactas.
Se almacenan el PDF, sus referencias y la lectura conciliada, sin pedir claves.
El visor permite consultarlo en la misma pantalla; un PDF cifrado puede pedir
su clave al navegador. No se guarda esa contraseña.

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
no como rendimiento. PDF cifrado, escaneado, incompleto o desconocido queda
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
