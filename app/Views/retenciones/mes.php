<section class="bg-surface-container-lowest border border-surface-container rounded-xl p-4 space-y-space-md">
<h2 class="text-headline-md font-semibold"><?= e(\App\Services\NominaPreview::MONTHS[$group['mes']-1].' · '.$group['empleador']) ?></h2>
<p>NIT <?= e($group['documento']) ?> · Devengado $<?= e(number_format($group['valores']['devengado']/100,2,',','.')) ?> · Aportes obligatorios $<?= e(number_format($group['valores']['aportes']/100,2,',','.')) ?> · Retenido $<?= e(number_format($group['valores']['retencion']/100,2,',','.')) ?></p>
<?php require __DIR__.'/resultado-mes.php'; ?>
<p>La nómina no informa el procedimiento ni los beneficios certificados. Solicita a quien liquida la nómina la depuración mensual y, si aplica, el porcentaje fijo del semestre.</p>
<?php if (!$group['completo'] || $group['valores']['retroactivos']>0): ?>
<p class="bg-primary-container rounded-xl p-4">Primero completa los períodos o revisa los retroactivos. No se valida un mes incompleto ni un pago especial con la tabla ordinaria.</p>
<?php elseif (!$readOnly): $values=$_SERVER['REQUEST_METHOD']==='POST'?$_POST:array_replace($review,array_map(static fn($v)=>(string)intdiv($v,100),array_intersect_key($review,\App\Services\RetencionDatos::MONEY))); ?>
<form method="post" class="space-y-space-md">
<?php require __DIR__.'/../partials/csrf.php'; ?>
<input type="hidden" name="revision" value="<?= e($revision) ?>"><input type="hidden" name="huella" value="<?= e($group['huella']) ?>">
<div class="grid md:grid-cols-2 gap-space-md">
<?php foreach (['procedimiento'=>['Procedimiento del empleador',['1'=>'1 · Tabla mensual del artículo 383','2'=>'2 · Porcentaje fijo certificado del semestre']],
    'dependientes'=>['Dependientes admisibles con soporte (art. 387)',['si'=>'Sí','no'=>'No']],
    'redondeo'=>['Redondeo utilizado por el empleador',['peso'=>'Al peso','miles'=>'Al millar']]] as $field=>[$label,$options]): ?>
<label><?= e($label) ?><select name="<?= e($field) ?>" required class="w-full rounded-xl bg-surface-container-low p-3"><option value="">Selecciona</option><?php foreach ($options as $value=>$text): ?><option value="<?= e($value) ?>" <?= (string)($values[$field]??'')===(string)$value?'selected':'' ?>><?= e($text) ?></option><?php endforeach; ?></select></label>
<?php endforeach; ?>
<label>Porcentaje fijo del semestre (solo procedimiento 2)<input name="porcentaje" inputmode="decimal" placeholder="Ejemplo: 7.25" value="<?= e($_SERVER['REQUEST_METHOD']==='POST'?input($_POST,'porcentaje'):(isset($review['porcentaje'])?number_format($review['porcentaje']/100,2,'.',''):'')) ?>" class="w-full rounded-xl bg-surface-container-low p-3"></label>
<?php foreach (\App\Services\RetencionDatos::MONEY as $field=>$label): ?>
<label><?= e($label) ?> (COP)<input name="<?= e($field) ?>" required inputmode="numeric" pattern="[0-9]+" value="<?= e($values[$field]??'') ?>" class="w-full rounded-xl bg-surface-container-low p-3"></label>
<?php endforeach; ?></div>
<details><summary class="text-secondary underline">? · Por qué se piden estos datos y qué incluye el control</summary>
<p>Vivienda: máximo 100 UVT por mes. Prepagada admisible: máximo 16 UVT por mes. Dependientes: 10% del ingreso bruto del mes, máximo 32 UVT. Se aplica la exención del 25% sobre la base después de aportes y deducciones, hasta el saldo anual de 790 UVT. El conjunto se limita al 40% y al saldo anual de 1.340 UVT. No dividimos estos topes anuales entre doce.</p>
<p>Los acumulados anteriores deben coincidir con la depuración certificada del empleador; no se deducen de meses sin importar. En enero son 0 si no hubo pagos anteriores del año. Si hay otros empleadores o traslados, pide que se revise su efecto en los límites. No incluyas préstamos ni descuentos personales como deducciones.</p>
<p>Procedimiento 2: comprobamos la aplicación del porcentaje certificado vigente para enero–junio o julio–diciembre. Su cálculo histórico requiere los pagos de los doce meses anteriores y una auditoría adicional: «OK» no certifica ese cálculo histórico.</p>
<p>Este control ordinario no incluye AFC, aportes voluntarios, cesantías, primas con tratamiento separado, indemnizaciones, retroactivos ni períodos parciales. Si existen, deja el mes pendiente y solicita la depuración particular.</p>
<?php $item=['id'=>'','norm'=>'ET 206.10, 383, 385, 386, 387 y 388'];require __DIR__.'/../beneficios/articles.php'; ?>
</details>
<label class="block">Referencia de los soportes y depuración revisados<textarea name="soporte" required minlength="10" maxlength="1000" class="w-full rounded-xl bg-surface-container-low p-3"><?= e($values['soporte']??'') ?></textarea></label>
<?php foreach (['completo'=>'Confirmé con el empleador que el mes contiene todos sus pagos.',
    'ordinario'=>'Son pagos ordinarios; no hay los conceptos especiales indicados en la ayuda.',
    'verificado'=>'Revisé soportes, acumulados anteriores, redondeo y procedimiento (incluido porcentaje vigente si es 2).'] as $field=>$label): ?>
<label class="block"><input type="checkbox" name="<?= e($field) ?>" value="1" required <?= $_SERVER['REQUEST_METHOD']==='POST' && !empty($values[$field])?'checked':'' ?>> <?= e($label) ?></label>
<?php endforeach; ?>
<button class="rounded-xl bg-inverse-surface text-on-secondary px-4 py-2">Guardar y comprobar retención</button>
</form><?php endif; ?>
</section>
