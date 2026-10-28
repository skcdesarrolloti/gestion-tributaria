# Parámetros por año y explicación de retenciones

Un Socio activo abre **Configuración → Parámetros anuales** desde el menú.
Puede consultar 2025/2026, preparar un año nuevo o publicar otra versión del año
actual. Los años ya terminados quedan en consulta. Los terceros no pueden entrar
ni escribir aunque conozcan la dirección. Las cuentas siguen siendo independientes.

## Qué se puede actualizar

- UVT en pesos enteros y dirección de la resolución oficial DIAN.
- Siete rangos laborales: límite superior en UVT, tarifa marginal y fijo en UVT.
- Topes mensuales de vivienda, prepagada y dependientes; porcentajes aplicables.
- Exención laboral y límite conjunto: porcentajes y topes anuales en UVT.
- Referencia de las normas que respaldan los valores.

No se estima la UVT futura. Un año nuevo deja UVT y fuentes vacías y muestra los
rangos de 2026 solo como referencia pendiente de contraste. Publicar exige una
confirmación y fuente DIAN HTTPS; no significa que la aplicación haya verificado
automáticamente la validez jurídica del dato digitado. Si cambia la estructura
de los siete rangos o aparecen porcentajes fraccionarios, se debe adaptar el motor.

La UVT configurada alimenta las consultas que usan BeneficioCatalog::year; los
rangos y topes configurados alimentan la retención mensual y la simulación P1.
No cambia automáticamente otras reglas del impuesto anual, el salario mínimo,
la nómina pagada ni los formularios. Preparar parámetros futuros no habilita
por sí solo otro año en Registro de información, que conserva sus años verificados.

## Historia y concurrencia

La migración 028 crea parametros_anuales en la base propia. Guarda nuevas filas
por año/revisión, autor y fecha: no reemplaza ni borra versiones. El modelo
comprueba la revisión y usa un índice único para rechazar publicaciones concurrentes.
Las versiones iniciales 2025/2026 siguen consultables desde la pantalla y Git.
No hay migración de datos reales ni edición de scripts antiguos.

La firma de los parámetros se registra al confirmar una revisión mensual o una
hipótesis de reserva. Al cambiar la versión, las revisiones anteriores quedan
pendientes; también se rechaza un formulario abierto con la firma anterior.
Nóminas, importes originales, documentos y resultados históricos guardados no
se sobrescriben. Las simulaciones de consulta sí usan los parámetros vigentes
del año, mostrando la versión. Otro año conserva sus propios valores.

## Explicación dentro del aplicativo

En **Validar retención mensual → Ver simulación provisional P1**, cada mes tiene
**Ver cuenta provisional**. Muestra una tabla con ingresos importados, aportes,
valor después de aportes, beneficios aplicados y base. Añade salario/comisiones
y salud/pensión/solidaridad del archivo, porcentaje de beneficio, UVT, tramo,
excedente, tarifa, componente fijo, operación en pesos y redondeo. La misma
presentación se usa en una validación confirmada de procedimiento 1.
No transforma una simulación en un hecho ni marca OK por consultar la cuenta.

Ejemplo ficticio de verificación aritmética (P1, sin deducciones adicionales):
devengado $10.000.000 menos aportes $800.000, beneficio $2.300.000 y base
$6.900.000; excedente sobre $4.975.530 por 19% = $365.649,30, redondeado $365.649.
Esta prueba no confirma el procedimiento real de una persona.

## Validación y reversión

Unitarias verifican parámetros inválidos, continuidad de rangos, motor realmente
afectado, firma vencida, separación por año y aritmética del desglose. Integración
en base aislada verifica migración, permisos, CSRF, valores vacíos del año nuevo,
publicaciones con historial, concurrencia y escape de las fuentes. No se publican
valores ficticios en producción para probar. Conservar la tabla y su historia al
revertir código; no borrar datos ni modificar la migración 028 ya aplicada.

Referencias contrastadas el 28/10/2026: ET 206.10/383/387/388; decreto 1625/2016,
art. 1.2.4.1.6; resoluciones DIAN 193/2024 y 238/2025. La fuente de cada nueva
publicación debe corresponder al año configurado.
