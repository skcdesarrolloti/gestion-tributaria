<?php $title='Exógena'; require __DIR__.'/../partials/head.php'; ?>
<body class="bg-background min-h-screen font-body-md text-on-surface antialiased">
<?php require __DIR__.'/../partials/header.php'; ?>
<main class="portal-container space-y-space-lg py-8">
    <section class="bg-inverse-surface text-on-secondary rounded-2xl p-space-xl">
        <h1 class="text-headline-lg font-semibold">Exógena</h1>
        <p class="text-body-md mt-2">Módulo independiente para preparar información de personas jurídicas por empresa y año gravable.</p>
    </section>
    <section class="space-y-space-md">
        <h2 class="text-headline-md font-semibold">Formatos disponibles</h2>
        <p class="text-body-md text-secondary">Cada formato tiene su propio módulo. Aquí encontrarás los que vayamos incorporando.</p>
        <div class="profile-grid">
            <?php foreach (\App\Services\ExogenaFormats::all() as $code=>$format): ?>
                <article class="bg-surface-container-lowest rounded-2xl border border-surface-container p-space-xl space-y-space-md">
                    <div class="flex items-center gap-space-md">
                        <span aria-hidden="true" class="material-symbols-outlined bg-primary-container rounded-xl p-3">description</span>
                        <h3 class="text-headline-md font-semibold">Formato <?= e($code) ?></h3>
                    </div>
                    <p class="font-semibold"><?= e($format['name']) ?></p>
                    <p class="text-body-md text-secondary"><?= e($format['description']) ?></p>
                    <a class="inline-block rounded-xl bg-inverse-surface text-on-secondary px-4 py-2" href="<?= e(url($config,$format['route'])) ?>">Abrir <?= e($code) ?></a>
                </article>
            <?php endforeach; ?>
        </div>
    </section>
    <?php require __DIR__.'/structure.php'; ?>
    <?php require __DIR__.'/structure-1007.php'; ?>
</main>
<?php require __DIR__.'/../partials/footer.php'; ?>
