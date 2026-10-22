<?php $title = 'Inicio'; require __DIR__ . '/../partials/head.php'; ?>
<body class="bg-background min-h-screen font-body-md text-on-surface antialiased">
<?php require __DIR__ . '/../partials/header.php'; ?>
<main class="portal-container space-y-space-lg py-8">
    <section class="bg-inverse-surface text-on-secondary rounded-2xl p-space-xl">
        <p class="text-label-md uppercase tracking-wide"><?= e($config['name']) ?></p>
        <h1 class="text-headline-lg font-semibold mt-2">Hola, <?= e($user['nombre']) ?></h1>
        <p class="text-body-md mt-2">Bienvenido al portal. Aquí encontrarás las herramientas de tu equipo.</p>
    </section>
    <div class="scenario-settings-grid" aria-label="Módulos de Gestión Tributaria">
    <section class="bg-surface-container-lowest rounded-2xl border border-surface-container p-4">
        <span aria-hidden="true" class="material-symbols-outlined bg-primary-container rounded-xl p-3">group</span>
        <h2 class="text-headline-md font-semibold mt-4">Paso 1 · Contribuyentes</h2>
        <p class="text-body-md text-secondary mt-2">Abre su perfil, sube el RUT y consulta su información organizada por pestañas.</p>
        <a class="inline-block mt-4 rounded-xl bg-inverse-surface text-on-secondary px-4 py-2" href="<?= e(url($config, 'contribuyentes')) ?>">Ir a contribuyentes</a>
    </section>
    <section class="bg-surface-container-lowest rounded-2xl border border-surface-container p-4">
        <span aria-hidden="true" class="material-symbols-outlined bg-primary-container rounded-xl p-3">calculate</span>
        <h2 class="text-headline-md font-semibold mt-4">Paso 2 · Hoja de planeación</h2>
        <p class="text-body-md text-secondary mt-2">Diligencia las celdas, realiza los primeros cálculos de renta y compara tus escenarios guardados.</p>
        <a class="inline-block mt-4 rounded-xl bg-inverse-surface text-on-secondary px-4 py-2" href="<?= e(url($config,'simulador')) ?>">Abrir hoja de cálculos</a>
    </section>
    <section class="bg-surface-container-lowest rounded-2xl border border-surface-container p-4">
        <span aria-hidden="true" class="material-symbols-outlined bg-primary-container rounded-xl p-3">menu_book</span>
        <h2 class="text-headline-md font-semibold mt-4">Paso 3 · Deducciones y beneficios</h2>
        <p class="text-body-md text-secondary mt-2">Consulta las 52 fichas por categoría: costos, deducciones, exenciones y descuentos, con fundamento normativo, límites y soportes.</p>
        <a class="inline-block mt-4 rounded-xl bg-inverse-surface text-on-secondary px-4 py-2" href="<?= e(url($config,'beneficios')) ?>">Consultar deducciones y normas</a>
    </section>
    <section class="bg-surface-container-lowest rounded-2xl border border-surface-container p-4">
        <span aria-hidden="true" class="material-symbols-outlined bg-primary-container rounded-xl p-3">folder_open</span>
        <h2 class="text-headline-md font-semibold mt-4">Exógena · Módulo independiente</h2>
        <p class="text-body-md text-secondary mt-2">Abre los formatos de información exógena por empresa y año, con sus campos y referencias.</p>
        <a class="inline-block mt-4 rounded-xl bg-inverse-surface text-on-secondary px-4 py-2" href="<?= e(url($config,'exogena')) ?>">Abrir Exógena</a>
    </section>
    </div>
</main>
<?php require __DIR__ . '/../partials/footer.php'; ?>
