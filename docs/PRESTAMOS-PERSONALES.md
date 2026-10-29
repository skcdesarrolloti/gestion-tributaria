# Control de préstamos personales y libranza

Consulta: Registro de información → Costos y gastos → Control de préstamos. Conserva
persona y año. Este control admite personas naturales y extractos personales de
consumo de Caja Social; rechaza crédito comercial y titular distinto. No importa
ni traslada documentos de la empresa. El lector se verificó localmente contra el
formato personal aportado; las pruebas de escritura usan PDF y personas ficticias.

La carga está centralizada en «Extractos · carga única», junto a los PDF de
cuentas. El selector reconoce el crédito, valida el titular y lo guarda solo en
prestamos_documentos; no crea otra copia en extractos_bancarios. El formulario
propio se retiró de Préstamos; el POST anterior queda compatible para sesiones
antiguas. El control y la pantalla central comparten el soporte privado.

El pago anterior del PDF se asigna a su fecha efectiva. Capital, intereses,
seguro de vida, mora y otros cargos se separan y su suma debe conciliar. La cuota
siguiente y el saldo de deuda se conservan como referencia, no como pagos. Un
valor omitido queda null en la lectura. No hay creación automática de costos ni
casillas tributarias. El crédito no se interpreta como ingreso de pensión.

Un PDF repetido o un segundo documento del mismo corte/pago se rechaza sin
reemplazar el original. Para corregir un documento conflictivo se necesita revisar
la evidencia; esta primera versión no ofrece sustitución destructiva. Migración
032 crea prestamos_documentos. PDF originales y copias privadas se respaldan junto
con la base en storage/extractos-bancarios, reutilizando la protección existente.
Solo el titular autorizado o un Socio acceden al PDF; POST exige CSRF y edición.

Colpensiones conserva libranza y neto como campos opcionales en la versión de
mesada, separados de salud 100 y bruto 99. Vacío no significa cero. Se compara
el neto con la pensión leída de bancos y la libranza con el total de crédito pagado
del mes. Una igualdad no prueba por sí sola la relación con ese crédito; se exige
revisar pagador, préstamo y período. Hasta recibir Colpensiones queda pendiente.
Si el banco cobra después de consignar, no presumir que Colpensiones descontó la
cuota; el movimiento bancario y el extracto de crédito son soportes del mismo pago.
El comprobante real de Colpensiones sigue pendiente de validar en su primer uso.

En «Ver movimientos» del extracto bancario, otros cargos/abonos pueden clasificarse
como personal, actividad, traslado, préstamo, devolución o por revisar. La ayuda
cambia con la selección. Guarda en el bloque anual movimientos_bancarios de la
caracterización existente, con revisión optimista y clave documento/índice. No
altera el texto, importe ni categoría automática del banco y no afecta casillas
ni impuestos. PDF de banco inmutables mantienen estable ese índice. Las categorías
son orientativas, no certifican deducibilidad ni hacen una conciliación contable.

Pruebas: prestamos-unit.php y prestamos-integration.php incluidos en las suites
generales. Cubren lectura, próxima cuota, titular, empresa, descuadre, duplicados,
años, permisos, CSRF, versiones de clasificación y ausencia de movimientos fiscales.
