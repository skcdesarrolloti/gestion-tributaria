<?php $tableUvt=\App\Services\BeneficioCatalog::year((string)$profile['anio'])['uvt'];
$tableBase=isset($result['base']) && ($review['procedimiento']??'')==='1'?$result['base']:null;
$money=static fn($cents)=>'$'.number_format($cents/100,0,',','.'); ?>
<section id="tabla-retenciones" class="bg-surface-container-lowest border border-surface-container rounded-xl p-4 space-y-space-md">
<h2 class="text-headline-md font-semibold">Qué tabla de retención aplica a mis salarios</h2>
<p>La referencia es el <strong>artículo 383 del Estatuto Tributario</strong>. Para <?= e($profile['anio']) ?>, una UVT equivale a <strong><?= e($money($tableUvt*100)) ?></strong>. UVT es una unidad que permite actualizar los rangos cada año.</p>
<p><strong>Procedimiento 1:</strong> se calcula la base del mes y se ubica en esta tabla. <strong>Procedimiento 2:</strong> se aplica a la base mensual un porcentaje fijo certificado para el semestre; la tabla interviene al determinar ese porcentaje con su información histórica.</p>
<?php if (isset($result['base'])): ?>
<p class="bg-primary-container rounded-xl p-3"><?php if (($review['procedimiento']??'')==='1'): ?>En el mes seleccionado confirmaste procedimiento 1. Tu base es <?= e($money($tableBase)) ?> (<?= e(number_format($tableBase/100/$tableUvt,4,',','.')) ?> UVT). El renglón aplicable está marcado «Tu rango».<?php else: ?>En el mes seleccionado confirmaste procedimiento 2: <?= e(number_format($review['porcentaje']/100,2,',','.')) ?>% sobre la base de <?= e($money($result['base'])) ?>. No se vuelve a aplicar directamente un tramo de esta tabla a ese mes.<?php endif; ?></p>
<?php elseif (empty($inlineTable)): ?><p class="bg-primary-container rounded-xl p-3">Todavía no está confirmado el procedimiento de este mes. El RUT y el listado de nómina no lo indican. Pide al contador el procedimiento 1 o 2 y su liquidación; si es 2, también el porcentaje del semestre. Por eso aún no marcamos un rango personal.</p><?php endif; ?>
<p><strong>La base no es el salario bruto ni el neto consignado.</strong> Primero se restan aportes y beneficios admitidos con sus límites. La tarifa marginal se aplica al excedente del límite inferior y luego se suma el componente fijo: no se multiplica todo el salario por ese porcentaje.</p>
<div class="renta-sheet-scroll" role="region" aria-label="Tabla de retención laboral artículo 383" tabindex="0"><table class="renta-sheet-table">
<thead><tr><th>Rango de base en UVT</th><th>Base mensual en pesos · <?= e($profile['anio']) ?></th><th>Tarifa marginal</th><th>Cálculo en pesos</th></tr></thead><tbody>
<?php foreach ($yearRules['tramos'] as [$lower,$upper,$rate,$fixed]):
    $active=$tableBase!==null && ($lower===0?$tableBase>=0:$tableBase>$lower*$tableUvt*100) && ($upper===null || $tableBase<=$upper*$tableUvt*100); ?>
<tr <?= $active?'class="bg-primary-container"':'' ?>><th scope="row"><?= $active?'Tu rango · ':'' ?><?= $lower===0?'De 0':e('Más de '.$lower) ?><?= $upper===null?' en adelante':e(' hasta '.$upper) ?></th>
<td><?= $lower===0?'De $0':e('Más de '.$money($lower*$tableUvt*100)) ?><?= $upper===null?' en adelante':e(' hasta '.$money($upper*$tableUvt*100)) ?></td>
<td><?= e($rate) ?>%</td><td><?= $rate===0?'$0':e('(Base − '.$money($lower*$tableUvt*100).') × '.$rate.'% + '.$money($fixed*$tableUvt*100)) ?></td></tr>
<?php endforeach; ?></tbody></table></div>
<p class="text-body-md text-secondary">Parámetros del año · versión <?= e($yearRules['revision']===0?'inicial':$yearRules['revision']) ?>. <?= e($yearRules['norma']) ?> Fuente UVT: <?= e($yearRules['source']) ?>. Esta es la tabla de retención laboral; el impuesto de la declaración anual se calcula por separado.</p>
<?php $item=['id'=>'','norm'=>'ET 383, 385 y 386'];require __DIR__.'/../beneficios/articles.php'; ?>
</section>
