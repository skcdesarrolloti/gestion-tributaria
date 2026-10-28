<?php $title='Configuración · Parámetros anuales';require __DIR__.'/../partials/head.php'; ?>
<body class="bg-background min-h-screen font-body-md text-on-surface antialiased">
<?php require __DIR__.'/../partials/header.php'; ?>
<main class="portal-container py-8 space-y-space-lg">
<h1 class="text-headline-lg font-semibold">Configuración · Parámetros anuales</h1>
<p>La UVT convierte los límites tributarios a pesos. Los rangos y topes de esta pantalla corresponden al control de retención laboral. Cada cálculo utiliza los valores de su propio año.</p>
<form method="get" class="space-y-space-sm"><input type="hidden" name="route" value="configuracion"><label>Año que quieres consultar o preparar<input name="anio" type="number" min="2025" max="2100" value="<?= e($year) ?>" required class="w-full rounded-xl bg-surface-container-low p-3"></label><button class="rounded-xl bg-inverse-surface text-on-secondary px-4 py-2">Consultar año</button></form>
<p>Años con parámetros: <?php foreach ($years as $savedYear): ?><a class="text-secondary underline" href="<?= e(url($config,'configuracion').'&anio='.$savedYear) ?>"><?= e($savedYear) ?></a> <?php endforeach; ?></p>
<?php if ($errors): ?><div role="alert" class="bg-error-container rounded-xl p-4"><?php foreach ($errors as $error): ?><p><?= e($error) ?></p><?php endforeach; ?></div><?php endif; ?>
<?php if (input($_GET,'guardado')==='1'): ?><p class="bg-primary-container rounded-xl p-3" role="status">Versión publicada. Las consultas nuevas usan estos valores del año; las revisiones de retención anteriores quedan pendientes de confirmar con la nueva versión. Los documentos e importes de nómina se conservan.</p><?php endif; ?>
<section class="bg-surface-container-lowest border border-surface-container rounded-xl p-4 space-y-space-md">
<h2 class="text-headline-md font-semibold">Parámetros de <?= e($year) ?> · <?= $exists?'versión '.e($revision===0?'inicial':$revision):'año por preparar' ?></h2>
<?php if (!$exists): ?><p class="bg-primary-container rounded-xl p-3">Este año aún no tiene valores oficiales guardados. Los rangos y topes se muestran como referencia de 2026; debes contrastarlos con la norma del nuevo año. UVT y fuentes quedan vacías para que no se confundan con valores confirmados.</p><?php endif; ?>
<?php if ($locked): ?><p>Este año ya terminó: se muestra en consulta para conservar sus valores históricos. Puedes preparar otro año desde el selector superior.</p><?php endif; ?>
<form method="post" class="space-y-space-md">
<?php require __DIR__.'/../partials/csrf.php'; ?><input type="hidden" name="anio" value="<?= e($year) ?>"><input type="hidden" name="revision" value="<?= e($revision) ?>">
<fieldset <?= $locked?'disabled':'' ?> class="space-y-space-md">
<label class="block">Valor de una UVT en pesos<input name="uvt" value="<?= e($values['uvt']??'') ?>" type="number" min="1" max="1000000" required class="w-full rounded-xl bg-surface-container-low p-3"></label>
<p class="text-body-md text-secondary">Escribe pesos enteros, sin puntos ni comas. Ejemplo de formato: 52374. No se estima automáticamente la UVT del próximo año.</p>
<label class="block">Dirección de la fuente oficial DIAN de la UVT<input name="source" value="<?= e($values['source']??'') ?>" type="url" maxlength="500" required class="w-full rounded-xl bg-surface-container-low p-3"></label>
<p class="text-body-md text-secondary">Copia la dirección HTTPS de la resolución publicada por la DIAN. Se conserva como referencia del dato; no necesitas salir de esta pantalla para consultar el historial.</p>
<label class="block">Normas que respaldan los rangos y topes<textarea name="norma" minlength="10" maxlength="500" required class="w-full rounded-xl bg-surface-container-low p-3"><?= e($values['norma']??'') ?></textarea></label>
<h3 class="text-headline-md font-semibold">Rangos de retención laboral</h3>
<p>Los límites se escriben en UVT, no en pesos. «Hasta» incluye el límite superior; el siguiente rango comienza por encima. La tarifa se aplica al excedente sobre el límite inferior y se suma el componente fijo. El aplicativo convierte todo a pesos con la UVT de este año.</p>
<div class="renta-sheet-scroll" tabindex="0" role="region" aria-label="Editar rangos anuales de retención"><table class="renta-sheet-table"><thead><tr><th>Rango</th><th>Hasta (UVT)</th><th>Tarifa marginal (%)</th><th>Componente fijo (UVT)</th></tr></thead><tbody>
<?php for ($i=0;$i<7;$i++): $row=$values['tramos'][$i]??[]; ?><tr><th scope="row"><?= e($i+1) ?></th><td><?php if ($i===6): ?>En adelante<?php else: ?><input aria-label="Hasta UVT rango <?= e($i+1) ?>" name="tramos[<?= e($i) ?>][hasta]" type="number" min="1" max="1000000" value="<?= e($row['hasta']??'') ?>" required class="w-full rounded-xl bg-surface-container-low p-2"><?php endif; ?></td>
<td><input aria-label="Tarifa porcentaje rango <?= e($i+1) ?>" name="tramos[<?= e($i) ?>][tarifa]" type="number" min="0" max="100" value="<?= e($row['tarifa']??'') ?>" required class="w-full rounded-xl bg-surface-container-low p-2"></td><td><input aria-label="Fijo UVT rango <?= e($i+1) ?>" name="tramos[<?= e($i) ?>][fijo]" type="number" min="0" max="1000000" value="<?= e($row['fijo']??'') ?>" required class="w-full rounded-xl bg-surface-container-low p-2"></td></tr><?php endfor; ?>
</tbody></table></div>
<h3 class="text-headline-md font-semibold">Topes de beneficios en la retención laboral</h3>
<p>Un tope es un máximo permitido, no un descuento automático. Los porcentajes se escriben como 25 para 25%. El valor anual se consume entre meses; no se reinicia mensualmente ni se divide entre doce.</p>
<div class="grid md:grid-cols-2 gap-space-md"><?php foreach (\App\Services\ParametrosAnuales::LIMITS as $field=>[$label,$default]): ?><label><?= e($label) ?><input name="<?= e($field) ?>" value="<?= e($values[$field]??'') ?>" type="number" min="0" max="<?= str_ends_with($field,'_pct')?'100':'100000' ?>" required class="w-full rounded-xl bg-surface-container-low p-3"></label><?php endforeach; ?></div>
<p class="bg-primary-container rounded-xl p-3">Publicar crea otra versión y no borra la anterior. Las modificaciones afectan las consultas nuevas de este año y exigen revisar las validaciones mensuales anteriores. No modifica importes de nómina ni declaraciones guardadas. Los parámetros de otros años se conservan.</p>
<label class="block"><input type="checkbox" name="confirmado" value="1" required> Contrasté la UVT, los rangos y topes con las fuentes oficiales del año y quiero publicar esta versión.</label>
<button class="rounded-xl bg-inverse-surface text-on-secondary px-4 py-2">Publicar parámetros de <?= e($year) ?></button>
</fieldset></form>
<p class="text-body-md text-secondary">Alcance: UVT anual y control de retención laboral. No cambia el salario mínimo, la nómina pagada ni las reglas de otros beneficios. Preparar un año futuro no habilita por sí solo nuevos formularios o años en Registro de información. Si la norma cambia la estructura de los siete rangos o requiere porcentajes fraccionarios, debe ajustarse el motor antes de publicar.</p>
</section>
<section class="bg-surface-container-lowest border border-surface-container rounded-xl p-4 space-y-space-md"><h2 class="text-headline-md font-semibold">Historial del año</h2>
<p>La versión inicial de 2025/2026 permanece incorporada en el programa. Cada publicación posterior conserva quién la realizó, cuándo y sus valores.</p>
<?php if (in_array($year,[2025,2026],true)): $historic=\App\Services\ParametrosAnuales::defaults($year); ?><details><summary class="text-secondary underline">Consultar valores de la versión inicial</summary><?php require __DIR__.'/resumen.php'; ?></details><?php endif; ?>
<?php foreach ($history as $entry): $historic=json_decode($entry['datos'],true,512,JSON_THROW_ON_ERROR); ?><details><summary class="text-secondary underline"><?= e('Versión '.$entry['revision'].' · '.$entry['created_at'].' · '.$entry['nombre']) ?></summary><?php require __DIR__.'/resumen.php'; ?></details><?php endforeach; ?>
<?php if (!$history): ?><p>Aún no hay versiones publicadas desde esta pantalla.</p><?php endif; ?></section>
</main><?php require __DIR__.'/../partials/footer.php'; ?>
