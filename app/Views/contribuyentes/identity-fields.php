<?php use App\Services\ContribuyenteData; ?>
            <fieldset class="space-y-space-md">
                <legend class="text-headline-md font-semibold">Datos de identificación</legend>
                <div class="profile-grid">
                    <?php foreach (['tipo' => ['Tipo de contribuyente', ContribuyenteData::TIPOS], 'documento_tipo' => ['Tipo de documento', ContribuyenteData::DOCUMENTOS]] as $field => [$label, $options]): ?>
                        <div class="profile-basic-field">
                            <label class="block text-body-md mb-1" for="<?= e($field) ?>"><?= e($label) ?> *</label>
                            <select class="w-full px-3 py-2.5 bg-surface-container-low rounded-xl border border-transparent focus:border-outline-variant" id="<?= e($field) ?>" name="<?= e($identityPrefix.$field.$identitySuffix) ?>" required>
                                <option value="">Selecciona una opción</option>
                                <?php foreach ($options as $key => $value): ?><option value="<?= e($key) ?>" <?= $identity[$field] === $key ? 'selected' : '' ?>><?= e($value) ?></option><?php endforeach; ?>
                            </select>
                            <?php $helpTitle=$label; $helpId=$field.'-ayuda';
                            $helpText=$field==='tipo'
                                ? 'Selecciona la naturaleza del titular según su identificación y RUT. Para una persona fallecida, revisa si corresponde a una sucesión ilíquida; el fallecimiento no la convierte en persona jurídica.'
                                : 'Copia el tipo de documento del soporte de identificación. Para personas jurídicas y sucesiones ilíquidas, este formulario exige NIT. Escribe el número sin puntos ni guiones y el dígito de verificación en su campo separado.';
                            require __DIR__.'/../partials/profile-help.php'; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="profile-basic-field">
                    <label class="block text-body-md mb-1" for="nombre">Nombre completo o razón social *</label>
                    <?php $helpTitle='Nombre'; $helpId='nombre-ayuda'; $helpText='Identifica al titular de esta ficha. Copia el nombre completo o la razón social del RUT, sin sustituirlo por un nombre comercial.'; require __DIR__.'/../partials/profile-help.php'; ?>
                    <input class="w-full px-3 py-2.5 bg-surface-container-low rounded-xl border border-transparent focus:border-outline-variant" id="nombre" name="<?= e($identityPrefix.'nombre'.$identitySuffix) ?>" maxlength="150" value="<?= e($identity['nombre']) ?>" required>
                </div>
                <div class="profile-grid">
                    <div class="profile-basic-field">
                        <label class="block text-body-md mb-1" for="documento_numero">Número de documento *</label>
                        <?php $helpTitle='Número de documento'; $helpId='documento-numero-ayuda'; $helpText='Permite distinguir este contribuyente y evitar registros duplicados. Copia el número del tipo de documento seleccionado; si es NIT, separa el dígito de verificación.'; require __DIR__.'/../partials/profile-help.php'; ?>
                        <input class="w-full px-3 py-2.5 bg-surface-container-low rounded-xl border border-transparent focus:border-outline-variant" id="documento_numero" name="<?= e($identityPrefix.'documento_numero'.$identitySuffix) ?>" maxlength="30" value="<?= e($identity['documento_numero']) ?>" aria-describedby="documento-ayuda" required>
                        <p id="documento-ayuda" class="text-body-md text-secondary mt-2">Sin puntos, espacios ni guiones. Si usas NIT, escribe el dígito de verificación por separado.</p>
                    </div>
                    <div class="profile-basic-field">
                        <label class="block text-body-md mb-1" for="dv">Dígito de verificación del NIT (opcional)</label>
                        <?php $helpTitle='Dígito de verificación'; $helpId='dv-ayuda'; $helpText='Dato del RUT que acompaña al NIT para verificar su identificación. Copia el dígito de la casilla 6 por separado; no corresponde al número de cédula.'; require __DIR__.'/../partials/profile-help.php'; ?>
                        <input class="w-full px-3 py-2.5 bg-surface-container-low rounded-xl border border-transparent focus:border-outline-variant" id="dv" name="<?= e($identityPrefix.'dv'.$identitySuffix) ?>" inputmode="numeric" pattern="[0-9]" maxlength="1" value="<?= e($identity['dv']) ?>">
                    </div>
                </div>
            </fieldset>
