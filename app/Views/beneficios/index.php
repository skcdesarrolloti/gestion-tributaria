<?php $title='Beneficios · Personas naturales'; require __DIR__.'/../partials/head.php'; ?>
<body class="bg-background min-h-screen font-body-md text-on-surface antialiased">
<?php require __DIR__.'/../partials/header.php'; ?>
<main class="portal-container space-y-space-lg py-8">
    <section class="bg-inverse-surface text-on-secondary rounded-2xl p-space-xl space-y-space-sm">
        <h1 class="text-headline-lg font-semibold">Planeación de socios y familia</h1>
        <p class="text-body-md">Costos, deducciones y beneficios para personas naturales residentes en régimen ordinario.</p>
        <p class="text-body-md">Primero identifica la fuente de ingreso de cada persona. Ser socio no otorga por sí solo una deducción.</p>
    </section>
    <details class="bg-primary-container rounded-2xl p-4 space-y-space-sm">
        <summary class="text-headline-sm font-semibold">? · Cómo usar el catálogo y su alcance</summary>
        <p class="text-body-md">Reúne 46 conceptos para revisar y 6 controles, con fundamento, condiciones, soportes y acciones antes del cierre. Un desembolso no garantiza ahorro: verifica el costo, el cupo y el efecto en caja.</p>
        <p class="text-body-md">Este catálogo explica los conceptos. La Hoja de planeación permite registrar escenarios y calcular reglas comunes; señala qué fichas requieren un dato verificado o una regla particular pendiente.</p>
        <a class="inline-block rounded-xl bg-inverse-surface text-on-secondary px-4 py-2" href="<?= e(url($config,'simulador')) ?>">Abrir hoja de planeación</a>
        <p class="text-body-md">Los casos especiales requieren revisión individual. No es un inventario exhaustivo de todos los incentivos sectoriales. SIMPLE y no residentes necesitan reglas separadas.</p>
    </details>
    <?php require __DIR__.'/filters.php'; ?>
    <?php if (!$showItems): require __DIR__.'/categories.php'; else: ?>
    <details class="bg-surface-container-lowest rounded-xl border border-surface-container p-4">
        <summary class="font-semibold text-secondary">? · Límites compartidos y UVT del año</summary>
        <?php require __DIR__.'/limits.php'; ?>
    </details>
    <section class="space-y-space-md" aria-label="Catálogo de conceptos">
        <h2 class="text-headline-md font-semibold"><?= e($group!==''?$groups[$group]:'Resultados de búsqueda') ?></h2>
        <p><?= e($total) ?> fichas encontradas · Página <?= e($page) ?> de <?= e($pages) ?> · Hasta nueve por página</p>
        <p class="text-body-md text-secondary">Abre cada ficha para ver alcance y soportes. Referencias revisadas: <?= e(\App\Services\BeneficioCatalog::REVIEWED) ?>. Destino por bloque del formulario 210; la casilla exacta se confirmará con su versión e instructivo antes de calcular.</p>
        <?php if (!$items): ?><p role="status">No se encontraron conceptos. Prueba otra búsqueda o selecciona todos los grupos.</p><?php endif; ?>
        <div class="exogena-format-grid beneficios-grid">
            <?php foreach ($items as $item): ?>
                <?php require __DIR__.'/card.php'; ?>
            <?php endforeach; ?>
        </div>
        <?php require __DIR__.'/pagination.php'; ?>
    </section>
    <?php endif; ?>
</main>
<script src="<?= e(asset($config,'js/normas.js').'?v='.filemtime(dirname(__DIR__,3).'/public/assets/js/normas.js')) ?>" defer></script>
<?php require __DIR__.'/../partials/footer.php'; ?>
