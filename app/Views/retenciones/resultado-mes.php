<p class="font-semibold" role="status"><?= e($result['estado']) ?><?= isset($result['diferencia']) && $result['estado']==='Revisar'?' · Diferencia $'.e(number_format($result['diferencia']/100,2,',','.')):'' ?></p>
<details><summary class="text-secondary underline">Observaciones</summary><p><?= e($result['motivo']) ?></p>
<?php if (isset($result['base'])): ?>
<p>Base gravable mensual calculada: $<?= e(number_format($result['base']/100,2,',','.')) ?>. Retención esperada: $<?= e(number_format($result['esperada']/100,2,',','.')) ?>. Retención registrada: $<?= e(number_format($result['real']/100,2,',','.')) ?>.</p>
<p>Deducciones antes de límites compartidos: $<?= e(number_format($result['deducciones']/100,2,',','.')) ?>. Exención del 25% antes del límite compartido: $<?= e(number_format($result['exencion25']/100,2,',','.')) ?>. Beneficios finalmente aplicados: $<?= e(number_format($result['beneficios']/100,2,',','.')) ?>.</p>
<p>Diferencia = registrada − esperada: positiva indica mayor retención; negativa, menor. «OK» admite hasta $1 de diferencia y depende de los soportes confirmados. No valida el impuesto anual.</p>
<p><?= e('Procedimiento '.$review['procedimiento'].' · '.($review['fecha']??'').' · '.$review['soporte']) ?></p>
<?php endif; ?></details>
