<?php use App\Services\ExtractoData; ?>
<section id="meses" class="bg-surface-container-lowest border border-surface-container rounded-xl p-4 space-y-space-md">
<h2 class="text-headline-md font-semibold">Control de meses · <?= e($year) ?></h2>
<?php $pensionOnly=$pensionOnly??false; ?>
<p>El acumulado suma los meses leídos. Un mes sin PDF o pendiente de lectura no cuenta como cero.</p>
<details><summary class="text-secondary underline">? · Qué representa este acumulado</summary><p>La pensión recibida es el abono neto; falta contrastar bruto, salud y retenciones con su comprobante. Los importes aún no se trasladan a casillas DIAN.</p><p>Se acumula por cuenta y año. Al abrir un mes puedes consultar también los rendimientos, el 4×1000 y los demás movimientos originales del banco.</p></details>
<?php if (!$documents): ?><p>Aún no hay extractos de este año. Al guardar el primero verás enero a diciembre para esa cuenta.</p><?php endif; ?>
<?php $accounts=[];foreach ($documents as $d) { $accounts[$d['banco'].'|'.$d['cuenta']]=[$d['banco'],$d['cuenta']]; } ?>
<?php foreach ($accounts as [$bank,$account]): $calendar=ExtractoData::calendar($documents,$bank,$account);$received=count(array_filter($calendar));$sum=['pension'=>0,'rendimientos'=>0,'gmf'=>0,'gmf_reintegro'=>0];$read=0; ?>
<?php if ($bank==='BANCO CAJA SOCIAL'): require __DIR__.'/caja-months.php';continue;endif; ?>
<details <?= count($accounts)===1?'open':'' ?>>
<summary class="font-semibold"><?= e($bank.' · Cuenta terminada en '.$account) ?> · <?= $received ?>/12 meses con PDF</summary>
<div class="renta-sheet-scroll" role="region" aria-label="Meses de extractos de <?= e($bank.' '.$account) ?>" tabindex="0"><table class="renta-sheet-table">
<thead><tr><th scope="col">Mes</th><th scope="col">Estado del soporte</th><th scope="col">Pensión recibida (neta)</th><th scope="col">Pensión acumulada (neta)</th><?php if (!$pensionOnly): ?><th scope="col">Rendimientos abonados</th><th scope="col">Rendimientos acumulados</th><th scope="col">4×1000 neto de reintegros</th><?php endif; ?><th scope="col">Consultar</th></tr></thead>
<tbody><?php foreach ($calendar as $n=>$docs): $future=$year.'-'.str_pad((string)$n,2,'0',STR_PAD_LEFT)>date('Y-m');$doc=$docs[0]??null;$reading=$doc?ExtractoData::reading($doc):[];$ok=($reading['estado']??'')==='leido';
if ($ok) { $read++;foreach ($sum as $key=>$unused) { $sum[$key]+=$reading['totales'][$key]; } } ?>
<tr><th scope="row"><?= e(ExtractoData::MONTHS[$n]) ?></th>
<td><?= $doc?($ok?'Leído y conciliado · '.$reading['paginas'].' páginas':'PDF recibido · Lectura y clasificación pendientes'):($future?'Por recibir':'Falta PDF') ?></td>
<td><?= $ok?e(ExtractoData::money($reading['totales']['pension'])):'—' ?></td>
<td><?= $ok?e(ExtractoData::money($sum['pension'])):'—' ?></td>
<?php if (!$pensionOnly): ?><td><?= $ok?e(ExtractoData::money($reading['totales']['rendimientos'])):'—' ?></td>
<td><?= $ok?e(ExtractoData::money($sum['rendimientos'])):'—' ?></td>
<td><?= $ok?e(ExtractoData::money($reading['totales']['gmf']-$reading['totales']['gmf_reintegro'])):'—' ?></td><?php endif; ?>
<td><?php if ($doc): ?><a class="text-secondary underline" href="<?= e(($extractBrowseUrl??url($config,'extractos').$extractQuery).'&documento='.(int)$doc['id'].'#detalle') ?>">Ver movimientos y PDF aquí</a><?php else: ?><a class="text-secondary underline" href="<?= e(($extractBrowseUrl??url($config,'extractos').$extractQuery).'&subir=1#subir-extracto') ?>">Subir PDF</a><?php endif; ?></td></tr>
<?php endforeach; ?></tbody>
<?php if ($read): ?><tfoot><tr><th scope="row">Acumulado leído</th><td><?= $read ?> meses conciliados de <?= $received ?> recibidos</td><td><?= e(ExtractoData::money($sum['pension'])) ?></td><td><?= e(ExtractoData::money($sum['pension'])) ?></td><?php if (!$pensionOnly): ?><td><?= e(ExtractoData::money($sum['rendimientos'])) ?></td><td><?= e(ExtractoData::money($sum['rendimientos'])) ?></td><td><?= e(ExtractoData::money($sum['gmf']-$sum['gmf_reintegro'])) ?></td><?php endif; ?><td>Clasificación fiscal pendiente</td></tr></tfoot><?php endif; ?>
</table></div>
<?php if (!$pensionOnly): ?><p class="text-secondary text-body-md">— significa sin lectura, no cero. GMF debitado leído: <?= $read?e(ExtractoData::money($sum['gmf'])):'—' ?>; reintegros: <?= $read?e(ExtractoData::money($sum['gmf_reintegro'])):'—' ?>.</p><?php endif; ?>
</details>
<?php endforeach; ?>
</section>
