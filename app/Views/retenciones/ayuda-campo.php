<?php $fieldGuide=\App\Services\RetencionGuia::field($field); ?>
<?php if ($field==='usado25') { $fieldGuide['hint']=str_replace('25%',$yearRules['exencion_pct'].'%',$fieldGuide['hint']); } ?>
<?php if (!empty($fieldGuide['choices'])):
    $selectedHelp=$fieldGuide['choices'][(string)($values[$field]??'')]??'Selecciona una opción: aquí aparecerá su explicación automáticamente.';
?>
<p class="bg-primary-container rounded-xl p-3 text-body-md" role="status" aria-live="polite" aria-atomic="true" data-retencion-guide="<?= e($field) ?>" data-retencion-options="<?= e(json_encode($fieldGuide['choices'],JSON_UNESCAPED_UNICODE|JSON_THROW_ON_ERROR)) ?>"><?= e($selectedHelp) ?></p>
<?php endif; ?>
<p id="ayuda-<?= e($field) ?>" class="text-body-md text-secondary"><?= e($fieldGuide['hint']) ?></p>
<?php if (!empty($fieldGuide['details']) || !empty($fieldGuide['choices'])): ?>
<details class="text-body-md"><summary class="text-secondary underline">? · Cómo responder este campo</summary>
<?php foreach ($fieldGuide['details']??[] as $helpText): ?><p><?= e($helpText) ?></p><?php endforeach; ?>
<?php foreach ($fieldGuide['choices']??[] as $helpText): ?><p><?= e($helpText) ?></p><?php endforeach; ?>
</details><?php endif; ?>
