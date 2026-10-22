<details class="exogena-nav" data-portal-menu>
<summary class="flex items-center gap-space-sm rounded-xl px-3 py-2.5 bg-surface-container-low text-secondary"><span class="font-semibold">Beneficios tributarios</span><span aria-hidden="true" class="material-symbols-outlined exogena-nav-chevron">expand_more</span></summary>
<div class="exogena-nav-panel bg-surface-container-lowest border border-surface-container rounded-2xl shadow-card p-4 space-y-space-sm">
<a class="block px-3 py-2.5 text-secondary" href="<?= e(url($config,'beneficios')) ?>">Ver categorías y buscar</a>
<?php foreach (\App\Services\BeneficioCatalog::groups() as $key=>$label): ?><a class="block px-3 py-2.5 text-secondary" href="<?= e(url($config,'beneficios').'&grupo='.$key) ?>"><?= e($label) ?></a><?php endforeach; ?>
</div></details>
<details class="exogena-nav" data-portal-menu>
<summary class="flex items-center gap-space-sm rounded-xl px-3 py-2.5 bg-surface-container-low text-secondary"><span class="font-semibold">Registro de información</span><span aria-hidden="true" class="material-symbols-outlined exogena-nav-chevron">expand_more</span></summary>
<div class="exogena-nav-panel bg-surface-container-lowest border border-surface-container rounded-2xl shadow-card p-4 space-y-space-sm">
<p>Selecciona contribuyente y año 2025 o 2026 dentro del registro.</p>
<a class="block px-3 py-2.5 text-secondary" href="<?= e(url($config,'registro').'&componente=ingresos') ?>">Ingresos</a>
<a class="block px-3 py-2.5 text-secondary" href="<?= e(url($config,'registro').'&componente=costos') ?>">Costos y gastos</a>
</div></details>
<a class="text-secondary" href="<?= e(url($config,'analisis')) ?>">Análisis y acciones</a>
