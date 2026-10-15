<?php $title='Exógena · 1007 · Ingresos Recibidos'; $formatCode='1007'; require __DIR__.'/../partials/head.php'; ?>
<body class="bg-background min-h-screen font-body-md text-on-surface antialiased">
<?php require __DIR__.'/../partials/header.php'; ?>
<main class="portal-container space-y-space-lg py-8">
    <section class="bg-inverse-surface text-on-secondary rounded-2xl p-space-xl">
        <p class="text-label-md uppercase tracking-wide">Exógena · Personas jurídicas</p>
        <h1 class="text-headline-lg font-semibold mt-2">1007 · Ingresos Recibidos</h1>
        <p class="text-body-md mt-2">Preparación de ingresos por empresa, tercero y año gravable.</p>
    </section>
    <?php if ($notice!==''): ?><p role="status" class="bg-primary-container rounded-xl p-3"><?= e($notice) ?></p><?php endif; ?>
    <?php require __DIR__.'/company-selector.php'; ?>
    <?php require __DIR__.'/notice-1007.php'; ?>
    <?php require __DIR__.'/structure-1007.php'; ?>
    <?php if ($company): ?>
        <section class="bg-surface-container-lowest rounded-2xl border border-surface-container p-space-xl space-y-space-md">
            <h2 class="text-headline-md font-semibold taxpayer-name"><?= e($company['nombre']) ?> · <?= e($year) ?></h2>
            <p class="text-body-md"><?= count($records) ?> registros guardados.</p>
            <a class="inline-block rounded-xl bg-inverse-surface text-on-secondary px-4 py-2" href="<?= e(url($config,'exogena/1007/registro').'&contribuyente='.$company['id'].'&anio='.$year) ?>">Nuevo registro 1007</a>
            <?php foreach ($records as $row): ?>
                <article class="rounded-xl border border-surface-container p-4 space-y-space-sm taxpayer-name">
                    <h3 class="font-semibold"><?= e($row['datos']['razon_social']?:trim($row['datos']['primer_nombre'].' '.$row['datos']['primer_apellido'])) ?></h3>
                    <p class="text-body-md">Documento <?= e($row['datos']['identificacion']) ?> · Concepto <?= e($row['datos']['concepto']) ?></p>
                    <p class="text-body-md">Ingresos brutos $<?= e(number_format((int)$row['datos']['ingresos_brutos'],0,',','.')) ?> · Devoluciones, rebajas y descuentos $<?= e(number_format((int)$row['datos']['devoluciones'],0,',','.')) ?></p>
                    <a class="text-secondary underline" href="<?= e(url($config,'exogena/1007/registro').'&id='.$row['id']) ?>">Ver y editar los 11 campos</a>
                </article>
            <?php endforeach; ?>
        </section>
    <?php endif; ?>
    <a class="text-secondary underline" href="<?= e(url($config,'exogena')) ?>">Volver a Exógena</a>
</main>
<?php require __DIR__.'/../partials/footer.php'; ?>
