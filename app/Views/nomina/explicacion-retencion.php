<details data-retencion-inline><summary class="text-secondary underline">Explicar retención · <?= e(\App\Services\NominaPreview::MONTHS[$i-1]) ?></summary>
<?php $inlineMoney=static fn($v)=>'$'.number_format($v/100,2,',','.');foreach ($monthChecks as $check):
    $r=$check['resultado'];$g=$check['grupo'];$confirmed=$check['revision']; ?>
<p><strong><?= e($g['empleador']) ?>.</strong> <?= $check['provisional']?'Simulación provisional con procedimiento 1; falta confirmarlo con el contador.':'Revisión del mes.' ?></p>
<?php if (!isset($r['esperada'])): ?><p><?= e($r['motivo']) ?> No se considera que el valor pendiente sea cero.</p>
<?php else: ?>
<p>Te descontaron <strong><?= e($inlineMoney($r['real'])) ?></strong>. El cálculo <?= $check['provisional']?'provisional':'con los datos confirmados' ?> da <strong><?= e($inlineMoney($r['esperada'])) ?></strong>.
<?= abs($r['diferencia'])<=100?'Coinciden dentro de $1.':e('La nómina descontó '.$inlineMoney(abs($r['diferencia'])).($r['diferencia']<0?' de menos.':' de más.')) ?></p>
<p>Devengado <?= e($inlineMoney($g['valores']['devengado'])) ?> − aportes obligatorios <?= e($inlineMoney($g['valores']['aportes'])) ?> − beneficios admitidos <?= e($inlineMoney($r['beneficios'])) ?> = <strong>base <?= e($inlineMoney($r['base'])) ?></strong>. Los préstamos y otros descuentos personales no reducen automáticamente esta base.</p>
<?php if ($check['provisional'] || ($confirmed['procedimiento']??'')==='1'):
    $explain=\App\Services\RetencionDesglose::build($g['valores'],$r,(int)$profile['anio']); ?>
<p>UVT de <?= e($profile['anio']) ?>: <?= e($inlineMoney($explain['uvt'])) ?>. Tu base equivale a <?= e(number_format($explain['base']/$explain['uvt'],4,',','.')) ?> UVT.
La tabla del artículo 383 aplica <?= e($explain['tarifa']) ?>% al excedente de <?= e($inlineMoney($explain['inferior'])) ?> y suma <?= e($inlineMoney($explain['fijo'])) ?>:
<strong>(<?= e($inlineMoney($explain['base'])) ?> − <?= e($inlineMoney($explain['inferior'])) ?>) × <?= e($explain['tarifa']) ?>% + <?= e($inlineMoney($explain['fijo'])) ?></strong>, con redondeo final.</p>
<?php else: ?><p>Procedimiento 2 confirmado: base <?= e($inlineMoney($r['base'])) ?> × <?= e(number_format($confirmed['porcentaje']/100,2,',','.')) ?>%, con el redondeo indicado por el empleador.</p><?php endif; ?>
<?php if ($check['provisional']): ?><p>El beneficio simulado es la exención laboral del <?= e($explain['rules']['exencion_pct']) ?>% sobre lo ganado después de aportes, limitada por el cupo anual disponible. Supuestos: pagos ordinarios, sin dependientes, vivienda, prepagada, AFC ni aportes voluntarios y sin otros pagos que consuman los topes arrastrados desde enero. Esta diferencia no demuestra todavía un error del empleador.</p><?php endif; ?>
<?php endif; ?>
<?php endforeach; ?></details>
