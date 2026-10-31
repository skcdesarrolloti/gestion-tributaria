<?php
(static function() use ($config,$taxpayer,$state,$bankMode,$bankDocuments,$bankDocument,$bankSummary): void {
    $documents=$bankDocuments;$selected=$bankDocument;$year=$state['anio'];$errors=[];
    $form=['banco'=>'BBVA','cuenta'=>'','mes'=>'auto'];$gmf=$bankMode==='gmf';
    $extractQuery='&contribuyente='.(int)$taxpayer['id'].'&anio='.$year.'&origen='.($gmf?'costos':'capital');
    $extractBrowseUrl=url($config,'registro').\App\Services\RegistroNavegacion::query($state,(int)$taxpayer['id']);
    $extractRoute='registro';$extractState=$state;$extractReturnAnchor='bancos';
    $money=static fn($v)=>\App\Services\ExtractoData::money($v);
    $hasRead=array_sum(array_column($bankSummary['cuentas'],'leidos'))>0;
    $total=$bankSummary['totales'];$net=$total['gmf']-$total['gmf_reintegro'];
?>
<section id="bancos" class="bg-surface-container-lowest border border-surface-container rounded-xl p-4 space-y-space-md">
<h2 class="text-headline-md font-semibold"><?= $gmf?'4×1000 por banco':'Rendimientos financieros por banco' ?></h2>
<?php if ($gmf): require __DIR__.'/../extractos/access.php'; endif; ?>
<div data-bank-summary>
<?php if (!$documents): ?><p>No hay extractos guardados de este año.</p><?php else: ?>
<p><strong><?= $gmf?'4×1000 neto leído: ':'Rendimientos leídos: ' ?><?= $hasRead?e($money($gmf?$net:$total['rendimientos'])):'Pendiente de lectura' ?></strong><?php if ($bankSummary['pendientes']): ?> · <?= (int)$bankSummary['pendientes'] ?> PDF pendientes de lectura.<?php endif; ?></p>
<p class="text-secondary text-body-md">Es el control del banco. Las casillas fiscales siguen separadas para conciliar certificados y evitar duplicados.</p>
<?php foreach ($bankSummary['cuentas'] as $account): $sum=0; ?>
<details class="border border-surface-container rounded-xl p-3" <?= count($bankSummary['cuentas'])===1?'open':'' ?>>
<summary class="font-semibold"><?= e($account['banco'].' · Cuenta '.$account['cuenta']) ?> · <?= count($account['meses']) ?>/12 meses con PDF</summary>
<div class="renta-sheet-scroll"><table class="renta-sheet-table"><thead><tr><th>Mes</th><?php if ($gmf): ?><th>4×1000 cobrado</th><th>Reintegros</th><th>4×1000 neto</th><?php else: ?><th>Rendimientos abonados</th><?php endif; ?><th>Acumulado</th><th>Soporte</th></tr></thead><tbody>
<?php foreach (\App\Services\ExtractoData::MONTHS as $month=>$label): $entry=$account['meses'][$month]??null;$ok=$entry['leido']??false;$v=$entry['valores']??[];$period=$year.'-'.str_pad((string)$month,2,'0',STR_PAD_LEFT);
    $amount=$ok?($gmf?$v['gmf']-$v['gmf_reintegro']:$v['rendimientos']):0;if ($ok) { $sum+=$amount; } ?>
<tr><th scope="row"><?= e($label) ?></th><?php if ($gmf): ?><td><?= $ok?e($money($v['gmf'])):'—' ?></td><td><?= $ok?e($money($v['gmf_reintegro'])):'—' ?></td><?php endif; ?><td><?= $ok?e($money($amount)):'—' ?></td><td><?= $ok?e($money($sum)):'—' ?></td>
<td><?php if ($entry): ?><a class="text-secondary underline" href="<?= e($extractBrowseUrl.'&documento='.$entry['documento'].'&mes_movimientos='.$month.'#detalle') ?>"><?= $ok?'Ver PDF y movimientos':'PDF pendiente de lectura' ?></a><?php else: ?><?= $period===date('Y-m')?'Mes en curso':($period>date('Y-m')?'Por recibir':'Falta PDF') ?><?php endif; ?></td></tr>
<?php endforeach; ?></tbody><tfoot><tr><th scope="row">Total leído</th><td colspan="<?= $gmf?4:2 ?>"><?= $account['leidos']?e($money($sum)):'Pendiente de lectura' ?> · <?= (int)$account['leidos'] ?> meses leídos</td><td>Parcial si faltan meses</td></tr></tfoot></table></div>
</details><?php endforeach; ?>
<?php endif; ?>
<details><summary class="text-secondary underline">? · Cómo se usa este valor</summary>
<?php if ($gmf): ?><p>4×1000 neto = cobros menos reintegros. El 50% del valor efectivamente pagado puede ser deducible con certificación bancaria, sujeto a las reglas de tu declaración. No reduce el impuesto peso por peso ni se copia automáticamente como costo de arrendamientos.</p><p>Referencia de planeación, antes de límites: <?= e($money(intdiv(max(0,$net),2))) ?>. Se revisa su posible impacto en Análisis y acciones. Fundamento: ET 115 y 336.</p>
<?php else: ?><p>Se suman solo intereses y rendimientos identificados, no todas las consignaciones ni la devolución del capital invertido. Este control bancario no se vuelve a sumar automáticamente a la casilla 58/48: contrástalo con el certificado tributario y tus registros para evitar duplicados.</p><?php endif; ?>
<p>Los valores provienen de los PDF conciliados. «—» es falta de información, no cero. Se reconocen BBVA mensual, Caja Social Cuentamiga y Bancolombia Ahorros, incluidos sus formatos trimestrales disponibles. Otros diseños quedan pendientes. Los trimestres se separan por fecha de cada operación; no se dividen los totales entre tres.</p>
</details></div>
<?php if ($selected): require __DIR__.'/../extractos/detail.php'; endif; ?>
</section><?php })(); ?>
