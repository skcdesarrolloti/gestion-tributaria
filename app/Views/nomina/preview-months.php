<?php $groups=\App\Services\NominaPreview::groups($preview['records']); ?>
<?php foreach ($groups as $group): ?>
<section class="space-y-space-sm">
<h3 class="font-semibold"><?= e($group['nombre']) ?></h3>
<p class="text-secondary"><?= e($group['empleador'].' · NIT '.$group['nit']) ?></p>
<?php foreach ($group['meses'] as $month): ?>
<details class="border border-surface-container rounded-xl p-4" open>
<summary class="font-semibold"><?= e($month['titulo']) ?> · <?= count($month['filas']) ?> quincena(s) seleccionada(s)</summary>
<div class="renta-sheet-scroll" role="region" aria-label="<?= e('Quincenas de '.$month['titulo'].' · '.$group['nombre']) ?>" tabindex="0">
<table class="renta-sheet-table"><thead><tr><th>Quincena</th>
<?php foreach (\App\Services\NominaPreview::COLUMNS as $label): ?><th><?= e($label) ?></th><?php endforeach; ?>
<th>Estado</th></tr></thead><tbody>
<?php foreach ($month['filas'] as $r): ?>
<tr><th scope="row"><?= (int)substr($r['inicio'],8,2)<=15?'1.ª quincena':'2.ª quincena' ?><br><?= e(substr($r['inicio'],8,2).' al '.substr($r['fin'],8,2)) ?>
<details><summary class="text-secondary underline">Ver origen y detalle</summary>
<p><?= e($r['archivo'].' · Hoja: '.$r['hoja']) ?></p>
<?php if (isset($r['periodo_origen'])): ?><p>Período del encabezado original: <?= e($r['periodo_origen']) ?>. Fechas corregidas por el usuario: <?= e($r['inicio'].' a '.$r['fin']) ?>.</p><?php endif; ?>
<dl><?php foreach ($r['valores'] as $field=>$value): ?><dt><?= e(\App\Services\NominaData::LABELS[$field][0]) ?></dt><dd>$<?= e(number_format($value/100,2,',','.')) ?></dd><?php endforeach; ?></dl>
<p>Neto calculado: $<?= e(number_format(\App\Services\NominaData::amounts($r['valores'])['neto_calculado']/100,2,',','.')) ?></p>
<form method="post" action="<?= e(url($config,'nomina').'&anio='.$year) ?>">
<?php require __DIR__.'/../partials/csrf.php'; ?>
<input type="hidden" name="accion" value="ajustar_periodo"><input type="hidden" name="fila" value="<?= e($r['indice_preview']) ?>"><input type="hidden" name="preview_token" value="<?= e($preview['token']) ?>">
<label>Inicio de la quincena <input type="date" name="inicio" required value="<?= e($r['inicio']) ?>"></label>
<label>Fin de la quincena <input type="date" name="fin" required value="<?= e($r['fin']) ?>"></label>
<button class="rounded-xl bg-inverse-surface text-on-secondary px-4 py-2">Actualizar fechas de esta quincena</button>
<p>Corrige únicamente errores del encabezado. Los importes se conservan y aún no se guarda la nómina.</p>
</form>
</details></th>
<?php foreach (\App\Services\NominaPreview::COLUMNS as $field=>$label): ?><td>$<?= e(number_format($r['valores'][$field]/100,2,',','.')) ?></td><?php endforeach; ?>
<td><?= e($r['estado']) ?></td></tr>
<?php endforeach; ?>
<tr class="font-semibold"><th scope="row">Subtotal seleccionado</th>
<?php foreach ($month['totales'] as $value): ?><td>$<?= e(number_format($value/100,2,',','.')) ?></td><?php endforeach; ?><td></td></tr>
</tbody></table></div>
<p class="text-secondary">El subtotal resume las filas anteriores; no es otro pago. Solo se muestran las quincenas seleccionadas.</p>
</details>
<?php endforeach; ?></section>
<?php endforeach; ?>
