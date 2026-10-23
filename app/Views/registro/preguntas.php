<details id="fuentes" class="bg-surface-container-lowest border border-surface-container rounded-xl p-4 space-y-space-md" <?= $questions['pending']?'open':'' ?>>
<summary class="text-headline-md font-semibold">Fuentes de ingresos de <?= e($state['anio']) ?> · <?= e((string)$questions['pending']) ?> por confirmar</summary>
<p>El RUT clasifica tus actividades; aquí confirmas qué ingresos hubo o esperas tener en este año. Se recuperan las respuestas anuales guardadas. Un espacio vacío significa pendiente, nunca «No».</p>
<?php if (input($_GET,'fuentes_guardadas')==='1'): ?><p role="status" class="bg-primary-container rounded-xl p-3">Respuestas del año guardadas. Los importes y las condiciones fiscales ya guardadas se conservan; revisa su coherencia si cambiaste una fuente.</p><?php endif; ?>
<?php foreach ($questions['groups'] as $groupKey=>$group): ?>
<details name="fuentes-del-anio" class="border border-surface-container rounded-xl p-3 space-y-space-md" <?= $questions['first']===$groupKey?'open':'' ?>>
<summary class="font-semibold"><?= e($group['title']) ?> · <?= e((string)$group['pending']) ?> pendientes</summary>
<form method="post" action="<?= e(url($config,'registro/fuentes').\App\Services\RegistroNavegacion::query($state,(int)$taxpayer['id'])) ?>" class="space-y-space-md">
<?php require __DIR__.'/../partials/csrf.php'; ?>
<input type="hidden" name="revision" value="<?= e((string)$questions['revision']) ?>"><input type="hidden" name="grupo" value="<?= e($groupKey) ?>">
<fieldset class="profile-grid" <?= $readOnly?'disabled':'' ?>>
<?php foreach ($group['fields'] as $key=>[$question,$help]): ?>
<div><label for="fuente-<?= e($key) ?>" class="block"><?= e($question) ?></label>
<select id="fuente-<?= e($key) ?>" name="respuestas[<?= e($key) ?>]" class="w-full bg-surface-container-low rounded-xl p-2">
<?php foreach ([''=>'Pendiente por confirmar','si'=>'Sí','no'=>'No'] as $value=>$label): ?><option value="<?= e($value) ?>" <?= $questions['answers'][$key]===$value?'selected':'' ?>><?= e($label) ?></option><?php endforeach; ?></select>
<details><summary class="text-secondary underline">? · Para qué se pregunta</summary><p><?= e($help) ?></p><p>Identifica qué sección revisar. La respuesta no registra importes, no concede beneficios y no cambia el RUT.</p></details></div>
<?php endforeach; ?></fieldset>
<?php if (!$readOnly): ?><button class="rounded-xl bg-inverse-surface text-on-secondary px-4 py-2">Guardar respuestas de este grupo</button><?php endif; ?>
</form></details>
<?php endforeach; ?>
<p class="text-secondary text-body-md">Se pueden tener varias fuentes simultáneas. Los importes y soportes se registran después en sus casillas. Exterior, venta de activos, criptoactivos y ECE requieren revisión particular, sin clasificación automática.</p>
</details>
