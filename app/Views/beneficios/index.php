<?php $title='Beneficios · Personas naturales'; require __DIR__.'/../partials/head.php'; ?>
<body class="bg-background min-h-screen font-body-md text-on-surface antialiased">
<?php require __DIR__.'/../partials/header.php'; ?>
<main class="portal-container space-y-space-lg py-8">
    <section class="bg-inverse-surface text-on-secondary rounded-2xl p-space-xl space-y-space-sm">
        <h1 class="text-headline-lg font-semibold">Planeación de socios y familia</h1>
        <p class="text-body-md">Costos, deducciones y beneficios para personas naturales residentes en régimen ordinario.</p>
        <p class="text-body-md">Primero identifica la fuente de ingreso de cada persona. Ser socio no otorga por sí solo una deducción.</p>
    </section>
    <section class="bg-primary-container rounded-2xl p-4 space-y-space-sm">
        <h2 class="text-headline-sm font-semibold">Qué hace este catálogo</h2>
        <p class="text-body-md">Reúne 46 conceptos para revisar y 6 controles, con fundamento, condiciones, soportes y acciones antes del cierre. Un desembolso no garantiza ahorro: verifica el costo, el cupo y el efecto en caja.</p>
        <p class="text-body-md">Es una consulta inicial: no asigna beneficios a una persona, no registra gastos ni calcula todavía el impuesto. Compararemos estas fichas con el PDF del contador antes de habilitar su aplicación a escenarios.</p>
        <p class="text-body-md">Los casos especiales requieren revisión individual. No es un inventario exhaustivo de todos los incentivos sectoriales. SIMPLE y no residentes necesitan reglas separadas.</p>
    </section>
    <?php require __DIR__.'/filters.php'; ?>
    <?php require __DIR__.'/limits.php'; ?>
    <section class="space-y-space-md" aria-label="Catálogo de conceptos">
        <h2 class="text-headline-md font-semibold"><?= count($items) ?> fichas encontradas</h2>
        <p class="text-body-md text-secondary">Abre cada ficha para ver alcance y soportes. Referencias revisadas: <?= e(\App\Services\BeneficioCatalog::REVIEWED) ?>. Destino por bloque del formulario 210; la casilla exacta se confirmará con su versión e instructivo antes de calcular.</p>
        <?php if (!$items): ?><p role="status">No se encontraron conceptos. Prueba otra búsqueda o selecciona todos los grupos.</p><?php endif; ?>
        <div class="exogena-format-grid">
            <?php foreach ($items as $item): ?>
                <?php require __DIR__.'/card.php'; ?>
            <?php endforeach; ?>
        </div>
    </section>
</main>
<?php require __DIR__.'/../partials/footer.php'; ?>
