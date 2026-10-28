<?php foreach ($monthChecks as $check): $r=$check['resultado']; ?>
<p><?php if (count($monthChecks)>1): ?><?= e($check['grupo']['empleador']) ?>: <?php endif; ?>
<?php if (!isset($r['diferencia'])): ?>Pendiente · mes sin cálculo
<?php elseif ($check['provisional']): ?><?= abs($r['diferencia'])<=100?'Coincide':'Diferencia $'.e(number_format(abs($r['diferencia'])/100,2,',','.')) ?> · provisional P1
<?php elseif ($r['estado']==='OK'): ?>OK
<?php else: ?>Diferencia $<?= e(number_format(abs($r['diferencia'])/100,2,',','.')) ?>
<?php endif; ?></p>
<?php endforeach; ?>
