<?php $fieldGuide=\App\Services\RetencionGuia::field($field); ?>
<p id="ayuda-<?= e($field) ?>" class="text-body-md text-secondary"><?= e($fieldGuide['hint']) ?></p>
<?php if (!empty($fieldGuide['details']) || !empty($fieldGuide['choices'])): ?>
<details class="text-body-md"><summary class="text-secondary underline">? · Cómo responder este campo</summary>
<?php foreach ($fieldGuide['details']??[] as $helpText): ?><p><?= e($helpText) ?></p><?php endforeach; ?>
<?php foreach ($fieldGuide['choices']??[] as $helpText): ?><p><?= e($helpText) ?></p><?php endforeach; ?>
</details><?php endif; ?>
<?php if (!empty($fieldGuide['choices'])): ?>
<p class="text-body-md text-secondary" role="status" aria-live="polite" data-retencion-guide="<?= e($field) ?>" data-retencion-options="<?= e(json_encode($fieldGuide['choices'],JSON_UNESCAPED_UNICODE|JSON_THROW_ON_ERROR)) ?>"></p>
<?php endif; ?>
