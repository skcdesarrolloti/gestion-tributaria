<div>
    <label class="block" for="<?= e($inputId) ?>"><?= e($definition['label']) ?><?= $definition['required']?' · necesario para completar':'' ?></label>
    <?php if ($definition['kind']==='select'): ?>
        <?php if (count($definition['options'])>25): ?>
            <label class="block text-label-sm text-secondary mt-2" for="<?= e($inputId.'-buscar') ?>">Buscar código o descripción</label>
            <input type="search" id="<?= e($inputId.'-buscar') ?>" data-filter-select="<?= e($inputId) ?>" class="w-full bg-surface-container-low rounded-xl p-2" placeholder="Escribe para reducir la lista">
        <?php endif; ?>
        <select id="<?= e($inputId) ?>" name="<?= e($name) ?>" aria-describedby="<?= e($inputId.'-descripcion') ?>" class="mt-2 w-full px-3 py-2.5 bg-surface-container-low rounded-xl">
            <option value="">Por confirmar / sin diligenciar</option>
            <?php foreach ($definition['options'] as $option=>$text): ?><option value="<?= e($option) ?>" <?= (string)$option===$value?'selected':'' ?>><?= e($text) ?></option><?php endforeach; ?>
        </select>
        <p id="<?= e($inputId.'-descripcion') ?>" data-selected-label="<?= e($inputId) ?>" class="text-body-md text-secondary mt-2 taxpayer-name"><?= e($definition['options'][$value]??'Elige una opción; si no la conoces, déjala por confirmar.') ?></p>
    <?php elseif ($definition['kind']==='textarea'): ?>
        <textarea id="<?= e($inputId) ?>" name="<?= e($name) ?>" maxlength="2000" rows="3" class="mt-2 w-full bg-surface-container-low rounded-xl p-2"><?= e($value) ?></textarea>
    <?php else: ?>
        <input id="<?= e($inputId) ?>" name="<?= e($name) ?>" value="<?= e($value) ?>" type="<?= e(in_array($definition['kind'],['date','email'],true)?$definition['kind']:'text') ?>" <?= in_array($definition['kind'],['integer','decimal','percent'],true)?'inputmode="decimal"':'' ?> maxlength="500" class="mt-2 w-full px-3 py-2.5 bg-surface-container-low rounded-xl">
    <?php endif; ?>
    <?php if ($definition['help']!==''): $helpTitle=$definition['label']; $helpText=$definition['help']; $helpArticle=null; require __DIR__.'/../partials/field-help.php'; endif; ?>
</div>
