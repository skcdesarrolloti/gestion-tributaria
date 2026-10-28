<section class="bg-surface-container-lowest border border-surface-container rounded-xl p-4 space-y-space-md">
<h2 class="text-headline-md font-semibold"><?= e(\App\Services\NominaPreview::MONTHS[$group['mes']-1].' · '.$group['empleador']) ?></h2>
<?php require __DIR__.'/contexto-mes.php'; ?>
<?php require __DIR__.'/resultado-mes.php'; ?>
<?php require __DIR__.'/ejemplo-mes.php'; ?>
<?php if (!$group['completo'] || $group['valores']['retroactivos']>0): ?>
<p class="bg-primary-container rounded-xl p-4">Primero completa los períodos o revisa los retroactivos. No se valida un mes incompleto ni un pago especial con la tabla ordinaria.</p>
<?php elseif (!$readOnly): $values=$_SERVER['REQUEST_METHOD']==='POST'?$_POST:array_replace($review,array_map(static fn($v)=>(string)intdiv($v,100),array_intersect_key($review,\App\Services\RetencionDatos::MONEY))); ?>
<?php if ($_SERVER['REQUEST_METHOD']!=='POST' && $group['mes']===1) { $values+=['usado25'=>'0','usado1340'=>'0']; } ?>
<h3 class="text-headline-md font-semibold">2. Confirma los datos que no vienen en el archivo</h3>
<p>Cada campo tiene una explicación. Si no conoces un dato, elige «No lo sé todavía» o déjalo vacío: no uses 0 para salir del paso. En enero proponemos 0 en los acumulados anteriores porque no hay meses previos dentro del mismo año.</p>
<p class="text-label-sm text-secondary">Escribe los importes en pesos enteros, sin puntos, comas ni signo $. Ejemplo: para $125.000 escribe 125000. El porcentaje sí usa punto decimal: 7.25.</p>
<form method="post" class="space-y-space-md" data-retencion-form>
<p hidden data-retencion-unsaved role="status" class="bg-primary-container rounded-xl p-3">Los cambios de este formulario todavía no se han comparado. Pulsa «Guardar y comprobar retención» para actualizar el resultado; la comparación anterior, si existe, corresponde a los datos guardados.</p>
<?php require __DIR__.'/../partials/csrf.php'; ?>
<input type="hidden" name="revision" value="<?= e($revision) ?>"><input type="hidden" name="huella" value="<?= e($group['huella']) ?>">
<input type="hidden" name="parametros" value="<?= e(\App\Services\ParametrosAnuales::fingerprint((int)$profile['anio'])) ?>">
<div class="grid md:grid-cols-2 gap-space-md">
<?php foreach (['procedimiento'=>['Procedimiento del empleador',['1'=>'1 · Tabla mensual del artículo 383','2'=>'2 · Porcentaje fijo certificado del semestre']],
    'dependientes'=>['Dependientes admisibles con soporte (art. 387)',['si'=>'Sí','no'=>'No']],
    'redondeo'=>['Redondeo utilizado por el empleador',['peso'=>'Al peso','miles'=>'Al millar']]] as $field=>[$label,$options]): $options['pendiente']='No lo sé todavía'; ?>
