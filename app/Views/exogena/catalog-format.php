<?php $title='Exógena · '.$schema['code']; $formatCode=$schema['code']; require __DIR__.'/../partials/head.php'; ?>
<body class="bg-background min-h-screen font-body-md text-on-surface antialiased">
<?php require __DIR__.'/../partials/header.php'; ?>
<main class="portal-container space-y-space-lg py-8">
    <section class="bg-inverse-surface text-on-secondary rounded-2xl p-space-xl">
        <p class="text-label-md uppercase tracking-wide">Exógena · Personas jurídicas · <?= e($schema['year']) ?></p>
        <h1 class="text-headline-lg font-semibold mt-2"><?= e($schema['code'].' · '.$schema['name']) ?></h1>
        <?php if ($company): ?><p class="text-body-md mt-2 taxpayer-name">Cliente: <?= e($company['nombre']) ?></p><?php endif; ?>
    </section>
    <?php if ($notice!==''): ?><p role="status" class="bg-primary-container rounded-xl p-3"><?= e($notice) ?></p><?php endif; ?>
    <?php require __DIR__.'/company-selector.php'; ?>
    <?php require __DIR__.'/catalog-notice.php'; ?>
    <?php if ($company): ?>
        <section class="bg-surface-container-lowest rounded-2xl border border-surface-container p-space-xl space-y-space-md">
            <h2 class="text-headline-md font-semibold">Registros de <?= e($year) ?> · <?= count($records) ?></h2>
            <?php if (!$records): ?><p class="text-body-md">Todavía no hay información cargada para este cliente, formato y año.</p><?php endif; ?>
            <a class="inline-block rounded-xl bg-inverse-surface text-on-secondary px-4 py-2" href="<?= e(url($config,'exogena/'.$formatCode.'/registro').'&contribuyente='.$company['id'].'&anio='.$year) ?>">Nuevo registro <?= e($formatCode) ?></a>
            <?php foreach ($records as $row): ?>
                <article class="rounded-xl border border-surface-container p-4 space-y-space-sm taxpayer-name">
                    <h3 class="font-semibold"><?= e(\App\Services\ExogenaSchemas::identity($schema,$row['datos'])) ?></h3>
                    <p class="text-body-md"><?= e($schema['fields'][$schema['amount']][0]) ?>: $<?= e(\App\Services\ExogenaSchemas::money($row['datos'][$schema['amount']])) ?></p>
                    <p class="text-body-md text-secondary">Referencia <?= e($row['estructura']) ?> · Actualizado <?= e($row['updated_at']) ?></p>
                    <a class="text-secondary underline" href="<?= e(url($config,'exogena/'.$formatCode.'/registro').'&id='.$row['id']) ?>">Ver y editar registro</a>
                </article>
            <?php endforeach; ?>
        </section>
    <?php endif; ?>
    <details class="space-y-space-md">
        <summary class="text-headline-sm font-semibold">Ver todos los campos de la estructura</summary>
        <?php require __DIR__.'/catalog-structure.php'; ?>
    </details>
    <a class="text-secondary underline" href="<?= e(url($config,'exogena').($company?'&contribuyente='.$company['id'].'&anio='.$year:'')) ?>">Volver a formatos del cliente</a>
</main>
<?php require __DIR__.'/../partials/footer.php'; ?>
