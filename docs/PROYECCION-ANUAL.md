# Instrumento de seguimiento y proyección anual

Requisito confirmado el 14 de octubre de 2026: vigilar el año en curso y comparar
acciones antes del cierre. La declaración anterior sirve para conciliar y aprender;
no es la finalidad del instrumento ni se copia como pronóstico del año actual.

## Disponible en esta entrega

Desde Contribuyentes → Ingresos por año → Proyección anual, cada perfil tiene un
tablero privado alimentado por sus movimientos ya guardados. También se accede
desde el detalle de casillas. No se duplican datos ni se crean nuevas tablas.

- Resumen real, esperado adicional y cierre parcial (suma de ambos).
- Ingresos brutos y partidas que se restan del catálogo vigente, con resultado
  preliminar; no sumar de nuevo las casillas calculadas.
- Resumen mensual de ingresos brutos y número de registros de partidas. Un mes
  sin registros queda identificado, y un mes con registros no se presume completo.
- Aviso de proyecciones cuya fecha ya pasó: editar la partida existente, comprobar
  su soporte y cambiar a real cuando corresponda; no registrar una copia.
- Estructura visible de los bloques pendientes hasta llegar al impuesto, caja,
  comparación de alternativas y acciones.

No hay impuesto calculado, beneficios automáticos, escenarios guardados ni alertas
enviadas. El tablero se actualiza al consultarlo. No hay extrapolación silenciosa.
El formulario 210 aún no captura pensiones, dividendos, ECE o ganancias ocasionales;
el 110 tampoco cubre todas las casillas. Su resumen siempre se identifica como parcial.
Un valor desconocido debe ser pendiente, nunca cero confirmado.

## Flujo objetivo para una persona que no es contadora

1. Confirmar el año que quiere vigilar y el perfil, con explicación y soporte.
2. Preguntar qué fuentes recibe y guiar su captura mensual con lenguaje sencillo.
3. Preguntar qué pagos y situaciones pueden tener tratamiento fiscal (salud,
   dependientes, aportes, gastos de actividad, facturas electrónicas, etc.).
4. Traducir las respuestas a conceptos/casillas y mostrar definición, alcance,
   norma, vigencia, soporte y revisión pendiente debajo de la opción escogida.
5. Mostrar el escenario actual y alternativas explicadas antes de guardar elecciones.
   La elección costos/exención de servicios requiere comparación y revisión;
   el usuario no debe decidirla a ciegas. Este asistente todavía no se implementa.

## Modelo de la proyección a desarrollar

| Bloque | Datos y resultado | Control necesario |
|---|---|---|
| Perfil anual | Tipo, residencia, régimen, actividades RUT, formulario y reglas | Confirmación por año; no copiar automáticamente el año anterior |
| Ingresos | Fuente, actividad, mes, real/adicional, origen y soporte | Completar todas las fuentes y evitar dobles registros |
| Depuración | INCR, devoluciones, costos, exenciones, deducciones y compensaciones | Elegibilidad, límites individuales/globales e incompatibilidades |
| Impuesto | Bases por tratamiento, tarifa, descuentos e impuesto neto | Normativa y UVT del año gravable; redondeo y casillas verificadas |
| Caja | Retenciones, anticipos anteriores, saldo a favor y anticipo siguiente | Impuesto distinto del saldo por pagar o a favor |
| Alternativas | Escenario base y escenario con acción, cambio y ahorro | Comparar mismos ingresos y supuestos, justificar cada diferencia |
| Acciones | Acción, responsable, fecha, documentos, estado y costo | Acción realizada a tiempo y requisitos cumplidos, sin ahorro garantizado |
| UGPP | Ingresos/aportes mensuales y PILA | Separar base de aportes y base fiscal; validar por actividad y vinculación |

Cada escenario deberá guardar fecha de corte, versión de reglas, hipótesis y origen
de sus importes. Separar realizado, proyectado y pendiente de confirmar. El horizonte
es el 31 de diciembre del año seleccionado (revisar fracciones de año en sucesiones).
Si cambia un dato, recalcular y conservar historial; no sobreescribir el pasado como
si hubiera sido conocido entonces. Estas funciones de escenarios son pendientes.

La comparación deberá mostrar ahorro de impuesto, desembolso de la acción, efectos
en aportes y liquidez. No recomendar gastar una suma superior al ahorro solo para
reducir renta. Un beneficio que alcanza su límite puede no producir ahorro adicional.
Más retención puede reducir el saldo a pagar, pero no reduce el impuesto por sí sola.

## Reglas antes de habilitar el cálculo

Construir y probar primero un alcance explícito del régimen ordinario de personas
naturales residentes, sin hacer que ese motor calcule empresas, no residentes,
SIMPLE o sucesiones con particularidades sin validar. Los perfiles existentes se
conservan; fuera del alcance se mostrará la razón de cálculo pendiente.

Confirmar casillas/versión y reglas de reconocimiento. Completar pensiones y demás
fuentes o confirmar su ausencia, depuración y beneficios, retenciones, anticipos y
descuentos. Tener patrimonio nacional/exterior y deudas para revisar obligaciones y
coherencia; la deuda no se deduce directamente del impuesto. No certificar una
declaración completa sin cubrir sus casillas. Probar topes, incompatibilidades,
casos negativos, tramos de tarifa, redondeos, titularidad y permisos con datos ficticios.

Referencias iniciales verificadas el 14/10/2026: [Estatuto Tributario](https://normograma.dian.gov.co/dian/compilacion/docs/estatuto_tributario.htm),
arts. 26–28, 107, 126-1, 126-4, 206, 240–241, 330–341, 387 y 807–810.
La [Resolución DIAN 238 de 2025](https://normograma.dian.gov.co/dian/compilacion/docs/resolucion_dian_0238_2025.htm)
fija UVT 2026 en $52.374; no utilizar la UVT 2025 para planear 2026.
Estas referencias no habilitan cálculos ni reemplazan la revisión particular.

## Comprobación

Abrir un perfil propio → Proyección anual → registrar una partida real o proyectada
→ volver al tablero y comprobar mes e importe. Editar una proyección para convertirla
en real: el cierre permanece igual si el importe no cambia. Intentar abrir un perfil
de otro funcionario debe devolver 404; sin sesión debe ir al login. Las pruebas
unitarias verifican agregación, exclusión de totales derivados y avisos de revisión.
