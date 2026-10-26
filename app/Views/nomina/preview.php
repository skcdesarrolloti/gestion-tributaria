<section class="bg-surface-container-lowest border border-surface-container rounded-xl p-4 space-y-space-md">
<h2 class="text-headline-md font-semibold">Vista previa · aún no guardada</h2>
<p>Comprueba empleado, empleador, quincena y valores. Cada fila es una quincena. La declaración acumula sus importes; no repitas estos valores como captura manual.</p>
<div class="renta-sheet-scroll" role="region" aria-label="Vista previa de nómina" tabindex="0"><table class="renta-sheet-table"><thead><tr><th>Persona y período</th><th>Pagador y origen</th><th>32 · Bruto</th><th>33 · Aportes</th><th>132 · Retención</th><th>Otros descuentos</th><th>Neto calculado</th><th>Neto listado</th><th>Estado</th></tr></thead><tbody>
<?php $corrections=false;foreach ($preview['records'] as $r): $a=\App\Services\NominaData::amounts($r['valores']);$corrections=$corrections||$r['estado']==='Corrección'; ?>
<tr><th scope="row"><?= e($r['nombre']) ?><br><?= e($r['inicio'].' a '.$r['fin']) ?></th><td><?= e($r['empleador'].' · NIT '.$r['empleador_documento']) ?><br><?= e($r['archivo'].' · '.$r['hoja']) ?></td>
<?php foreach (['devengado','aportes','retencion','otros_descuentos','neto_calculado','neto'] as $k): ?><td>$<?= e(number_format($a[$k]/100,2,',','.')) ?></td><?php endforeach; ?><td><?= e($r['estado']) ?></td></tr>
<?php endforeach; ?></tbody></table></div>
<details><summary class="text-secondary underline">? · Ver conceptos de cada quincena</summary><?php foreach ($preview['records'] as $r): ?><details><summary><?= e($r['nombre'].' · '.$r['inicio'].' · '.$r['hoja']) ?></summary><dl><?php foreach ($r['valores'] as $k=>$v): ?><dt><?= e(\App\Services\NominaData::LABELS[$k][0]) ?></dt><dd>$<?= e(number_format($v/100,2,',','.')) ?></dd><?php endforeach; ?></dl></details><?php endforeach; ?></details>
<form method="post" action="<?= e(url($config,'nomina').'&anio='.$year) ?>">
<?php require __DIR__.'/../partials/csrf.php'; ?><input type="hidden" name="accion" value="guardar"><input type="hidden" name="preview_token" value="<?= e($preview['token']) ?>">
<?php if ($corrections): ?><label class="block"><input type="checkbox" name="reemplazar" value="1" required> Confirmo reemplazar las quincenas marcadas «Corrección», conservando su historial y sin sumar ambas versiones.</label><?php endif; ?>
<button class="rounded-xl bg-inverse-surface text-on-secondary px-4 py-2">Confirmar importación</button>
</form></section>
