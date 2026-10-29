<?php use App\Services\ExtractoData; ?>
<details open><summary class="font-semibold"><?= e($bank.' · Cuenta '.$account) ?> · <?= $received ?>/12 meses con soporte</summary>
<p>Un PDF trimestral conserva el documento completo y distribuye las operaciones por su fecha. Los abonos por revisar no se toman todavía como ingresos tuyos.</p>
<div class="renta-sheet-scroll"><table class="renta-sheet-table"><thead><tr><th>Mes</th><th>Abonos por revisar</th><th>Rendimientos</th><th>Salidas de la cuenta</th><th>4×1000 identificado</th><th>Estado / soporte</th></tr></thead><tbody>
<?php $cajaTotals=['otros_abonos'=>0,'rendimientos'=>0,'cargos'=>0,'gmf'=>0];$cajaRead=0;foreach ($calendar as $n=>$docs): $doc=$docs[0]??null;$r=$doc?ExtractoData::reading($doc):[];$ok=($r['estado']??'')==='leido';
    $v=$ok?['otros_abonos'=>$r['totales']['otros_abonos'],'rendimientos'=>$r['totales']['rendimientos'],'cargos'=>$r['resumen']['cargos'],'gmf'=>$r['totales']['gmf']-$r['totales']['gmf_reintegro']]:[];
    if ($ok) { $cajaRead++;foreach ($cajaTotals as $k=>$_) { $cajaTotals[$k]+=$v[$k]; } } ?>
<tr><th><?= e(ExtractoData::MONTHS[$n]) ?></th><?php foreach ($cajaTotals as $k=>$_): ?><td><?= $ok?e(ExtractoData::money($v[$k])):'—' ?></td><?php endforeach; ?><td><?php if ($doc): ?><a class="text-secondary underline" href="<?= e(($extractBrowseUrl??url($config,'extractos').$extractQuery).'&documento='.$doc['id'].'&mes_movimientos='.$n.'#detalle') ?>"><?= $ok?'Ver mes y PDF':'PDF pendiente de lectura' ?></a><?php else: ?><?= $year.'-'.str_pad((string)$n,2,'0',STR_PAD_LEFT)>date('Y-m')?'Por recibir':'Falta PDF' ?><?php endif; ?></td></tr>
<?php endforeach; ?></tbody><tfoot><tr><th>Acumulado leído</th><?php foreach ($cajaTotals as $v): ?><td><?= $cajaRead?e(ExtractoData::money($v)):'—' ?></td><?php endforeach; ?><td><?= $cajaRead ?> meses leídos</td></tr></tfoot></table></div>
<p class="text-secondary text-body-md">Salidas incluye compras, retiros y traslados: no equivale a gastos deducibles. El extracto de la cuenta no vuelve a sumar la nómina ni el informe inmobiliario. Los pagos de crédito requieren identificar su referencia y comprobar su desglose.</p>
</details>
