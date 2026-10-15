<?php $exogenaActive=str_starts_with(input($_GET,'route'),'exogena'); ?>
<details class="exogena-nav" data-exogena-menu>
    <summary class="flex items-center gap-space-sm rounded-xl px-3 py-2.5 <?= $exogenaActive?'bg-inverse-surface text-on-secondary':'bg-surface-container-low text-secondary' ?>">
        <span aria-hidden="true" class="material-symbols-outlined">folder_open</span>
        <span class="font-semibold">Exógena</span>
        <span aria-hidden="true" class="material-symbols-outlined exogena-nav-chevron">expand_more</span>
    </summary>
    <div class="exogena-nav-panel bg-surface-container-lowest border border-surface-container rounded-2xl shadow-card p-4 space-y-space-sm">
        <p class="text-label-md uppercase tracking-wide text-secondary">Formatos de información</p>
        <?php $menuContext=isset($company['id'])?'&contribuyente='.$company['id'].'&anio=2025':''; ?>
        <a class="block rounded-xl px-3 py-2.5 bg-surface-container-low text-secondary" href="<?= e(url($config,'exogena').$menuContext) ?>">Ver todos los formatos</a>
        <?php foreach (\App\Services\ExogenaFormats::grouped() as $menuGroup): ?>
            <details class="rounded-xl border border-surface-container p-2">
                <summary class="text-body-md font-semibold text-secondary"><?= e($menuGroup['name']) ?></summary>
                <?php foreach ($menuGroup['formats'] as $code=>$format): ?>
                    <a class="flex items-center gap-space-md rounded-xl px-3 py-2.5 text-on-surface" href="<?= e(url($config,$format['route']).$menuContext) ?>">
                        <span class="bg-primary-container rounded-xl p-2 font-semibold"><?= e($code) ?></span>
                        <span class="text-body-md"><?= e($format['name']) ?></span>
                    </a>
                <?php endforeach; ?>
            </details>
        <?php endforeach; ?>
    </div>
</details>
