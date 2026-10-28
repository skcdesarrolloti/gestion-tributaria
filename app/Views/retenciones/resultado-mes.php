<section id="comparacion-retencion" class="border border-surface-container rounded-xl p-4 space-y-space-sm" aria-label="Resultado de la validación mensual">
<h3 class="text-headline-md font-semibold">Resultado de la validación mensual</h3>
<p class="font-semibold" role="status"><?= e($result['estado']) ?><?= isset($result['diferencia']) && $result['estado']==='Revisar'?' · Diferencia $'.e(number_format($result['diferencia']/100,2,',','.')):'' ?></p>
<p>Retención descontada en nómina: <strong>$<?= e(number_format($group['valores']['retencion']/100,2,',','.')) ?></strong>.</p>
<?php if (isset($result['esperada'])): ?>
<p>Retención esperada con los datos confirmados: <strong>$<?= e(number_format($result['esperada']/100,2,',','.')) ?></strong>.</p>
<p><?= $result['estado']==='OK'?'Las cifras coinciden dentro de la tolerancia de $1.':($result['diferencia']<0?'Se descontó menos que el cálculo esperado.':'Se descontó más que el cálculo esperado.') ?> El resultado depende de los soportes confirmados y no certifica el impuesto anual.</p>
<?php else: ?><p><strong>Retención esperada: pendiente de confirmar.</strong> <?= e($result['motivo']) ?></p><?php endif; ?>
<details><summary class="text-secondary underline">Observaciones</summary><p><?= e($result['motivo']) ?></p>
<?php if (isset($result['base'])): ?>
<p>Base gravable mensual calculada: $<?= e(number_format($result['base']/100,2,',','.')) ?>. Retención esperada: $<?= e(number_format($result['esperada']/100,2,',','.')) ?>. Retención registrada: $<?= e(number_format($result['real']/100,2,',','.')) ?>.</p>
<p>Deducciones antes de límites compartidos: $<?= e(number_format($result['deducciones']/100,2,',','.')) ?>. Exención del <?= e($yearRules['exencion_pct']) ?>% antes del límite compartido: $<?= e(number_format($result['exencion25']/100,2,',','.')) ?>. Beneficios finalmente aplicados: $<?= e(number_format($result['beneficios']/100,2,',','.')) ?>.</p>
<p>Diferencia = registrada − esperada: positiva indica mayor retención; negativa, menor. «OK» admite hasta $1 de diferencia y depende de los soportes confirmados. No valida el impuesto anual.</p>
<p><?= e('Procedimiento '.$review['procedimiento'].' · '.($review['fecha']??'').' · '.$review['soporte']) ?></p>
<?php endif; ?></details>
<?php if (isset($result['base'])): ?><details><summary class="text-secondary underline">? · Ver la cuenta paso a paso</summary>
<?php if ($review['procedimiento']==='1'): $explain=\App\Services\RetencionDesglose::build($group['valores'],$result,(int)$profile['anio']);require __DIR__.'/cuenta-p1.php';else: ?>
<p>1. Lo ganado antes de descuentos: $<?= e(number_format($group['valores']['devengado']/100,2,',','.')) ?>.</p>
<p>2. Restamos aportes obligatorios: $<?= e(number_format($group['valores']['aportes']/100,2,',','.')) ?>.</p>
<p>3. Restamos beneficios admitidos después de aplicar sus límites: $<?= e(number_format($result['beneficios']/100,2,',','.')) ?>.</p>
<p>4. Queda la base de cálculo: $<?= e(number_format($result['base']/100,2,',','.')) ?>. Esta base no equivale al dinero que te consignaron.</p>
<p>5. <?= $review['procedimiento']==='1'?'Aplicamos la tabla mensual del artículo 383.':'Aplicamos el porcentaje fijo certificado: '.e(number_format($review['porcentaje']/100,2,',','.')).'%.' ?> Resultado: $<?= e(number_format($result['esperada']/100,2,',','.')) ?>.</p>
<p>6. Comparamos con el descuento real. Los préstamos y descuentos personales no se restan automáticamente de esta base.</p>
<?php endif; ?></details><?php endif; ?></section>
