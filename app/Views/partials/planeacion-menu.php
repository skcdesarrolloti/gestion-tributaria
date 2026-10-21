<?php $planningActive=in_array(input($_GET,'route'),['beneficios','simulador','seguridad-social'],true); ?>
<details class="exogena-nav" data-planeacion-menu>
    <summary class="flex items-center gap-space-sm rounded-xl px-3 py-2.5 <?= $planningActive?'bg-inverse-surface text-on-secondary':'bg-surface-container-low text-secondary' ?>">
        <span aria-hidden="true" class="material-symbols-outlined">calculate</span>
        <span class="font-semibold">Planeación</span>
        <span aria-hidden="true" class="material-symbols-outlined exogena-nav-chevron">expand_more</span>
    </summary>
    <div class="exogena-nav-panel bg-surface-container-lowest border border-surface-container rounded-2xl shadow-card p-4 space-y-space-sm">
        <a class="block rounded-xl px-3 py-2.5 text-secondary" href="<?= e(url($config,'contribuyentes')) ?>">1 · Contribuyentes y perfil</a>
        <a class="block rounded-xl px-3 py-2.5 text-secondary" href="<?= e(url($config,'simulador')) ?>">2 · Hoja de planeación</a>
        <details class="rounded-xl border border-surface-container p-2">
            <summary class="font-semibold text-secondary">3 · Beneficios por categoría</summary>
            <a class="block px-3 py-2.5 text-secondary" href="<?= e(url($config,'beneficios')) ?>">Ver categorías y buscar</a>
            <?php foreach (\App\Services\BeneficioCatalog::groups() as $menuKey=>$menuLabel): ?>
                <a class="block px-3 py-2.5 text-secondary" href="<?= e(url($config,'beneficios').'&grupo='.$menuKey) ?>"><?= e($menuLabel) ?></a>
            <?php endforeach; ?>
        </details>
        <a class="block rounded-xl px-3 py-2.5 text-secondary" href="<?= e(url($config,'seguridad-social')) ?>">4 · Seguridad social · revisión</a>
    </div>
</details>
