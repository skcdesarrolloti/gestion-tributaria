<?php
$monthChecks=[];
foreach (\App\Services\RetencionLaboral::groups($payroll) as $key=>$group) {
    if ($group['mes']!==$i) { continue; }
    $monthChecks[$key]=['grupo'=>$group,'resultado'=>\App\Services\RetencionLaboral::check($group,$retentionData['meses'][$key]??[],(int)$profile['anio'])];
}
$statuses=array_column(array_column($monthChecks,'resultado'),'estado');
$status=in_array('Pendiente de soporte',$statuses,true)?'Pendiente de soporte':(in_array('Revisar',$statuses,true)?'Revisar':'OK');
?>
<span><?= e($status) ?></span>
<?php if ($status!=='OK'): ?><details><summary class="text-secondary underline">Observaciones</summary>
<?php foreach ($monthChecks as $check): $r=$check['resultado'];$g=$check['grupo']; ?>
<p><?= e($g['empleador'].' · '.$r['estado'].': '.$r['motivo']) ?><?= isset($r['diferencia'])?' Diferencia $'.e(number_format($r['diferencia']/100,2,',','.')):'' ?></p>
<a class="text-secondary underline" href="<?= e(url($config,'retenciones').'&id='.$profile['id'].'&mes='.$i.'&empleador='.rawurlencode($g['documento'])) ?>">Revisar este empleador</a>
<?php endforeach; ?></details><?php endif; ?>
