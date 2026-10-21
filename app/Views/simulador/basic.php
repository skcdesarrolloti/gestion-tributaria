<section class="bg-surface-container-lowest rounded-2xl border border-surface-container p-4 space-y-space-md">
    <h2 class="text-headline-md font-semibold">3 · Ingresos anuales, bases, patrimonio y pagos previos</h2>
    <p>Escribe el importe bruto en su fuente. No registres un salario ni una pensión nuevamente aquí. Las bases certificadas se usan solo cuando solicitas el beneficio relacionado.</p>
    <div class="profile-grid">
        <?php foreach (\App\Services\SimuladorFields::basic() as $field=>[$label,$norm]): ?>
            <label><?= e($label) ?><input name="<?= e($field) ?>" value="<?= e($record[$field]) ?>" inputmode="numeric" pattern="[0-9]*" maxlength="12" class="mt-2 w-full px-space-md py-space-md rounded-xl bg-surface-container-low"><span class="block text-secondary text-label-sm mt-2"><?= e($norm) ?></span></label>
        <?php endforeach; ?>
    </div>
</section>
