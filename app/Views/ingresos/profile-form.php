        <form method="post" action="<?= e($profileAction) ?>" class="space-y-space-md">
        <fieldset <?= $readOnly?'disabled':'' ?> class="space-y-space-md">
        <?php if ($readOnly): ?><legend>Solo consulta: no se guardan cambios en esta ficha.</legend><?php endif; ?>
            <?php require __DIR__.'/../partials/csrf.php'; ?>
            <div class="profile-grid">
                <?php foreach (\App\Services\PerfilGuidance::fields($taxpayer['tipo']) as $field=>$definition): ?>
                <?php $label=$definition['label']; $options=$definition['options']; ?>
                <div>
                    <label class="block text-body-md mb-1" for="<?= e($field) ?>"><?= e($label) ?> *</label>
                    <select class="w-full px-3 py-2.5 bg-surface-container-low rounded-xl border border-transparent" id="<?= e($field) ?>" name="<?= e($field) ?>" aria-describedby="<?= e($field.'-help') ?>" data-guidance="<?= e(json_encode($options,JSON_UNESCAPED_UNICODE|JSON_THROW_ON_ERROR)) ?>" required>
                        <option value="">Selecciona una opción</option>
                        <?php foreach ($options as $key=>$option): ?><option value="<?= e($key) ?>" <?= (string)$record[$field]===(string)$key?'selected':'' ?>><?= e($option['label']) ?></option><?php endforeach; ?>
                    </select>
                    <?php require __DIR__.'/option-help.php'; ?>
                </div>
                <?php endforeach; ?>
            </div>
            <label class="block text-body-md" for="soporte_perfil">Evidencia del perfil fiscal *</label>
            <textarea class="w-full px-3 py-2.5 bg-surface-container-low rounded-xl" id="soporte_perfil" name="soporte_perfil" maxlength="500" rows="3" required><?= e($record['soporte_perfil']) ?></textarea>
            <p class="text-body-md text-secondary">Indica referencias del RUT y del análisis de residencia. Es una referencia documental, no una carga de archivos. La elección de tratamiento no acredita por sí sola un beneficio.</p>
            <p id="regimen-availability" class="text-body-md text-secondary" role="status">El régimen ordinario tiene depuración inicial disponible. Las otras opciones se ofrecen para consultar su definición y alcance.</p>
            <button id="create-profile" class="rounded-xl bg-inverse-surface text-on-secondary px-4 py-2" type="submit"><?= e($profileButton) ?></button>
        </fieldset>
        </form>
