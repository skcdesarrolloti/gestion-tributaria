<?php
// Mantener las variables del lector bancario dentro de su propio ámbito.
(static function() use ($config,$taxpayer,$state,$pensionDocuments,$pensionDocument): void {
    $documents=$pensionDocuments;$selected=$pensionDocument;$year=$state['anio'];$errors=[];$pensionOnly=true;
    $form=['banco'=>$documents[0]['banco']??'BBVA','cuenta'=>$documents[0]['cuenta']??'','mes'=>'auto'];
    $extractQuery='&contribuyente='.(int)$taxpayer['id'].'&anio='.$year.'&origen=pensiones';
    $extractRoute='registro';$extractBrowseUrl=url($config,'registro').'&contribuyente='.(int)$taxpayer['id'].'&anio='.$year.'&componente=ingresos&cedula=pensiones';
    $months=array_unique(array_column(\App\Services\ExtractoData::monthlyDocuments($documents),'mes'));
?>
<section id="pensiones" class="space-y-space-md" aria-label="Pensiones y extractos guardados">
<div data-bank-count class="bg-primary-container rounded-xl p-4">
<p role="status"><?= count($documents).' '.(count($documents)===1?'PDF guardado':'PDF guardados').' · '.count($months).' '.(count($months)===1?'mes':'meses') ?> con soporte.</p>
<p>Abre un mes para consultar el PDF y sus movimientos. Los extractos se cargan en un único lugar.</p>
</div>
<?php if (input($_GET,'guardado')==='1'): ?><p role="status">Extracto guardado. Ya aparece en el control de meses; puedes abrirlo aquí.</p><?php endif; ?>
<?php if ($selected): require __DIR__.'/../extractos/detail.php'; endif; ?>
</section>
<?php })(); ?>
