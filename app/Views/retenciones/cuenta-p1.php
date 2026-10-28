<?php $explainMoney=static fn($v)=>'$'.number_format($v/100,2,',','.'); ?>
<div class="space-y-space-sm" aria-label="Explicación paso a paso de la retención">
<p><strong>Primero calculamos sobre qué valor se aplica la tabla.</strong> Los ingresos y aportes vienen de las nóminas importadas del mes; no se calcula sobre el dinero consignado.</p>
<div class="renta-sheet-scroll" tabindex="0" role="region" aria-label="Pasos para obtener la base"><table class="renta-sheet-table"><thead><tr><th>Paso</th><th>De dónde sale</th><th>Valor</th></tr></thead><tbody>
<?php foreach ([['1. Ingresos antes de descuentos','Total devengado: salario y demás pagos importados.',$explain['bruto']],
    ['2. Aportes obligatorios a restar','Salud, pensión y solidaridad que aparecen en el archivo; no inventamos un aporte que no exista.',$explain['aportes']],
    ['3. Después de aportes','Ingresos menos aportes obligatorios.',$explain['neto']],
    ['4. Beneficios aplicados a restar','Deducciones y exención laboral, después de aplicar sus topes.',$explain['beneficios']],
    ['5. Base para la tabla','Valor del paso 3 menos el paso 4.',$explain['base']]] as [$step,$why,$amount]): ?>
<tr><th scope="row"><?= e($step) ?></th><td><?= e($why) ?></td><td><?= e($explainMoney($amount)) ?></td></tr><?php endforeach; ?></tbody></table></div>
<?php if ($explain['ingresos_detalle']): ?><p>Detalle del devengado del archivo: <?php foreach ($explain['ingresos_detalle'] as $concept=>$value): ?><?= e($concept.': '.$explainMoney($value)) ?>; <?php endforeach; ?></p><?php endif; ?>
<?php if ($explain['aportes_detalle']): ?><p>Detalle de aportes del archivo: <?php foreach ($explain['aportes_detalle'] as $concept=>$value): ?><?= e($concept.': '.$explainMoney($value)) ?>; <?php endforeach; ?>Un aporte en $0 no se agrega al cálculo.</p><?php endif; ?>
<p>El beneficio laboral se obtiene con el <?= e($explain['rules']['exencion_pct']) ?>% de lo que queda después de aportes y deducciones, limitado al cupo anual restante de <?= e($explain['rules']['exencion_uvt']) ?> UVT. En esta cuenta: deducciones antes del límite conjunto <?= e($explainMoney($explain['deducciones'])) ?>; exención antes del límite conjunto <?= e($explainMoney($explain['exencion'])) ?>. El beneficio finalmente aplicado es el del paso 4.</p>
<p><strong>Luego ubicamos esa base en la tabla de <?= e($explain['year']) ?>.</strong> Una UVT vale <?= e($explainMoney($explain['uvt'])) ?>. La base representa <?= e(number_format($explain['base']/$explain['uvt'],4,',','.')) ?> UVT.</p>
<p>Rango aplicado: <?= $explain['inferior']===0?'desde $0':e('más de '.$explainMoney($explain['inferior'])) ?><?= $explain['superior']===null?' en adelante':e(' hasta '.$explainMoney($explain['superior'])) ?>. Tarifa sobre el excedente: <?= e($explain['tarifa']) ?>%; componente fijo: <?= e($explainMoney($explain['fijo'])) ?>.</p>
<?php if ($explain['tarifa']===0): ?><p>La base está en el primer rango: en esta cuenta la retención es $0.</p><?php else: ?>
<p>Excedente = base <?= e($explainMoney($explain['base'])) ?> − límite inferior <?= e($explainMoney($explain['inferior'])) ?> = <strong><?= e($explainMoney($explain['excedente'])) ?></strong>.</p>
<p><strong><?= e($explainMoney($explain['excedente'])) ?> × <?= e($explain['tarifa']) ?>% + <?= e($explainMoney($explain['fijo'])) ?> = <?= e($explainMoney($explain['sin_redondear'])) ?></strong> antes del redondeo final. Ese porcentaje se aplica solo al excedente, no a todo el salario.</p>
<?php endif; ?>
<p>Retención calculada después del redondeo: <strong><?= e($explainMoney($explain['esperada'])) ?></strong>. Retención que figura en nómina: <strong><?= e($explainMoney($explain['real'])) ?></strong>.</p>
<p class="text-body-md text-secondary">Parámetros <?= e($explain['year']) ?> · versión <?= e($explain['rules']['revision']===0?'inicial':$explain['rules']['revision']) ?>. <?= e($explain['rules']['norma']) ?></p>
</div>
