<?php $title='Exógena'; require __DIR__.'/../partials/head.php'; ?>
<body class="bg-background min-h-screen font-body-md text-on-surface antialiased">
<?php require __DIR__.'/../partials/header.php'; ?>
<main class="portal-container space-y-space-lg py-8">
    <section class="bg-inverse-surface text-on-secondary rounded-2xl p-space-xl">
        <h1 class="text-headline-lg font-semibold">Exógena</h1>
        <p class="text-body-md mt-2">Módulo independiente para preparar información de personas jurídicas por empresa y año gravable.</p>
    </section>
    <?php $selectorRoute='exogena'; require __DIR__.'/company-selector.php'; ?>
    <section class="bg-primary-container rounded-2xl p-4 space-y-space-sm">
        <h2 class="text-headline-sm font-semibold">Preparación de todos tus clientes · 2025</h2>
        <p class="text-body-md">Selecciona la empresa para abrir sus formatos. Las estructuras nuevas están vacías; cada registro pertenece a un cliente y un formato.</p>
        <?php if ($company): ?><p class="text-body-md taxpayer-name"><strong>Cliente seleccionado: <?= e($company['nombre']) ?>.</strong> Las tarjetas muestran cuántos registros tiene cada formato; tener registros no acredita que esté completo o validado.</p><?php endif; ?>
        <p class="text-body-md">La carga mensual y su conciliación con los acumulados anuales se desarrollarán después de recibir los archivos. Los saldos de cuentas representan un corte y no deben sumarse entre meses.</p>
    </section>
    <?php foreach (\App\Services\ExogenaFormats::grouped() as $group): ?>
    <section class="space-y-space-md">
        <h2 class="text-headline-md font-semibold"><?= e($group['name']) ?></h2>
        <div class="exogena-format-grid">
            <?php foreach ($group['formats'] as $code=>$format): ?>
                <article class="bg-surface-container-lowest rounded-2xl border border-surface-container p-space-xl space-y-space-md">
                    <div class="flex items-center gap-space-md">
                        <span aria-hidden="true" class="material-symbols-outlined bg-primary-container rounded-xl p-3">description</span>
                        <h3 class="text-headline-md font-semibold">Formato <?= e($code) ?></h3>
                    </div>
                    <p class="font-semibold"><?= e($format['name']) ?></p>
                    <p class="text-body-md text-secondary"><?= e($format['description']) ?></p>
                    <?php if ($company): ?><p class="text-body-md font-semibold"><?= (int)($counts[$code]??0)>0?'Registros en preparación: '.e($counts[$code]):'Sin información cargada' ?></p><?php endif; ?>
                    <a class="inline-block rounded-xl bg-inverse-surface text-on-secondary px-4 py-2" href="<?= e(url($config,$format['route']).($company?'&contribuyente='.$company['id'].'&anio='.$year:'')) ?>">Abrir <?= e($code) ?></a>
                </article>
            <?php endforeach; ?>
        </div>
    </section>
    <?php endforeach; ?>
</main>
<?php require __DIR__.'/../partials/footer.php'; ?>
