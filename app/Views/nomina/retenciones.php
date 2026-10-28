<?php foreach ($retentionControl['simulaciones'] as $document=>$trial):
    $trialEmployer=current(array_filter($retentionGroups,static fn($g)=>(string)$g['documento']===(string)$document));
    $confirmedTotal=\App\Services\RetencionResumen::build($retentionGroups,$retentionData['meses']??[],(int)$profile['anio'],(string)$document);
    $inlineMoney=static fn($v)=>'$'.number_format($v/100,2,',','.');
    if ($confirmedTotal['revisados']): ?>
<p><strong><?= e($trialEmployer['empleador']) ?> · Acumulado de <?= (int)$confirmedTotal['revisados'] ?> meses con revisión:</strong>
esperado <?= e($inlineMoney($confirmedTotal['esperada'])) ?>; descontado en esos mismos meses <?= e($inlineMoney($confirmedTotal['real_comparable'])) ?>.
Diferencia <?= e($inlineMoney(abs($confirmedTotal['diferencia']))) ?> <?= $confirmedTotal['diferencia']<0?'de menos':($confirmedTotal['diferencia']>0?'de más':'· coinciden') ?>.
<?= $confirmedTotal['pendientes']?'Quedan '.(int)$confirmedTotal['pendientes'].' meses pendientes; este subtotal no representa todo el corte.':'' ?></p>
<?php elseif ($trial['calculados']): ?>
<p><strong><?= e($trialEmployer['empleador']) ?> · Acumulado provisional P1 hasta <?= e(\App\Services\NominaPreview::MONTHS[$trial['calculados']-1]) ?>:</strong>
calculado <?= e($inlineMoney($trial['simulada'])) ?>; descontado en esos mismos meses <?= e($inlineMoney($trial['real'])) ?>.
Diferencia <?= e($inlineMoney(abs($trial['diferencia']))) ?> <?= $trial['diferencia']<0?'de menos':($trial['diferencia']>0?'de más':'· coinciden') ?> en nómina. Solo incluye meses completos consecutivos desde enero; falta confirmar el procedimiento con el contador.</p>
<?php endif;endforeach; ?>
<details><summary class="text-secondary underline">Consultar tabla DIAN de retención · <?= e($profile['anio']) ?></summary>
<?php $inlineTable=true;$yearRules=\App\Services\ParametrosAnuales::year((int)$profile['anio']);$result=[];$review=[];require __DIR__.'/../retenciones/tabla.php';unset($inlineTable); ?></details>
<details><summary class="text-secondary underline">Opciones para el contador y proyección anual</summary>
<p>No necesitas estas opciones para consultar la comparación de arriba. Permiten registrar el procedimiento y los soportes cuando el contador los confirme.</p>
<?php foreach ($retentionGroups as $g): ?><p><a class="text-secondary underline" href="<?= e(url($config,'retenciones').'&id='.$profile['id'].'&mes='.$g['mes'].'&empleador='.rawurlencode($g['documento'])) ?>"><?= e('Confirmar soportes · '.\App\Services\NominaPreview::MONTHS[$g['mes']-1].' · '.$g['empleador']) ?></a></p><?php endforeach; ?>
<a class="text-secondary underline" href="<?= e(url($config,'retenciones').'&id='.$profile['id'].'&vista=reserva') ?>">Consultar reserva anual estimada</a>
</details>
