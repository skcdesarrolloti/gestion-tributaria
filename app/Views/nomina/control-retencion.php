<?php foreach ($monthChecks as $check): $r=$check['resultado']; ?>
<p><?php if (count($monthChecks)>1): ?><?= e($check['grupo']['empleador']) ?>: <?php endif; ?>
<?php if (!isset($r['diferencia'])): ?>Pendiente
<?php elseif ($check['provisional'] && abs($r['diferencia'])<=100): ?>Coincide (provisional)
<?php elseif ($r['estado']==='OK'): ?>OK
<?php else: ?>$<?= e(number_format(abs($r['diferencia'])/100,2,',','.')) ?> <?= $r['diferencia']<0?'de menos':'de más' ?>
<?php endif; ?></p>
<?php endforeach; ?>
