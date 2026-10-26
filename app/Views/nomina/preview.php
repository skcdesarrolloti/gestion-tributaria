<section class="bg-surface-container-lowest border border-surface-container rounded-xl p-4 space-y-space-md">
<h2 class="text-headline-md font-semibold">Vista previa · aún no guardada</h2>
<p>Revisión por persona → mes → quincena. Salud, pensión y solidaridad componen la casilla 33. Retención corresponde a 132. El nombre del archivo no determina el mes: se usa el período de cada hoja.</p>
<?php $corrections=in_array('Corrección',array_column($preview['records'],'estado'),true);require __DIR__.'/preview-months.php'; ?>
<form method="post" action="<?= e(url($config,'nomina').'&anio='.$year) ?>">
<?php require __DIR__.'/../partials/csrf.php'; ?><input type="hidden" name="accion" value="guardar"><input type="hidden" name="preview_token" value="<?= e($preview['token']) ?>">
<?php if ($corrections): ?><label class="block"><input type="checkbox" name="reemplazar" value="1" required> Confirmo reemplazar las quincenas marcadas «Corrección», conservando su historial y sin sumar ambas versiones.</label><?php endif; ?>
<button class="rounded-xl bg-inverse-surface text-on-secondary px-4 py-2">Confirmar importación</button>
</form></section>
