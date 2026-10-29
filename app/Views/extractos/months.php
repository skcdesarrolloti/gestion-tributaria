<?php use App\Services\ExtractoData; ?>
<section id="meses" class="bg-surface-container-lowest border border-surface-container rounded-xl p-4 space-y-space-md">
<h2 class="text-headline-md font-semibold">Control de meses · <?= e($year) ?></h2>
<p>Acumulados bancarios de los PDF leídos y conciliados. La pensión recibida es el abono neto; falta contrastar bruto, salud y retenciones con su comprobante. Los importes aún no se trasladan a casillas DIAN.</p>
<p>Las columnas acumuladas suman lo leído desde enero hasta cada mes, por cuenta y año. Si falta un extracto o su lectura está pendiente, el acumulado es parcial: no suponemos que ese mes sea cero.</p>
<?php if (!$documents): ?><p>Aún no hay extractos de este año. Al guardar el primero verás enero a diciembre para esa cuenta.</p><?php endif; ?>
<?php $accounts=[];foreach ($documents as $d) { $accounts[$d['banco'].'|'.$d['cuenta']]=[$d['banco'],$d['cuenta']]; } ?>
<?php foreach ($accounts as [$bank,$account]): $calendar=ExtractoData::calendar($documents,$bank,$account);$received=count(array_filter($calendar));$sum=['pension'=>0,'rendimientos'=>0,'gmf'=>0,'gmf_reintegro'=>0];$read=0; ?>
<details <?= count($accounts)===1?'open':'' ?>>
<summary class="font-semibold"><?= e($bank.' · Cuenta terminada en '.$account) ?> · <?= $received ?>/12 meses con PDF</summary>
<div class="renta-sheet-scroll" role="region" aria-label="Meses de extractos de <?= e($bank.' '.$account) ?>" tabindex="0"><table class="renta-sheet-table">
<thead><tr><th scope="col">Mes</th><th scope="col">Estado del soporte</th><th scope="col">Pensión recibida (neta)</th><th scope="col">Pensión acumulada (neta)</th><th scope="col">Rendimientos abonados</th><th scope="col">Rendimientos acumulados</th><th scope="col">4×1000 neto de reintegros</th><th scope="col">Consultar</th></tr></thead>
<tbody><?php foreach ($calendar as $n=>$docs): $future=$year.'-'.str_pad((string)$n,2,'0',STR_PAD_LEFT)>date('Y-m');$doc=$docs[0]??null;$reading=$doc?ExtractoData::reading($doc):[];$ok=($reading['estado']??'')==='leido';
if ($ok) { $read++;foreach ($sum as $key=>$unused) { $sum[$key]+=$reading['totales'][$key]; } } ?>
<tr><th scope="row"><?= e(ExtractoData::MONTHS[$n]) ?></th>
<td><?= $doc?($ok?'Leído y conciliado · '.$reading['paginas'].' páginas':'PDF recibido · Lectura y clasificación pendientes'):($future?'Por recibir':'Falta PDF') ?></td>
<td><?= $ok?e(ExtractoData::money($reading['totales']['pension'])):'—' ?></td>
<td><?= $ok?e(ExtractoData::money($sum['pension'])):'—' ?></td>
<td><?= $ok?e(ExtractoData::money($reading['totales']['rendimientos'])):'—' ?></td>
<td><?= $ok?e(ExtractoData::money($sum['rendimientos'])):'—' ?></td>
<td><?= $ok?e(ExtractoData::money($reading['totales']['gmf']-$reading['totales']['gmf_reintegro'])):'—' ?></td>
<td><?php if ($doc): ?><a class="text-secondary underline" href="<?= e(($extractBrowseUrl??url($config,'extractos').$extractQuery).'&documento='.(int)$doc['id'].'#detalle') ?>">Ver movimientos y PDF aquí</a><?php else: ?><a class="text-secondary underline" href="<?= e(($extractBrowseUrl??url($config,'extractos').$extractQuery).'&subir=1#subir-extracto') ?>">Subir PDF</a><?php endif; ?></td></tr>
<?php endforeach; ?></tbody>
<?php if ($read): ?><tfoot><tr><th scope="row">Acumulado leído</th><td><?= $read ?> meses conciliados de <?= $received ?> recibidos</td><td><?= e(ExtractoData::money($sum['pension'])) ?></td><td><?= e(ExtractoData::money($sum['pension'])) ?></td><td><?= e(ExtractoData::money($sum['rendimientos'])) ?></td><td><?= e(ExtractoData::money($sum['rendimientos'])) ?></td><td><?= e(ExtractoData::money($sum['gmf']-$sum['gmf_reintegro'])) ?></td><td>Clasificación fiscal pendiente</td></tr></tfoot><?php endif; ?>
</table></div>
<p class="text-secondary text-body-md">— significa sin lectura, no cero. GMF debitado leído: <?= $read?e(ExtractoData::money($sum['gmf'])):'—' ?>; reintegros: <?= $read?e(ExtractoData::money($sum['gmf_reintegro'])):'—' ?>.</p>
</details>
<?php endforeach; ?>
</section>
