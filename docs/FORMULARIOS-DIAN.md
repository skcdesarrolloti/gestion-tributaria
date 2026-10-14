# Formularios y fuentes por tipo de contribuyente

Actualización: 14 de octubre de 2026. Referencia para continuar la planeación;
no equivale a una declaración lista para presentar ni a cobertura implementada.

## La selección necesita más que el tipo

| Perfil | Referencia de declaración |
|---|---|
| Persona natural residente, renta ordinaria | 210. |
| Persona natural no residente, cuando corresponde declarar renta | 110; revisar obligaciones especiales. |
| Persona jurídica en renta ordinaria o entidad obligada a ingresos y patrimonio | 110, con su tratamiento particular. |
| Sucesión ilíquida de causante residente al fallecer | 210. |
| Sucesión ilíquida de causante no residente al fallecer | 110. |
| Contribuyente válidamente inscrito en SIMPLE | 260 y obligaciones complementarias aplicables. |

Una sucesión ilíquida corresponde al patrimonio de una persona fallecida mientras
se completa su adjudicación. No es una sociedad en liquidación. Revisar causante,
representación, residencia al fallecer, período y titularidad de cada ingreso.

El perfil definitivo es por año, residencia y régimen; no se obtiene solamente de
seleccionar natural, jurídica o sucesión en el registro inicial.

## Corrección de la pantalla inicial

Las claves ya guardadas se conservan. No hay nuevas tablas ni reclasificación de
importes. Las opciones se presentan según el tipo y se validan en el servidor.

- Natural: fuentes personales y patrimoniales, incluidas salario y pensión.
- Jurídica: ventas/actividades ordinarias, servicios, arriendos, ingresos financieros,
  dividendos y otros. No ofrece salarios ni pensiones como ingresos recibidos.
- Sucesión: fuentes a verificar con el período del causante y de la sucesión;
  seleccionar una fuente no confirma que se pueda trasladar fiscalmente.

Servicios, comercio y arriendos son detalles de la actividad; no necesariamente
tienen una casilla independiente en el 110. La asignación al concepto oficial se
realiza durante el análisis, no duplicando un ingreso en varias casillas.

El cambio de tipo conserva temporalmente las selecciones en pantalla. Las fuentes
incompatibles se ocultan y no se envían al guardar; se advierte cuando alguna estaba
marcada. Al volver al tipo anterior antes de guardar reaparecen. Sin JavaScript,
se muestran todas y el servidor impide combinaciones inválidas.

## Matriz inicial de bloques a desarrollar

Numeración de referencia verificada en los formularios publicados en 2026 y en
las declaraciones AG 2025 aportadas. Confirmar versión al habilitar cada año.
Todos los bloques de importes que siguen están pendientes de implementación.

| Bloque | Formulario 110 | Formulario 210 |
|---|---|---|
| Datos del declarante | Identificación, razón social, actividad, correcciones y condiciones. | Identificación, actividad y correcciones. |
| Datos informativos | Nómina y aportes: 33–35. | Compras con factura electrónica: 28; datos adicionales al final. |
| Patrimonio | Desglose por activos, pasivos y totales: 36–46. | Bruto, deudas y líquido: 29–31; conservar detalle de activos en el sistema. |
| Ingresos y depuración | Actividades ordinarias 47, financieros 48, dividendos 49–56, otros 57; totales y reducciones 58–61. | Cédula general por fuentes y depuración: 32–98. |
| Costos y gastos | Costos 62, administración 63, distribución/ventas 64, financieros 65, otros 66, total 67. | Dentro de cada bloque de ingresos cuando procede; respetar imputación y límites. |
| Inversiones y renta | Inversiones 68–69; renta, pérdidas, compensaciones y bases 70–79. | Dentro de la depuración de las cédulas. |
| Pensiones y dividendos | Tratamientos específicos que correspondan; no replicar cédulas del 210. | Pensiones 99–103; dividendos 104–110; base agregada 111. |
| Ganancias ocasionales | 80–83. | 112–115. |
| Liquidación | Impuestos, descuentos y mecanismos específicos: 84–102. | Liquidación privada: 116–129. |
| Retenciones y anticipos | 103–110. | 130–133. |
| Saldos y otros datos | Saldos, sanciones y datos adicionales: 111–117. | Saldos/sanciones 134–137; datos adicionales 138–141. |
| Representación y pago | Firmas, contador/revisor, tarjeta profesional y pago total. | Firmas, representación y pago total. |

Esta matriz es por bloques. Antes de habilitar el formulario, completar una ficha
por CADA casilla con nombre exacto, explicación, ejemplo, fundamento aplicable,
soportes, fórmula y evidencia de pruebas, según DICCIONARIO-CAMPOS.md. Incluir
casillas especiales aunque sean no aplicables, con motivo. No inventar un artículo
del ET para campos que dependen del instructivo o de otra norma.

Referencias oficiales:

- [Formulario 110 e instructivo](https://www.dian.gov.co/atencionciudadano/formulariosinstructivos/Formularios/2026/Formulario_110_2026.pdf).
- [Formulario 210 e instructivo](https://www.dian.gov.co/atencionciudadano/formulariosinstructivos/Formularios/2026/Formulario_210_2026.pdf).
- [DIAN: sucesiones y residencia del causante](https://micrositios.dian.gov.co/renta-personas-naturales-ag-2025/todo-lo-que-usted-debe-saber-sobre-renta/).
- [DIAN: SIMPLE](https://micrositios.dian.gov.co/ruta-de-la-formalizacion/regimen-simple-de-tributacion/).

No guardar los PDF reales ni sus datos como fixtures del repositorio.
