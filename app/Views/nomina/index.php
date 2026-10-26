<?php $title='Importar nómina laboral';require __DIR__.'/../partials/head.php'; ?>
<body class="bg-background min-h-screen font-body-md text-on-surface antialiased">
<?php require __DIR__.'/../partials/header.php'; ?>
<main class="portal-container py-8 space-y-space-lg">
<section class="bg-inverse-surface text-on-secondary rounded-2xl p-4"><h1 class="text-headline-lg font-semibold">Subir nómina · <?= e($year) ?></h1><p>Archivo → hojas y empleados → vista previa → confirmar.</p></section>
<form method="get" action="<?= e($config['base_path'].'/index.php') ?>"><input type="hidden" name="route" value="nomina"><label>Año <select name="anio" class="bg-surface-container-low rounded-xl p-2"><?php foreach (['2025','2026'] as $y): ?><option <?= $y===$year?'selected':'' ?>><?= e($y) ?></option><?php endforeach; ?></select></label> <button class="rounded-xl bg-inverse-surface text-on-secondary px-4 py-2">Abrir</button></form>
<?php foreach ($errors as $error): ?><p role="alert" class="bg-error-container text-on-error-container rounded-xl p-4"><?= e($error) ?></p><?php endforeach; ?>
<?php if ($notice): ?><p role="status" class="bg-primary-container rounded-xl p-4">Guardadas: <?= e($notice['guardadas']) ?>. Repetidas sin volver a sumar: <?= e($notice['duplicadas']) ?>. Abre el contribuyente en Registro → Ingresos → Trabajo para ver los meses y las casillas.</p><?php endif; ?>
<?php if ($preview): require __DIR__.'/preview.php'; endif; ?>
<section class="bg-surface-container-lowest border border-surface-container rounded-xl p-4 space-y-space-md">
<h2 class="text-headline-md font-semibold">Seleccionar archivo y períodos</h2>
<p>El Excel se lee en tu navegador. Solo se envían las filas elegidas de contribuyentes autorizados; no se guarda el libro completo ni la nómina de los demás empleados. No hay conexión con el sistema de funcionarios.</p>
<form data-nomina-upload method="post" action="<?= e(url($config,'nomina').'&anio='.$year) ?>" class="space-y-space-md">
<?php require __DIR__.'/../partials/csrf.php'; ?><input type="hidden" name="accion" value="revisar"><input type="hidden" name="filas" value="">
<label class="block">NIT del empleador, sin dígito de verificación <input name="empleador_documento" inputmode="numeric" pattern="[0-9]{5,20}" required maxlength="20" class="bg-surface-container-low rounded-xl p-2"></label>
<p class="text-secondary">Se propone si el nombre del empleador en la hoja coincide con una sociedad registrada. Confírmalo; este NIT identifica al pagador, no a los empleados.</p>
<label class="block">Nómina de un mismo empleador: XLS o XLSX (hasta seis archivos, 4 MB cada uno) <input type="file" accept=".xls,.xlsx" multiple required></label>
<p id="nomina-estado" role="status" aria-live="polite">Selecciona los archivos. Revisaremos las hojas internas, no solo el nombre del archivo.</p>
<fieldset id="nomina-seleccion" hidden class="space-y-space-sm"><legend class="font-semibold">Seleccionar personas y períodos</legend>
<div id="nomina-personas"></div>
<label>Períodos <select data-nomina-periodos class="bg-surface-container-low rounded-xl p-2"><option value="todos">Todos los períodos disponibles del año</option><option value="enero">Solo enero</option></select></label>
<p id="nomina-seleccion-estado" role="status" aria-live="polite"></p>
</fieldset>
<div class="renta-sheet-scroll" role="region" aria-label="Hojas y filas reconocidas" tabindex="0"><table class="renta-sheet-table"><thead><tr><th>Elegir</th><th>Contribuyente</th><th>Período</th><th>Origen</th><th>Devengado</th><th>Neto listado</th><th>Estado</th></tr></thead><tbody id="nomina-hojas"></tbody></table></div>
<button data-preview disabled class="rounded-xl bg-inverse-surface text-on-secondary px-4 py-2">Revisar selección antes de guardar</button>
<noscript><p>Activa JavaScript para leer el libro de nómina. No se puede importar sin revisar sus hojas.</p></noscript>
</form></section>
<details class="border border-surface-container rounded-xl p-4"><summary>? · Qué se importa y qué falta confirmar</summary>
<p>Adaptador del listado quincenal compartido: encabezado «DEL … AL … DE … DE …», identificación, salario liquidado, sobresueldo, retroactivos, incapacidad, auxilio, salud, pensión, retención, otros descuentos y neto. Las hojas de primas y seguridad social con otro diseño no se importan. «Básico» es referencia mensual: no se suma al salario liquidado. «Sobresueldo» se conserva así; no se convierte en comisión.</p>
<p>Identificación exacta, nunca por parecido del nombre. Si falta el perfil anual, abre Registro de información, selecciona la persona y prepara el año antes de importar. Las fechas se toman del encabezado de cada hoja. No se cambian meses ni se distribuyen cifras anuales.</p>
<p>La vista previa comprueba devengados, descuentos y neto con tolerancia de $1 por fila. Guarda los centavos del origen; los movimientos 32 y 33 se redondean a pesos por quincena. El redondeo final de la declaración a miles se revisará en el cálculo tributario. No se presupone que el neto del listado fue consignado.</p>
<p>La salud y pensión descontadas por este empleo alimentan 33; retenciones laborales se muestran aparte como aporte a 132. Otros descuentos no se vuelven beneficios automáticamente. No incluye pensiones recibidas ni la depuración final del impuesto.</p>
<p>Una repetición no vuelve a sumar. Una corrección requiere confirmación y conserva la versión anterior. No se permiten períodos superpuestos ni sumar automáticamente meses con captura laboral manual existente.</p>
</details>
<section class="space-y-space-sm"><h2 class="font-semibold">Abrir ingresos laborales por contribuyente</h2><?php foreach ($targets as $t): ?><p><a class="text-secondary underline" href="<?= e(url($config,'registro').'&contribuyente='.$t['id'].'&anio='.$year.'&componente=ingresos&cedula=general&renta=trabajo') ?>"><?= e($t['nombre']) ?></a><?= !$t['perfil']?' · Prepara su año':'' ?></p><?php endforeach; ?></section>
</main>
<script id="nomina-config" type="application/json"><?= json_encode(['targets'=>$targets,'employers'=>$employers,'year'=>$year],JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT|JSON_THROW_ON_ERROR) ?></script>
<?php foreach (['vendor/sheetjs/xlsx.full.min.js','js/nomina-parser.js','js/nomina-upload.js'] as $script): ?><script src="<?= e(asset($config,$script).'?v='.filemtime(dirname(__DIR__,3).'/public/assets/'.$script)) ?>" defer></script><?php endforeach; ?>
<?php require __DIR__.'/../partials/footer.php'; ?>
