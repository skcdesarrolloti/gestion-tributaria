<?php $title='Exógena'; require __DIR__.'/../partials/head.php'; ?>
<body class="bg-background min-h-screen font-body-md text-on-surface antialiased">
<?php require __DIR__.'/../partials/header.php'; ?>
<main class="portal-container space-y-space-lg py-8">
    <section class="bg-inverse-surface text-on-secondary rounded-2xl p-space-xl">
        <h1 class="text-headline-lg font-semibold">Exógena</h1>
        <p class="text-body-md mt-2">Módulo independiente para preparar información de personas jurídicas por empresa y año gravable.</p>
    </section>
    <section class="bg-surface-container-lowest rounded-2xl border border-surface-container p-space-xl space-y-space-md">
        <h2 class="text-headline-md font-semibold">5247</h2>
        <p class="text-body-md">Pagos o abonos en cuenta y retenciones practicadas en contratos de colaboración empresarial y otros contratos.</p>
        <p class="text-body-md text-secondary">Primera estructura basada en las 22 columnas de la plantilla 2024. Permite registrar y editar borradores; la preparación para envío a la DIAN se revisará por año.</p>
        <a class="inline-block rounded-xl bg-inverse-surface text-on-secondary px-4 py-2" href="<?= e(url($config,'exogena/5247')) ?>">Abrir 5247</a>
    </section>
</main>
<?php require __DIR__.'/../partials/footer.php'; ?>
