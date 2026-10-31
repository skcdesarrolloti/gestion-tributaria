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
<p>La comparación provisional P1 parte del devengado total, incluido el sobresueldo, resta aportes obligatorios y aplica la exención laboral del 25% con los topes disponibles del año. A la base resultante se aplica la tabla de abajo. Supone pagos ordinarios, sin deducciones adicionales y redondeo al peso. No confirma todavía el procedimiento que usó el contador. Los otros descuentos personales no se restan automáticamente de esa base.</p>
<?php foreach ($retentionGroups as $group): if ($group['valores']['retroactivos']>0): $retro=$retentionControl['simulaciones'][(string)$group['documento']]['meses'][$group['mes']-1]['retroactivo']??null; ?>
<p><?= e(\App\Services\NominaPreview::MONTHS[$group['mes']-1]) ?>: retroactivo registrado $<?= e(number_format($group['valores']['retroactivos']/100,2,',','.')) ?>.
<?php if ($retro): ?>Compatible con aumento mensual de $<?= e(number_format($retro['anterior']/100,0,',','.')) ?> a $<?= e(number_format($retro['nuevo']/100,0,',','.')) ?>: <?= $retro['meses_previos'] ?> meses × $<?= e(number_format($retro['diferencia_mensual']/100,0,',','.')) ?> + $<?= e(number_format($retro['primera_quincena']/100,0,',','.')) ?> de la primera quincena. En la hipótesis P1, los meses reajustados y el mes del pago quedan en el rango de retención cero. El retroactivo permanece registrado en el mes pagado; no se mueve ni se suma otra vez. Esto permite continuar la comparación provisional, sin certificar el método del empleador.
<?php else: ?>Falta conocer los períodos y el tratamiento aplicado por el empleador. Ese mes y los posteriores quedan pendientes para no suponer el consumo de topes.<?php endif; ?> Fundamento: Oficio DIAN 27396 de 2019.</p><?php endif; endforeach; ?>
<?php $inlineTable=true;$yearRules=\App\Services\ParametrosAnuales::year((int)$profile['anio']);$result=[];$review=[];require __DIR__.'/../retenciones/tabla.php';unset($inlineTable); ?></details>
<details><summary class="text-secondary underline">Opciones para el contador y proyección anual</summary>
<p>No necesitas estas opciones para consultar la comparación de arriba. Permiten registrar el procedimiento y los soportes cuando el contador los confirme.</p>
<?php foreach ($retentionGroups as $g): ?><p><a class="text-secondary underline" href="<?= e(url($config,'retenciones').'&id='.$profile['id'].'&mes='.$g['mes'].'&empleador='.rawurlencode($g['documento'])) ?>"><?= e('Confirmar soportes · '.\App\Services\NominaPreview::MONTHS[$g['mes']-1].' · '.$g['empleador']) ?></a></p><?php endforeach; ?>
<a class="text-secondary underline" href="<?= e(url($config,'retenciones').'&id='.$profile['id'].'&vista=reserva') ?>">Consultar reserva anual estimada</a>
</details>
