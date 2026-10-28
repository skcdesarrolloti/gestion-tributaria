<nav class="portal-container registro-section-tabs" aria-label="Secciones de Registro de información">
<?php foreach (\App\Services\RegistroNavegacion::menu($menuRoute,$_GET,$menuTaxpayer,$menuYear) as $tab): ?>
<a class="rounded-xl px-3 py-2 <?= $tab['active']?'bg-inverse-surface text-on-secondary':'bg-surface-container-low text-secondary' ?>" <?= $tab['active']?'aria-current="page"':'' ?> href="<?= e(url($config,$tab['route']).$tab['query']) ?>"><?= e($tab['label']) ?></a>
<?php endforeach; ?>
</nav>
