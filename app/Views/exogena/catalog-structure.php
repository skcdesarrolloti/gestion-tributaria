<section class="bg-surface-container-lowest rounded-2xl border border-surface-container p-space-xl space-y-space-md">
    <h2 class="text-headline-md font-semibold">Estructura del formato <?= e($schema['code']) ?></h2>
    <p class="text-body-md text-secondary">Cliente y año se seleccionan aparte. Campos en el orden del anexo de referencia.</p>
    <div class="profile-grid">
        <?php foreach (\App\Services\ExogenaSchemas::sections($schema) as $section): ?>
            <article class="rounded-xl border border-surface-container p-4 space-y-space-sm">
                <h3 class="font-semibold"><?= e($section['name']) ?></h3>
                <ul class="space-y-space-sm text-body-md">
                    <?php foreach ($section['fields'] as $field): ?><li class="taxpayer-name"><?= e($field[0]) ?></li><?php endforeach; ?>
                </ul>
            </article>
        <?php endforeach; ?>
    </div>
</section>
