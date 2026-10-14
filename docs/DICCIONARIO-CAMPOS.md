# Contrato de los campos tributarios

Requisito del responsable: conservar la estructura oficial DIAN y explicar cada
campo con respaldo normativo actualizado. Este documento define el formato del
diccionario futuro; no es todavía un catálogo completo de casillas ni un motor.

## Ficha obligatoria por concepto

| Dato | Contenido esperado |
|---|---|
| Identificador | Clave estable interna, independiente del número de casilla. |
| Nombre oficial | Denominación exacta del formulario e instructivo correspondiente. |
| Destino | Formulario, versión, año gravable, bloque, cédula y casilla. |
| Explicación | Qué representa, cómo diligenciarlo y un ejemplo sencillo. |
| Aplicabilidad | Contribuyente, residencia, régimen y condiciones. |
| Norma | Artículo del ET cuando corresponda; además reglamento, doctrina o jurisprudencia necesaria. |
| Fuente | Enlace oficial, fecha de consulta, vigencia y estado de revisión. |
| Datos de origen | Movimientos, activos, certificados y otros detalles que alimentan el campo. |
| Período y moneda | Fecha o mes, corte fiscal, importe original y conversión justificada. |
| Soportes | Documentos requeridos y pendientes; nunca credenciales privadas. |
| Regla | Capturado o calculado, fórmula, dependencias, límites y redondeo. |
| Validaciones | Consistencia, incompatibilidades y controles para evitar duplicados. |
| Trazabilidad | Importe real o proyectado, escenario, autor y fecha de revisión. |
| Estado | Implementado, pendiente o no aplicable con motivo. |

No inventar un artículo del ET si el concepto depende de otra ley o del instructivo.
En UGPP, citar las normas de seguridad social que correspondan. Una URL sin revisar
su contenido y vigencia no constituye una regla validada.

## Ejemplos iniciales de contenido

### Ingresos brutos

- Usar el nombre específico por fuente que indique el formulario seleccionado.
- Explicación: valor de los ingresos antes de restar las partidas que correspondan
  en su depuración; distinguirlo de consignaciones o transferencias entre cuentas.
- Fundamento inicial: ET art. 26, complementado por los arts. 27 o 28 y las reglas
  especiales del ingreso y del régimen. No equivale a una fórmula universal.
- Soportes por definir según fuente: certificados, facturas, contratos, extractos.
- Destino: asignar casilla después de verificar versión, perfil y tipo de ingreso.

### Devoluciones, rebajas y descuentos

- Conservar el nombre y ubicación del concepto en la versión del formulario.
- Explicación: devoluciones de operaciones, disminuciones del precio o descuentos
  comerciales que correspondan a los ingresos registrados.
- Fundamento inicial: ET art. 26 y el instructivo de la casilla aplicable.
- Ejemplo ilustrativo: ingreso de una venta por $1.000.000 y devolución soportada
  de $100.000; revisar $900.000 después de esa partida, antes de costos y otros ajustes.
- Vincular soporte y operación original; controlar su período y que no esté ya
  descontada del valor registrado. No confundirla con descuentos tributarios.

### Patrimonio y cuentas del exterior

- Vincular el detalle del activo con patrimonio bruto, sin registrar el mismo saldo
  dos veces. Separar patrimonio bruto, deudas y patrimonio líquido.
- Fundamento inicial: ET art. 261; revisar valoración por activo y moneda (arts.
  269 y 288 cuando correspondan) y obligación separada del art. 607.
- Registrar cortes diferentes para renta y activos en el exterior.

## Matriz de cobertura antes de liberar un formulario

Crear una fila por casilla con: nombre oficial, concepto interno, origen del dato,
fórmula, norma, aplicabilidad, estado y evidencia de validación. Los números de
casilla no deben reutilizarse sin verificar versiones. Los conceptos UGPP tendrán
su propia matriz mensual, relacionada con el detalle de ingresos y aportes.

Fuentes iniciales:

- [Estatuto Tributario](https://normograma.dian.gov.co/dian/compilacion/docs/estatuto_tributario.htm).
- [Formulario 210 e instructivo publicado en 2026](https://www.dian.gov.co/atencionciudadano/formulariosinstructivos/Formularios/2026/Formulario_210_2026.pdf).
