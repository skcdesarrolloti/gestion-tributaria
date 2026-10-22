<?php $schema=$schemas[$block]; ?>
<section class="bg-surface-container-lowest rounded-2xl border border-surface-container p-4 space-y-space-md">
    <h2 class="text-headline-md font-semibold"><?= e($tabs[$tab][0]??$schema['title']) ?></h2>
    <details><summary class="text-secondary underline">? · Qué información va aquí</summary><p class="mt-2"><?= e($schema['guide']) ?></p></details>
    <p class="text-body-md text-secondary">Los valores guardados aparecen diligenciados. Completa los vacíos que correspondan a tu caso; un campo opcional puede quedar vacío.</p>
    <label class="block"><input type="checkbox" id="profile-only-empty"> Mostrar solo campos vacíos (conservar los datos ya guardados)</label>
    <?php if ($errors): ?><div role="alert" class="bg-error-container text-on-error-container rounded-xl p-3"><ul class="list-disc pl-4"><?php foreach ($errors as $error): ?><li><?= e($error) ?></li><?php endforeach; ?></ul></div><?php endif; ?>
    <?php $missing=\App\Services\CaracterizacionData::missing($record,$schema,$taxpayer['tipo']); ?>
    <?php if ($roles && !$record['filas']) { $missing=[]; } ?>
    <?php if ($missing): ?><details><summary class="text-secondary underline"><?= count($missing) ?> <?= count($missing)===1?'dato pendiente':'datos pendientes' ?></summary><ul class="list-disc pl-4"><?php foreach ($missing as $item): ?><li><?= e($item) ?></li><?php endforeach; ?></ul></details><?php endif; ?>
    <form id="caracterizacion-form" method="post" action="<?= e($formAction) ?>" class="space-y-space-md">
        <?php require __DIR__.'/../partials/csrf.php'; ?>
        <input type="hidden" name="revision" value="<?= e($revision) ?>">
        <?php if ($block==='identificacion'): $identityPrefix='identidad['; $identitySuffix=']'; require __DIR__.'/../contribuyentes/identity-fields.php'; endif; ?>
        <?php if ($roles): ?><input type="hidden" name="aplicabilidad" value="si"><input type="hidden" name="justificacion" value="<?= e($record['justificacion']) ?>"><?php endif; ?>
        <?php if (!empty($schema['allow_na']) && !$roles): ?>
            <div class="profile-field-label"><label for="aplicabilidad">¿Esta información aplica?</label>
            <?php $helpTitle='Aplicabilidad'; $helpId='aplicabilidad-ayuda'; $helpText=\App\Services\PerfilAyuda::text('control','aplicabilidad',['help'=>'']); require __DIR__.'/../partials/profile-help.php'; ?>
            </div>
            <select id="aplicabilidad" name="aplicabilidad" class="w-full bg-surface-container-low rounded-xl p-2"><?php foreach ([''=>'Por confirmar','si'=>'Sí, aplica','no'=>'No aplica: justificar'] as $key=>$text): ?><option value="<?= e($key) ?>" <?= $record['aplicabilidad']===$key?'selected':'' ?>><?= e($text) ?></option><?php endforeach; ?></select>
            <div class="profile-field-label"><label for="justificacion">Si no aplica, explica por qué</label>
            <?php $helpTitle='Justificación'; $helpId='justificacion-ayuda'; $helpText=\App\Services\PerfilAyuda::text('control','justificacion',['help'=>'']); require __DIR__.'/../partials/profile-help.php'; ?>
            </div>
            <input id="justificacion" name="justificacion" value="<?= e($record['justificacion']) ?>" maxlength="500" class="w-full bg-surface-container-low rounded-xl p-2">
        <?php endif; ?>
        <?php require __DIR__.'/fields.php'; ?>
        <?php if (isset($schema['rows'])): ?>
            <?php if ($roles && !$record['filas']): ?><p>No hay personas registradas en esta sección. Agrega solo las que correspondan.</p><?php endif; ?>
            <p>Agrega solo los registros que correspondan. Para confirmar cambios o eliminaciones pulsa Guardar información.</p>
            <div id="profile-rows" class="space-y-space-md"><?php foreach ($record['filas'] as $rowIndex=>$row): require __DIR__.'/row.php'; endforeach; ?></div>
            <template id="profile-row-template"><?php $rowIndex='__INDEX__'; $row=[]; require __DIR__.'/row.php'; ?></template>
            <button type="button" id="add-profile-row" data-next="<?= count($record['filas']) ?>" class="rounded-xl bg-surface-container-low text-secondary px-4 py-2">Agregar renglón</button>
        <?php endif; ?>
        <?php require __DIR__.'/metadata.php'; ?>
        <p role="status" id="profile-save-status">Se guarda esta pestaña. Puedes continuar después.</p>
        <button type="submit" class="rounded-xl bg-inverse-surface text-on-secondary px-4 py-2">Guardar información</button>
    </form>
</section>
