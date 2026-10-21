<section class="bg-surface-container-lowest rounded-2xl border border-surface-container p-4 space-y-space-md">
    <h2 class="text-headline-sm font-semibold">Buscar una oportunidad de planeación</h2>
    <form method="get" action="<?= e($config['base_path'].'/index.php') ?>" class="space-y-space-md">
        <input type="hidden" name="route" value="beneficios">
        <div class="profile-grid">
            <label>Año gravable
                <select name="anio" class="mt-2 w-full px-space-md py-space-md rounded-xl bg-surface-container-low">
                    <?php foreach (['2025','2026'] as $option): ?><option <?= $year===$option?'selected':'' ?>><?= e($option) ?></option><?php endforeach; ?>
                </select>
            </label>
            <label>Tipo de concepto
                <select name="grupo" class="mt-2 w-full px-space-md py-space-md rounded-xl bg-surface-container-low">
                    <option value="">Todos los grupos</option>
                    <?php foreach ($groups as $value=>$label): ?><option value="<?= e($value) ?>" <?= $group===$value?'selected':'' ?>><?= e($label) ?></option><?php endforeach; ?>
                </select>
            </label>
            <label>Palabra o artículo del ET
                <input name="buscar" value="<?= e($query) ?>" maxlength="100" placeholder="Ejemplo: arriendos, salud, 119" class="mt-2 w-full px-space-md py-space-md rounded-xl bg-surface-container-low">
            </label>
        </div>
        <button class="rounded-xl bg-inverse-surface text-on-secondary px-4 py-2" type="submit">Consultar</button>
        <a class="text-secondary underline" href="<?= e(url($config,'beneficios').'&anio='.$year) ?>">Ver todo el listado</a>
    </form>
</section>
