<?php $schema=$schemas[$block]; ?>
<section class="bg-surface-container-lowest rounded-2xl border border-surface-container p-4 space-y-space-md">
    <h2 class="text-headline-md font-semibold"><?= e($schema['title']) ?></h2>
    <details><summary class="text-secondary underline">? · Qué debes registrar en este bloque</summary><p class="mt-2"><?= e($schema['guide']) ?></p></details>
    <?php if ($errors): ?><div role="alert" class="bg-error-container text-on-error-container rounded-xl p-3"><ul class="list-disc pl-4"><?php foreach ($errors as $error): ?><li><?= e($error) ?></li><?php endforeach; ?></ul></div><?php endif; ?>
    <?php $missing=\App\Services\CaracterizacionData::missing($record,$schema,$taxpayer['tipo']); ?>
    <?php if ($missing): ?><details><summary class="text-secondary underline"><?= count($missing) ?> pendientes para completar</summary><ul class="list-disc pl-4"><?php foreach ($missing as $item): ?><li><?= e($item) ?></li><?php endforeach; ?></ul></details><?php endif; ?>
    <form id="caracterizacion-form" method="post" action="<?= e($base.'&bloque='.$block) ?>" class="space-y-space-md">
        <?php require __DIR__.'/../partials/csrf.php'; ?>
        <input type="hidden" name="revision" value="<?= e($revision) ?>">
        <?php if (!empty($schema['allow_na'])): ?>
            <label class="block" for="aplicabilidad">¿Este bloque aplica?</label>
            <select id="aplicabilidad" name="aplicabilidad" class="w-full bg-surface-container-low rounded-xl p-2"><?php foreach ([''=>'Por confirmar','si'=>'Sí, aplica','no'=>'No aplica: justificar'] as $key=>$text): ?><option value="<?= e($key) ?>" <?= $record['aplicabilidad']===$key?'selected':'' ?>><?= e($text) ?></option><?php endforeach; ?></select>
            <label class="block" for="justificacion">Si no aplica, explica por qué</label><input id="justificacion" name="justificacion" value="<?= e($record['justificacion']) ?>" maxlength="500" class="w-full bg-surface-container-low rounded-xl p-2">
        <?php endif; ?>
        <div class="scenario-settings-grid">
            <?php foreach ($schema['fields'] as $key=>$definition): $name='campos['.$key.']'; $inputId='campo-'.$key; $value=$record['campos'][$key]; require __DIR__.'/field.php'; endforeach; ?>
        </div>
        <?php if (isset($schema['rows'])): ?>
            <p>Agrega los renglones necesarios. Se conservan al guardar; quitar uno se confirma con el botón de guardado del bloque. Máximo 100 por bloque.</p>
            <div id="profile-rows" class="space-y-space-md"><?php foreach ($record['filas'] as $rowIndex=>$row): require __DIR__.'/row.php'; endforeach; ?></div>
            <template id="profile-row-template"><?php $rowIndex='__INDEX__'; $row=[]; require __DIR__.'/row.php'; ?></template>
            <button type="button" id="add-profile-row" data-next="<?= count($record['filas']) ?>" class="rounded-xl bg-surface-container-low text-secondary px-4 py-2">Agregar renglón</button>
        <?php endif; ?>
        <details class="border border-surface-container rounded-xl p-4" open>
            <summary class="font-semibold">Estado, soporte y notas de este bloque</summary>
            <div class="profile-grid mt-2">
                <div><label for="estado">Estado de la información</label><select id="estado" name="estado" class="w-full bg-surface-container-low rounded-xl p-2"><?php foreach ([''=>'Por confirmar','borrador'=>'Borrador: estoy completando','declarado'=>'Declarado por el contribuyente','documentado'=>'Contrastado por mí con documentos'] as $key=>$text): ?><option value="<?= e($key) ?>" <?= $record['estado']===$key?'selected':'' ?>><?= e($text) ?></option><?php endforeach; ?></select></div>
                <div><label for="fecha">Fecha de revisión</label><input id="fecha" name="fecha" type="date" value="<?= e($record['fecha']) ?>" class="w-full bg-surface-container-low rounded-xl p-2"></div>
            </div>
            <label class="block mt-2" for="fuente">Documento o fuente de los datos</label><input id="fuente" name="fuente" maxlength="500" value="<?= e($record['fuente']) ?>" placeholder="Ejemplo: declaración del contribuyente; RUT pendiente de conseguir." class="w-full bg-surface-container-low rounded-xl p-2">
            <label class="block mt-2" for="notas">Notas, diferencias o documentos pendientes</label><textarea id="notas" name="notas" maxlength="2000" rows="3" class="w-full bg-surface-container-low rounded-xl p-2"><?= e($record['notas']) ?></textarea>
        </details>
        <p role="status" id="profile-save-status">Puedes guardar un borrador y continuar después. Solo se guarda este bloque y este año.</p>
        <button type="submit" class="rounded-xl bg-inverse-surface text-on-secondary px-4 py-2">Guardar bloque</button>
    </form>
</section>
<script src="<?= e(asset($config,'js/caracterizacion.js').'?v='.filemtime(dirname(__DIR__,3).'/public/assets/js/caracterizacion.js')) ?>" defer></script>