<div><label><?= e($label) ?><select name="<?= e($field) ?>" required aria-describedby="ayuda-<?= e($field) ?>" class="w-full rounded-xl bg-surface-container-low p-3"><option value="">Selecciona</option><?php foreach ($options as $value=>$text): ?><option value="<?= e($value) ?>" <?= (string)($values[$field]??'')===(string)$value?'selected':'' ?>><?= e($text) ?></option><?php endforeach; ?></select></label><?php require __DIR__.'/ayuda-campo.php'; ?></div>
<?php endforeach; ?>
<div data-retencion-percentage><label>Porcentaje fijo del semestre (solo procedimiento 2)<input name="porcentaje" aria-describedby="ayuda-porcentaje" inputmode="decimal" placeholder="Ejemplo: 7.25" value="<?= e($_SERVER['REQUEST_METHOD']==='POST'?input($_POST,'porcentaje'):(isset($review['porcentaje'])?number_format($review['porcentaje']/100,2,'.',''):'')) ?>" class="w-full rounded-xl bg-surface-container-low p-3"></label><?php $field='porcentaje';require __DIR__.'/ayuda-campo.php'; ?></div>
<?php foreach (\App\Services\RetencionDatos::MONEY as $field=>$label): $label=str_replace(['25%','1.340'],[$yearRules['exencion_pct'].'%',number_format($yearRules['conjunto_uvt'],0,',','.')],$label); ?>
<div><label><?= e($label) ?> (COP)<input name="<?= e($field) ?>" required aria-describedby="ayuda-<?= e($field) ?>" inputmode="numeric" pattern="[0-9]+" value="<?= e($values[$field]??'') ?>" class="w-full rounded-xl bg-surface-container-low p-3"></label><?php require __DIR__.'/ayuda-campo.php'; ?></div>
<?php endforeach; ?></div>
<details><summary class="text-secondary underline">? · Por qué se piden estos datos y qué incluye el control</summary>
<?php $yearUvt=\App\Services\BeneficioCatalog::year((string)$profile['anio'])['uvt']; ?>
<p>Para <?= e($profile['anio']) ?>, una UVT equivale a $<?= e(number_format($yearUvt,0,',','.')) ?>. El sistema convierte los límites a pesos; tú registras los valores utilizados, no los topes de la norma.</p>
<p>Vivienda: máximo <?= e($yearRules['vivienda_uvt']) ?> UVT por mes. Prepagada admisible: máximo <?= e($yearRules['prepagada_uvt']) ?> UVT por mes. Dependientes: <?= e($yearRules['dependientes_pct']) ?>% del ingreso bruto del mes, máximo <?= e($yearRules['dependientes_uvt']) ?> UVT. Se aplica la exención del <?= e($yearRules['exencion_pct']) ?>% sobre la base después de aportes y deducciones, hasta el saldo anual de <?= e($yearRules['exencion_uvt']) ?> UVT. El conjunto se limita al <?= e($yearRules['conjunto_pct']) ?>% y al saldo anual de <?= e($yearRules['conjunto_uvt']) ?> UVT. No dividimos estos topes anuales entre doce.</p>
<p>Los acumulados anteriores deben coincidir con la depuración certificada del empleador; no se deducen de meses sin importar. En enero son 0 si no hubo pagos anteriores del año. Si hay otros empleadores o traslados, pide que se revise su efecto en los límites. No incluyas préstamos ni descuentos personales como deducciones.</p>
<p>Procedimiento 2: comprobamos la aplicación del porcentaje certificado vigente para enero–junio o julio–diciembre. Su cálculo histórico requiere los pagos de los doce meses anteriores y una auditoría adicional: «OK» no certifica ese cálculo histórico.</p>
<p>Este control ordinario no incluye AFC, aportes voluntarios, cesantías, primas con tratamiento separado, indemnizaciones, retroactivos ni períodos parciales. Si existen, deja el mes pendiente y solicita la depuración particular.</p>
<?php $item=['id'=>'','norm'=>'ET 206.10, 383, 385, 386, 387 y 388'];require __DIR__.'/../beneficios/articles.php'; ?>
</details>
<label class="block">Referencia de los soportes y depuración revisados<textarea name="soporte" aria-describedby="ayuda-soporte" required minlength="10" maxlength="1000" class="w-full rounded-xl bg-surface-container-low p-3"><?= e($values['soporte']??'') ?></textarea></label><?php $field='soporte';require __DIR__.'/ayuda-campo.php'; ?>
<h3 class="text-headline-md font-semibold">3. Revisa estas confirmaciones y compara</h3>
<p>Después de pulsar «Guardar y comprobar retención», verás el resultado arriba: lo descontado, lo esperado y su diferencia. No se cambia el descuento de tu nómina.</p>
<?php foreach (['completo'=>'Confirmé con el empleador que el mes contiene todos sus pagos.',
    'ordinario'=>'Son pagos ordinarios; no hay los conceptos especiales indicados en la ayuda.',
    'verificado'=>'Revisé soportes, acumulados anteriores, redondeo y procedimiento (incluido porcentaje vigente si es 2).'] as $field=>$label): ?>
<div><label class="block"><input type="checkbox" name="<?= e($field) ?>" aria-describedby="ayuda-<?= e($field) ?>" value="1" required <?= $_SERVER['REQUEST_METHOD']==='POST' && !empty($values[$field])?'checked':'' ?>> <?= e($label) ?></label><?php require __DIR__.'/ayuda-campo.php'; ?></div>
<?php endforeach; ?>
<button class="rounded-xl bg-inverse-surface text-on-secondary px-4 py-2">Guardar y comprobar retención</button>
</form><?php endif; ?>
</section>
