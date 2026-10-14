<section class="bg-surface-container-lowest rounded-2xl border border-surface-container p-space-xl space-y-space-md">
    <h2 class="text-headline-md font-semibold">Seleccionar empresa y año</h2>
    <?php if (!$companies): ?><p class="text-body-md">Primero registra una persona jurídica en Contribuyentes.</p><?php endif; ?>
    <form method="get" action="<?= e($config['base_path'].'/index.php') ?>" class="space-y-space-md">
        <input type="hidden" name="route" value="<?= e('exogena/'.$formatCode) ?>">
        <div class="profile-grid">
            <label class="text-body-md">Empresa informante *
                <select name="contribuyente" required class="mt-2 w-full px-space-md py-space-md border-0 rounded-xl bg-surface-container-low">
                    <option value="">Selecciona una persona jurídica</option>
                    <?php foreach ($companies as $c): ?><option value="<?= e($c['id']) ?>" <?= $company && (int)$company['id']===(int)$c['id']?'selected':'' ?>><?= e($c['nombre']) ?> · <?= e($c['documento_numero']) ?></option><?php endforeach; ?>
                </select>
            </label>
            <label class="text-body-md">Año gravable *
                <input name="anio" value="<?= e($year) ?>" inputmode="numeric" pattern="20[0-9]{2}" maxlength="4" required class="mt-2 w-full px-space-md py-space-md border-0 rounded-xl bg-surface-container-low">
                <span class="block text-secondary mt-2">Año de las operaciones. Consulta la referencia de estructura indicada en este formato.</span>
            </label>
        </div>
        <button class="rounded-xl bg-inverse-surface text-on-secondary px-4 py-2" type="submit">Consultar registros</button>
    </form>
    <a class="text-secondary underline" href="<?= e(url($config,'contribuyentes/formulario')) ?>">Registrar empresa</a>
</section>
