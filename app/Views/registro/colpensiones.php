<?php if ($profile && $profile['formulario']==='210' && $profile['tipo']==='natural'): ?>
<section id="colpensiones" class="bg-surface-container-lowest border border-surface-container rounded-xl p-4 space-y-space-md"
 data-colpensiones data-api="<?= e(url($config,'pensiones/comprobante').'&perfil='.$profile['id']) ?>" data-year="<?= e($state['anio']) ?>">
<h2 class="text-headline-md font-semibold">Comprobante mensual de Colpensiones</h2>
<p>Sube el PDF del pagador: se leerán mes, pensión bruta y salud. Revisa el resultado aquí antes de llevarlo a 99 y 100.</p>
<form enctype="multipart/form-data" method="post" data-pension-upload data-documento="<?= e($taxpayer['documento_numero']) ?>" data-worker="<?= e(asset($config,'js/extracto-worker.js')) ?>" class="space-y-space-sm">
<?php require __DIR__.'/../partials/csrf.php'; ?>
<label class="block">Comprobante de Colpensiones en PDF · máximo 10 MB<input name="comprobante_pdf" type="file" required accept="application/pdf,.pdf" class="block w-full"></label>
<label class="block">Contraseña del PDF, solo si es distinta a tu documento<input type="password" data-pension-password autocomplete="new-password" class="w-full bg-surface-container-low rounded-xl p-2"></label>
<button type="submit" disabled class="rounded-xl bg-inverse-surface text-on-secondary px-4 py-2">Leer comprobante aquí</button><noscript>Activa JavaScript para leer y revisar sin salir de Pensiones.</noscript>
</form>
<p data-pension-status role="status" aria-live="polite"></p>
<div data-pension-preview hidden class="space-y-space-md"></div>
<div data-colpensiones-list><?php if ($pensionReceipts): ?><details><summary class="text-secondary underline">Comprobantes guardados (<?= count($pensionReceipts) ?>) · consultar o corregir</summary>
<?php foreach ($pensionReceipts as $receipt): ?><p><button type="button" class="text-secondary underline" data-pension-open="<?= (int)$receipt['id'] ?>"><?= e($receipt['titulo']) ?></button></p><?php endforeach; ?></details><?php endif; ?></div>
<details><summary class="text-secondary underline">? · Qué se guarda y qué no se supone</summary><p>El PDF original queda privado. Guardarlo no crea ingresos hasta confirmar los datos. Las correcciones conservan versiones y no duplican el mes. Un certificado anual sin detalle mensual no se reparte entre doce. Las libranzas y otros descuentos personales no se suman a salud.</p></details>
</section>
<?php endif; ?>
