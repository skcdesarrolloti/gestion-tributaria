<?php $title = 'Inicio'; require __DIR__ . '/../partials/head.php'; ?>
<body class="bg-background min-h-screen font-body-md text-on-surface antialiased">
<?php require __DIR__ . '/../partials/header.php'; ?>
<main class="portal-container space-y-space-lg py-8">
    <section class="bg-inverse-surface text-on-secondary rounded-2xl p-space-xl">
        <p class="text-label-md uppercase tracking-wide"><?= e($config['name']) ?></p>
        <h1 class="text-headline-lg font-semibold mt-2">Hola, <?= e($user['nombre']) ?></h1>
        <p class="text-body-md mt-2">Bienvenido al portal. Aquí encontrarás las herramientas de tu equipo.</p>
    </section>
    <section class="bg-surface-container-lowest rounded-2xl border border-surface-container p-space-xl">
        <span aria-hidden="true" class="material-symbols-outlined bg-primary-container rounded-xl p-3">group</span>
        <h2 class="text-headline-md font-semibold mt-4">Paso 1 · Contribuyentes</h2>
        <p class="text-body-md text-secondary mt-2">Empieza por identificar a la persona o entidad cuya situación tributaria vas a analizar.</p>
        <p class="text-body-md text-secondary mt-2">Registra sus datos y fuentes de ingresos. Después revisaremos las cédulas y los beneficios aplicables.</p>
        <a class="inline-block mt-4 rounded-xl bg-inverse-surface text-on-secondary px-4 py-2" href="<?= e(url($config, 'contribuyentes')) ?>">Ir a contribuyentes</a>
    </section>
    <section class="bg-surface-container-lowest rounded-2xl border border-surface-container p-space-xl">
        <h2 class="text-headline-md font-semibold">Exógena</h2>
        <p class="text-body-md text-secondary mt-2">Prepara los formatos 1007 · Ingresos Propios y 5247 · Pagos o Abonos en cuenta, por persona jurídica y año.</p>
        <a class="inline-block mt-4 rounded-xl bg-inverse-surface text-on-secondary px-4 py-2" href="<?= e(url($config,'exogena')) ?>">Abrir Exógena</a>
    </section>
</main>
<?php require __DIR__ . '/../partials/footer.php'; ?>
