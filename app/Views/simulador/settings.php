<?php require __DIR__.'/settings-options.php'; ?>
<section class="bg-surface-container-lowest rounded-2xl border border-surface-container p-4 space-y-space-md">
    <h2 class="text-headline-md font-semibold">1 · Escenario y condiciones</h2>
    <p class="text-body-md text-secondary">Empieza por Situación actual. Abre las ayudas «?» para saber qué escoger. Si un dato no está claro, déjalo por confirmar: puedes guardar la versión con el cálculo pendiente.</p>
    <div class="scenario-settings-grid">
        <div>
            <label for="escenario-nombre">Nombre del escenario</label>
            <select id="escenario-nombre" name="nombre" class="mt-2 w-full px-space-md py-space-md rounded-xl bg-surface-container-low">
                <option value="">Selecciona un escenario</option>
                <?php foreach ($scenarioNames as $name): ?><option value="<?= e($name) ?>" <?= $record['nombre']===$name?'selected':'' ?>><?= e($name) ?></option><?php endforeach; ?>
            </select>
            <?php $helpTitle='Nombre del escenario'; $helpArticle=null;
            $helpText='Es una etiqueta para reconocer la versión, no una casilla de la DIAN. Situación actual sirve para empezar; Proyección al cierre del año incluye lo que esperas recibir y pagar hasta diciembre; Alternativa para comparar sirve para ensayar una acción. La etiqueta no modifica los valores ni el impuesto: debes cambiar las celdas correspondientes.';
            require __DIR__.'/../partials/field-help.php'; ?>
        </div>
        <?php foreach ($settings as $key=>$setting): ?>
            <div>
                <label for="escenario-<?= e($key) ?>"><?= e($setting['label']) ?></label>
                <select id="escenario-<?= e($key) ?>" name="<?= e($key) ?>" aria-describedby="escenario-<?= e($key) ?>-hint" class="mt-2 w-full px-space-md py-space-md rounded-xl bg-surface-container-low">
                    <option value="" <?= $record[$key]===''?'selected':'' ?>>No lo sé / por confirmar</option>
                    <?php foreach ($setting['options'] as $value=>$text): ?><option value="<?= e($value) ?>" <?= $record[$key]===(string)$value?'selected':'' ?>><?= e($text) ?></option><?php endforeach; ?>
                </select>
                <p id="escenario-<?= e($key) ?>-hint" class="text-body-md text-secondary mt-2"><?= e($setting['hint']) ?></p>
                <?php $helpTitle=$setting['label']; $helpText=$setting['help']; $helpArticle=$setting['article']; require __DIR__.'/../partials/field-help.php'; ?>
            </div>
        <?php endforeach; ?>
    </div>
    <label class="block" for="escenario-notas">¿Qué falta confirmar? (opcional)</label>
    <textarea id="escenario-notas" name="notas" maxlength="1500" rows="3" aria-describedby="escenario-notas-hint" placeholder="Ejemplo: falta el certificado del banco; confirmar los arriendos de noviembre y diciembre." class="mt-2 w-full px-space-md py-space-md rounded-xl bg-surface-container-low"><?= e($record['notas']) ?></textarea>
    <p id="escenario-notas-hint" class="text-body-md text-secondary">Aquí escribes recordatorios, documentos pendientes o supuestos de la proyección. Este texto no suma ni resta valores y no adjunta archivos.</p>
</section>
