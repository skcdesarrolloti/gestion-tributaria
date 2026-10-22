<?php use App\Services\ContribuyenteData; ?>
            <fieldset class="space-y-space-md">
                <legend class="text-headline-md font-semibold">Datos de identificación</legend>
                <div class="profile-grid">
                    <?php foreach (['tipo' => ['Tipo de contribuyente', ContribuyenteData::TIPOS], 'documento_tipo' => ['Tipo de documento', ContribuyenteData::DOCUMENTOS]] as $field => [$label, $options]): ?>
                        <div>
                            <label class="block text-body-md mb-1" for="<?= e($field) ?>"><?= e($label) ?> *</label>
                            <select class="w-full px-3 py-2.5 bg-surface-container-low rounded-xl border border-transparent focus:border-outline-variant" id="<?= e($field) ?>" name="<?= e($identityPrefix.$field.$identitySuffix) ?>" required>
                                <option value="">Selecciona una opción</option>
                                <?php foreach ($options as $key => $value): ?><option value="<?= e($key) ?>" <?= $identity[$field] === $key ? 'selected' : '' ?>><?= e($value) ?></option><?php endforeach; ?>
                            </select>
                            <?php $helpTitle=$label; $helpArticle=null;
                            $helpText=$field==='tipo'
                                ? 'Selecciona la naturaleza del titular según su identificación y RUT. Para una persona fallecida, revisa si corresponde a una sucesión ilíquida; el fallecimiento no la convierte en persona jurídica.'
                                : 'Copia el tipo de documento del soporte de identificación. Para personas jurídicas y sucesiones ilíquidas, este formulario exige NIT. Escribe el número sin puntos ni guiones y el dígito de verificación en su campo separado.';
                            require __DIR__.'/../partials/field-help.php'; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div>
                    <label class="block text-body-md mb-1" for="nombre">Nombre completo o razón social *</label>
                    <input class="w-full px-3 py-2.5 bg-surface-container-low rounded-xl border border-transparent focus:border-outline-variant" id="nombre" name="<?= e($identityPrefix.'nombre'.$identitySuffix) ?>" maxlength="150" value="<?= e($identity['nombre']) ?>" required>
                </div>
                <div class="profile-grid">
                    <div>
                        <label class="block text-body-md mb-1" for="documento_numero">Número de documento *</label>
                        <input class="w-full px-3 py-2.5 bg-surface-container-low rounded-xl border border-transparent focus:border-outline-variant" id="documento_numero" name="<?= e($identityPrefix.'documento_numero'.$identitySuffix) ?>" maxlength="30" value="<?= e($identity['documento_numero']) ?>" aria-describedby="documento-ayuda" required>
                        <p id="documento-ayuda" class="text-body-md text-secondary mt-2">Sin puntos, espacios ni guiones. Si usas NIT, escribe el dígito de verificación por separado.</p>
                    </div>
                    <div>
                        <label class="block text-body-md mb-1" for="dv">Dígito de verificación del NIT (opcional)</label>
                        <input class="w-full px-3 py-2.5 bg-surface-container-low rounded-xl border border-transparent focus:border-outline-variant" id="dv" name="<?= e($identityPrefix.'dv'.$identitySuffix) ?>" inputmode="numeric" pattern="[0-9]" maxlength="1" value="<?= e($identity['dv']) ?>">
                    </div>
                </div>
            </fieldset>
