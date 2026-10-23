<article id="<?= e($item['id']) ?>" class="border border-surface-container rounded-xl p-4 bg-surface-container-lowest space-y-space-sm taxpayer-name">
    <p class="text-label-sm text-secondary"><?= e($groups[$item['group']]) ?></p>
    <h3 class="text-headline-sm font-semibold"><?= e($item['title']) ?></h3>
    <p class="text-body-md"><strong>Fundamento:</strong> <?= e($item['norm']) ?></p>
    <details class="space-y-space-sm">
        <summary class="text-secondary underline">Ver alcance, límites y acción</summary>
        <p class="text-body-md"><strong>A quién puede aplicar:</strong> <?= e($item['scope']) ?></p>
        <p class="text-body-md"><strong>Alcance y límites:</strong> <?= e($item['limit']) ?></p>
        <p class="text-body-md"><strong>Qué debes conservar:</strong> <?= e($item['evidence']) ?></p>
        <p class="text-body-md"><strong>Acción antes del cierre:</strong> <?= e($item['action']) ?></p>
        <p class="text-label-sm">Aplicación individual y casilla: pendientes de validar. La Hoja de planeación identifica la cobertura de cálculo de cada ficha; el PDF revisado no contiene todas estas reglas.</p>
    </details>
    <?php require __DIR__.'/articles.php'; ?>
</article>
