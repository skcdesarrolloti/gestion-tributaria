# Caja Social: una carga, consulta mensual

Acceso: Registro de información → Ingresos → Subir extractos bancarios · carga
única. El selector central conserva los PDF completos. Costos, capital y préstamos
consultan el mismo soporte; no ofrecen una segunda carga bancaria.

Lector v1: Extracto Individual / Cuenta de Ahorros Cuentamiga, con titular y cuenta,
período expresado en meses en español, resumen y columnas Fecha, Transacción,
Documento, Lugar, Débitos, Créditos y Saldos. Se validó el diseño de dos páginas
aportado por el responsable. No se asegura leer otras plantillas de Caja Social.

El lector reconoce meses completos de un mismo año, con hasta tres meses por PDF.
Reconstruye columnas mediante coordenadas, incorpora notas de continuación entre
páginas, exige orden de fechas y concilia cada saldo y los totales del período.
En este formato créditos incluye los intereses abonados; no añadirlos otra vez.
Si falla la conciliación queda pendiente sin publicar importes parciales.

Solo hay un registro en extractos_bancarios, con mes inicial y original privado.
El JSON versionado guarda inicio/fin, meses_cubiertos, movimientos completos y
resúmenes por mes. ExtractoData::monthlyDocuments crea vistas en memoria del mismo
ID; reading aplica el mes solicitado. No crea tres PDF ni tres filas. BancoResumen,
PensionResumen y PrestamoCruce reutilizan estas vistas sin repetir el trimestre.
El modelo bloquea al titular antes de guardar y rechaza solapamientos de cuenta,
año y banco, incluidos mensual frente a trimestral, además de hash repetido.
No requiere nuevas columnas ni migración; no cambia archivos históricos.

El detalle se filtra por mes sin cambiar los índices originales de movimientos;
la clasificación sigue usando documento/índice, no una numeración filtrada.
Los abonos genéricos, nómina, reembolsos y pagos a propietarios quedan por revisar.
No se duplican nóminas ni informes de arrendamientos. Solo conceptos explícitos de
pensión se proponen como pensión; mencionar un pagador no es suficiente. Intereses
de cuenta se separan como rendimientos; cargos inequívocos de GMF se identifican.
Un pago genérico por PSE o a una entidad financiera no determina préstamo, interés
ni deducibilidad: hace falta su referencia y el soporte del crédito.

Pruebas: caja-social-unit.php y caja-social-integration.php, incluidas en las suites.
Fixtures exclusivamente ficticios: tres meses, conservación de un ID, importes por
fecha, ausencia de ingreso presumido, saldo descuadrado, lectura HTTP, consulta
mensual, duplicados y solapamiento con un mes dentro del trimestre. El PDF real
solo se leyó localmente para validar el diseño; nunca se copia a Git ni a pruebas.
