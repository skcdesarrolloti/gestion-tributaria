# Consultar artículos dentro de las fichas

Cada ficha de Beneficios tributarios conserva su título, fundamento y explicación.
**Consultar artículos aquí** permanece cerrado inicialmente. Al desplegarlo ofrece
los artículos citados y cada uno se abre dentro de la misma tarjeta, con área de
lectura desplazable y botón Cerrar artículo. No abre pestañas ni otra página.
Solo un artículo de cada ficha queda abierto a la vez; el texto ya cargado se
reutiliza en la página. Los filtros, año, página y posición del catálogo se conservan.

## Textos y alcance

La colección versionada contiene 131 artículos distintos: 126 del Estatuto y cinco
de las leyes 361/1997, 1715/2014, 814/2003 y 2380/2024, consultados en el Normograma
DIAN. Todos los artículos citados del ET están disponibles; un rango 125-1 a 125-3
incluye también 125-2. Los numerales 336.4 o 206.10 abren su artículo completo,
no se confunden con artículos 4 o 10. La ficha conserva la referencia precisa.
La ampliación a Ingresos incluye artículos de año, residencia, régimen, servicios,
dividendos y ganancias ocasionales. Las ayudas de opciones muestran los artículos
de la opción seleccionada dentro del mismo cuadro; se actualizan al cambiarla.
Los términos «numeral» y «parágrafo» no crean artículos adicionales; los rangos
de artículos se expanden para poder consultar sus textos completos.
La mención de una ley modificatoria no crea un artículo nuevo si no cita su número.

Los JSON de resources/normas son archivos **generados** de texto legal público,
con fuente, fecha de consulta y huella de la descarga original. No son resúmenes
generados por IA. Se extraen párrafos y filas de tablas, conservando indicaciones
editoriales visibles y marcando el texto tachado como tal. Se omiten navegación,
scripts y ventanas ocultas de jurisprudencia/concordancias/legislación anterior.
No se copia la página oficial completa en un iframe.

La fecha de consulta no certifica vigencia para 2025 o 2026. El aviso del visor lo
explica y distingue las anotaciones entre signos <…>. Estos textos no cambian
el catálogo de beneficios, sus límites ni las reglas de cálculo de escenarios.
La URL de origen puede consultarse como texto dentro de Fuente de este texto.

## Funcionamiento y mantenimiento

NormaCatalog asigna las referencias de cada ficha y solo permite los identificadores
conocidos. BeneficioController::article exige sesión y devuelve texto JSON por
beneficios/articulo. La ruta no acepta URL externas o rutas de archivos del usuario.
En producción no hay descargas remotas: la lectura funciona con los textos locales.
El navegador usa textContent; nunca ejecuta HTML o scripts de la fuente.
Si falla la carga, muestra un mensaje y Reintentar dentro de la misma ficha.

Para actualizar: **php bin/actualizar-normas.php** localmente. Descarga solo los
documentos oficiales definidos, verifica encabezados y límites de artículos, y
reemplaza la colección después de procesarlos todos. Revisar contenido y diferencias
antes de commit/push; no actualizar fechas ni textos sin consultar nuevamente.
Si cambia la estructura del sitio fuente, el generador debe fallar para revisarla.

Comprobar php bin/check.php, php tests/unit.php e integración aislada. Las pruebas
comprueban cobertura, rangos, seguridad, final de artículo, tablas y consulta con
sesión. Revisar teclado, carga diferida, cierre, errores y área desplazable en móvil.
