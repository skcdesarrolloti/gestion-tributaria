<?php
use App\Services\ContribuyenteData;
$sourceProfiles = [];
foreach (ContribuyenteData::TIPOS as $type => $name) {
    $sourceProfiles[$type] = ContribuyenteData::sourcesFor($type);
}
?>
<fieldset class="space-y-space-md" id="income-sources">
    <legend class="text-headline-md font-semibold">Fuentes de ingresos (opcionales)</legend>
    <p class="text-body-md text-secondary" id="sources-help" aria-live="polite">Selecciona el tipo de contribuyente para ver sus fuentes. Estas opciones describen actividades; los valores se registrarán en las casillas del formulario aplicable por año, residencia y régimen.</p>
    <div class="profile-grid">
        <?php foreach (ContribuyenteData::FUENTES as $key => $label): ?>
            <?php
            $labels = [];
            foreach ($sourceProfiles as $type => $options) {
                if (isset($options[$key])) { $labels[$type] = $options[$key]; }
            }
            ?>
            <div data-source-labels="<?= e(json_encode($labels, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR)) ?>">
                <label class="flex items-center gap-space-sm text-body-md">
                    <input type="checkbox" name="fuentes[]" value="<?= e($key) ?>" <?= in_array($key, $record['fuentes'], true) ? 'checked' : '' ?>>
                    <span data-source-label><?= e(ContribuyenteData::sourcesFor($record['tipo'])[$key] ?? $label) ?></span>
                </label>
            </div>
        <?php endforeach; ?>
    </div>
    <p class="text-body-md text-secondary" id="sources-change" hidden>Las fuentes que no aplican al tipo seleccionado no se enviarán al guardar. Si vuelves al tipo anterior antes de guardar, conservarás sus selecciones.</p>
    <noscript><p class="text-body-md text-secondary">Sin JavaScript se muestran todas las opciones. Para personas jurídicas no selecciones salarios ni pensiones; el servidor valida las fuentes al guardar.</p></noscript>
</fieldset>
