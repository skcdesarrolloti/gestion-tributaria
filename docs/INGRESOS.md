# Ingresos: primera etapa operativa

## Cómo comprobar el flujo

1. Inicia sesión y entra a **Contribuyentes**.
2. En el registro que creaste, pulsa **Ingresos por año**.
3. Crea el perfil anual: año 2025 o 2026, residencia fiscal, régimen ordinario,
   tratamiento de servicios personales y referencia de la evidencia utilizada.
4. La aplicación determina 210 para persona natural o sucesión residente, y 110
   para persona jurídica o persona natural/sucesión no residente. En sucesiones
   la residencia indicada corresponde al causante al fallecer.
5. Abre las fichas para consultar explicación, artículos, ejemplo, soportes y fórmula.
6. Pulsa **Registrar movimiento** y selecciona una casilla capturable.
7. Registra fecha de reconocimiento fiscal, concepto, tercero, país, moneda original,
   importe original y valor fiscal revisado en COP. Si la moneda es COP, repite el
   mismo entero; no escribas puntos o comas como separadores de miles.
8. Marca **Real** o **Proyectado adicional**. Real exige referencia del soporte;
   una partida que se resta exige también norma particular y justificación.
9. Guarda y comprueba los totales por casilla. Para corregir, usa **Editar movimiento**.

El valor proyectado corresponde exclusivamente a lo adicional esperado. No repetir
allí el real acumulado. El escenario suma real y proyectado. Los importes fiscales
se registran en pesos enteros, hasta 12 dígitos por movimiento; el original admite
decimales con punto. No se calcula ni se impone una TRM de cierre para todos los casos.

## Alcance y límites visibles

- Primera depuración de 210: casillas 32–34, 43–46, 58–61 y 74–78, con captura
  solo de ingresos y partidas que se restan. Los resultados se calculan.
- Inicio de 110: 47, 48, 57, 59, 60 y 62–66; resultados 58, 61 y 67. Los totales
  de ingresos son parciales porque no incluyen las casillas de dividendos 49–56.
- Catálogo inicial de 29 fichas entre ambos formularios, referencia `2026-10`,
  basada en los instructivos publicados en 2026. El año gravable se guarda aparte.
- Las casillas 43–45 requieren opción de costos en el perfil del 210. Si el
  tratamiento está pendiente, revisar la clasificación antes de registrar servicios.
- Un perfil anual se fija al crear y no se cambia desde esta etapa. Revisar sus
  datos previamente; no crear otro contribuyente para ocultar un error del perfil.
- No se calcula impuesto, límite de exenciones personales, pérdida fiscal, UGPP,
  ganancias ocasionales, dividendos especiales ni liquidación final.
- Si una operación aritmética queda negativa, se muestra y se pide revisión.
  No se convierte automáticamente en una pérdida fiscal aceptada.
- Valores sin redondeo al millar para presentación DIAN; no son archivo de declaración.
- Se guardan referencias de soportes, no archivos adjuntos. Las referencias y
  justificaciones ingresadas no equivalen a aprobación de procedencia tributaria.
- Los datos de fallecimiento y representación detallada de sucesiones siguen
  en el alcance pendiente. SIMPLE y otros regímenes requieren su propio motor.

## Seguridad y estructura

Todos los accesos se filtran por el propietario autenticado del contribuyente,
incluidos perfil, movimientos y edición. El rol no abre registros ajenos.
Escrituras POST con CSRF, consultas preparadas y salida HTML escapada.
No se permite usar conceptos de otro formulario ni capturar resultados calculados.

`IngresoController` coordina, `Ingreso` consulta, `IngresoData` valida e
`IngresoSummary` agrega movimientos y aplica fórmulas del alcance inicial.
La base central de funcionarios conserva su acceso de solo lectura.

Migraciones nuevas: 004 crea catálogo, 005 lo carga, 006 crea perfiles anuales y
007 movimientos. Cada una incluye comprobación y se ejecuta automáticamente.
Conservar todas las migraciones históricas y config/local.php. Desplegar código,
migraciones, comprobaciones y CSS juntos; no importar SQL manualmente.

Reversión de código: conservar tablas, catálogo y registros. No eliminar datos;
una reversión de esquema requiere respaldo y un cambio posterior revisado.

## Validación

`php bin/check.php`, `php tests/unit.php` y `php tests/integration.php` en bases
aisladas. Incluyen fórmulas, separación real/proyectado, rechazo de conceptos o
años ajenos, privacidad entre cuentas, CSRF, escape, creación y corrección de
movimientos, justificación de costos y selección 210/110. Revisión en Edge a
1280px y 360px de perfil, fichas y captura, con datos ficticios.

Referencias normativas: ver [INGRESOS-DISENO.md](INGRESOS-DISENO.md).
