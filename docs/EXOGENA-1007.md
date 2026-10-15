# 1007 · Ingresos Recibidos

Estructura inicial para personas jurídicas, año gravable 2025, sin importar ni
precargar registros. Acceso: Exógena → Ingresos → 1007 · Ingresos Recibidos. El 5247 aparece
como Pagos o Abonos en cuenta; su alcance de contratos y sus campos se conservan.

## Archivo recibido y estructura utilizada

El archivo `1007.xls` es un libro binario XLS con hoja `1001`, cuyo encabezado
corresponde a movimientos de 2024. Contiene cuentas, nombre/NIT del tercero,
saldo anterior, débito, crédito y saldo actual, con columnas vacías y repetidas.
No es una plantilla de presentación del 1007. Se revisó su encabezado; no se
importaron sus filas ni se incorporaron datos reales al repositorio.

Se pidió confirmación al responsable y, mientras no responda, se adoptó la
estructura oficial de ingresos 1007 versión 9 para la captura vacía solicitada.
El auxiliar puede apoyar una carga posterior, tras confirmar año, cuentas,
conceptos, terceros y conciliación. No trasladar créditos o saldos automáticamente
a ingreso bruto; no usar datos 2024 para llenar el año 2025.

Nombre visible actualizado por solicitud del responsable: **Ingresos Recibidos**.
Referencia: [Resolución DIAN 162 de 2023](https://normograma.dian.gov.co/dian/compilacion/docs/resolucion_dian_0162_2023.htm),
artículo 22 y anexo del formato 1007 versión 9, y artículo 1.3.5.4.1 de la Resolución
227 de 2025. Fundamento: ET 631 literal f) y 631-2. Verificar catálogo e historial
normativo por período antes de exportar. Consulta inicial: 14/10/2026.

## Campos

Empresa y año se seleccionan aparte. Once campos de captura:

| Orden | Campo |
|---|---|
| A | Concepto |
| B | Tipo de documento |
| C | Número identificación |
| D | Primer apellido del informado |
| E | Segundo apellido del informado |
| F | Primer nombre del informado |
| G | Otros nombres del informado |
| H | Razón social informado |
| I | País de residencia o domicilio |
| J | Ingresos brutos recibidos |
| K | Devoluciones, rebajas y descuentos |

Las letras son orden de presentación de la captura, no códigos oficiales de casilla.
Cada campo tiene explicación debajo. No se asume país, concepto, identidad ni
importe. Los códigos se ingresan manualmente; un valor numéricamente válido no
acredita pertenencia al catálogo. Preservar identificaciones como texto. Valores
COP enteros, sin separadores, hasta 18 dígitos; cero debe registrarse expresamente.

## Arquitectura, seguridad y límites

- Migración/comprobación 010 crea `exogena_1007`, sin modificar tablas anteriores.
- `ExogenaRecords` reutiliza consultas preparadas y autorización por propietario
  para ambos formatos; las tablas provienen de una lista interna, nunca de la URL.
- `Exogena1007` y `Exogena5247` seleccionan sus tablas/diccionarios independientes.
- `ExogenaController` reutiliza el flujo con una instancia configurada por formato.
- `company-selector.php` comparte selección de empresa/año en ambos módulos.
- `Exogena1007Fields` y `Exogena1007Data` definen campos y validación de captura.
- Rutas propias GET de listado/edición y POST de guardado con sesión y CSRF.

Solo empresas propias como informantes; terceros pueden ser personas naturales o
entidades. Captura y edición de borradores, con JSON independiente por registro.
No hay importación masiva, XML, envío a DIAN ni cálculo de impuestos. Antes de
exportar, revisar obligación, reconocimiento, concepto/documentos/país, reglas de
cuantías menores, agrupación y unicidad del reporte por concepto/documento/identidad.
Esta entrega no agrupa ni certifica registros listos para presentar.

## Comprobación

Abrir un registro nuevo: año 2025 y once campos vacíos. Capturar datos ficticios,
guardar, abrir y corregir sin duplicar. Probar CSRF, campos vacíos, códigos y monto
inválidos, IDs inexistentes, otra empresa/propietario e informante natural. Comprobar
que editar el 1007 no altera el 5247 y que el 5247 mantiene su funcionamiento.
Ejecutar check, unitarias e integración aislada según VALIDACION.md. Revisar menú,
lista y captura a 1280px y 360px; confirmar producción tras publicar en main.
