<p>UVT: <strong>$<?= e(number_format($historic['uvt'],0,',','.')) ?></strong>. <?= e($historic['norma']) ?></p>
<p class="text-body-md text-secondary">Fuente de la UVT: <?= e($historic['source']) ?></p>
<div class="renta-sheet-scroll" tabindex="0" role="region" aria-label="Rangos de esta versión"><table class="renta-sheet-table"><thead><tr><th>Desde (UVT)</th><th>Hasta (UVT)</th><th>Tarifa marginal</th><th>Fijo (UVT)</th></tr></thead><tbody>
<?php foreach ($historic['tramos'] as [$lower,$upper,$rate,$fixed]): ?><tr><td><?= $lower===0?'Desde 0':e('Más de '.$lower) ?></td><td><?= $upper===null?'En adelante':e($upper) ?></td><td><?= e($rate) ?>%</td><td><?= e($fixed) ?></td></tr><?php endforeach; ?>
</tbody></table></div>
<div class="renta-sheet-scroll" tabindex="0" role="region" aria-label="Topes de esta versión"><table class="renta-sheet-table"><thead><tr><th>Concepto</th><th>Valor</th></tr></thead><tbody>
<?php foreach (\App\Services\ParametrosAnuales::LIMITS as $key=>[$label,$default]): ?><tr><th scope="row"><?= e($label) ?></th><td><?= e($historic[$key]) ?><?= str_ends_with($key,'_pct')?'%':' UVT' ?></td></tr><?php endforeach; ?>
</tbody></table></div>
