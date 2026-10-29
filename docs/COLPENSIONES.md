# Comprobantes mensuales dentro de Pensiones

En Registro de información → Ingresos → Pensiones, escoger persona y año.
«Leer comprobante aquí» conserva el PDF privado y muestra en el mismo panel el
mes, pensión bruta y salud. Se intenta abrir con el documento de la ficha o con
la contraseña indicada, exclusivamente en el navegador. No se almacena la clave.
La revisión del titular y los importes es obligatoria antes de confirmar.

Al confirmar se guardan juntos los movimientos reales 99 (bruto) y 100 (salud),
se actualizan el control mensual, los acumulados y una estimación parcial del
impuesto con salarios y pensiones registrados. El neto bancario se conserva como
referencia; no se suma de nuevo. El resumen no incorpora automáticamente otros
ingresos, beneficios ni retenciones pensionales ausentes del comprobante.

La lectura reconoce texto mensual con período, total devengado/pensión bruta y
aporte de salud. Un campo no leído queda vacío para revisar, nunca cero supuesto.
Salud cero exige confirmación explícita. Los certificados anuales sin desglose,
retroactivos, ajustes y solidaridad quedan para revisión particular, sin dividir
el total ni omitir partidas. PDF escaneados pueden requerir completar campos.
La detección se probó con PDF ficticios; falta validar el primer formato real de
Colpensiones que el responsable suba. No se promete leer cualquier diseño.

Un mismo PDF no se duplica en el perfil. Para corregir una mesada se abre el
comprobante y se marca la sustitución: conserva PDF e historial y actualiza los
movimientos existentes. Una ventana antigua no sobrescribe la versión nueva.
Si ya hay captura manual 99/100 en ese mes, se bloquea la importación para evitar
doble suma; revisar esos registros primero. Este importador cubre una mesada
ordinaria por mes, no múltiples pagadores simultáneos.

Migraciones 030/031 crean documentos privados y versiones de mesadas. Respaldar
base y storage/extractos-bancarios juntos. Nunca borrar las versiones para
resolver un error. Solo se consulta o modifica una ficha autorizada en servidor;
POST exige CSRF y autorización de edición. Los datos derivados se corrigen desde
su comprobante, no aisladamente en el formulario genérico de movimientos.

Pruebas: colpensiones-unit.php y colpensiones-integration.php, incluidas en las
suites generales. Usar base aleatoria y PDF ficticios; nunca datos de producción.
