<section class="space-y-space-md" aria-label="Categorías de beneficios">
    <h2 class="text-headline-md font-semibold">Elige qué necesitas revisar</h2>
    <p class="text-body-md text-secondary">52 fichas organizadas en siete categorías. Entra a una categoría y abre solo la explicación que necesites.</p>
    <div class="exogena-format-grid">
        <?php foreach ($groups as $category=>$label): ?>
            <a class="block rounded-xl border border-surface-container bg-surface-container-lowest p-4" href="<?= e(url($config,'beneficios').'&anio='.$year.'&grupo='.$category) ?>">
                <h3 class="text-headline-sm font-semibold"><?= e($label) ?></h3>
                <p class="text-body-md text-secondary mt-2"><?= e($counts[$category]??0) ?> fichas · Ver conceptos y explicaciones</p>
            </a>
        <?php endforeach; ?>
    </div>
</section>
