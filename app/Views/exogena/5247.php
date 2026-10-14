<?php $title='Exógena · 5247'; require __DIR__.'/../partials/head.php'; ?>
<body class="bg-background min-h-screen font-body-md text-on-surface antialiased">
<?php require __DIR__.'/../partials/header.php'; ?>
<main class="portal-container space-y-space-lg py-8">
    <section class="bg-inverse-surface text-on-secondary rounded-2xl p-space-xl">
        <p class="text-label-md uppercase tracking-wide">Exógena · Personas jurídicas</p>
        <h1 class="text-headline-lg font-semibold mt-2">5247</h1>
        <p class="text-body-md mt-2">Pagos o abonos en cuenta y retenciones de contratos.</p>
    </section>
    <?php if ($notice!==''): ?><p role="status" class="bg-primary-container rounded-xl p-3"><?= e($notice) ?></p><?php endif; ?>
    <section class="bg-surface-container-lowest rounded-2xl border border-surface-container p-space-xl space-y-space-md">
        <h2 class="text-headline-md font-semibold">Seleccionar empresa y año</h2>
        <?php if (!$companies): ?><p class="text-body-md">Primero registra una persona jurídica en Contribuyentes.</p><?php endif; ?>
        <form method="get" action="<?= e($config['base_path'].'/index.php') ?>" class="space-y-space-md">
            <input type="hidden" name="route" value="exogena/5247">
            <div class="profile-grid">
                <label class="text-body-md">Empresa informante *
                    <select name="contribuyente" required class="mt-2 w-full px-space-md py-space-md border-0 rounded-xl bg-surface-container-low">
                        <option value="">Selecciona una persona jurídica</option>
                        <?php foreach ($companies as $c): ?><option value="<?= e($c['id']) ?>" <?= $company && (int)$company['id']===(int)$c['id']?'selected':'' ?>><?= e($c['nombre']) ?> · <?= e($c['documento_numero']) ?></option><?php endforeach; ?>
                    </select>
                </label>
                <label class="text-body-md">Año gravable *
                    <input name="anio" value="<?= e($year) ?>" inputmode="numeric" pattern="20[0-9]{2}" maxlength="4" required class="mt-2 w-full px-space-md py-space-md border-0 rounded-xl bg-surface-container-low">
                    <span class="block text-secondary mt-2">Año de las operaciones. La estructura de captura sigue siendo la plantilla 2024.</span>
                </label>
            </div>
            <button class="rounded-xl bg-inverse-surface text-on-secondary px-4 py-2" type="submit">Consultar registros</button>
        </form>
        <a class="text-secondary underline" href="<?= e(url($config,'contribuyentes/formulario')) ?>">Registrar empresa</a>
    </section>
    <?php require __DIR__.'/notice.php'; ?>
    <?php require __DIR__.'/structure.php'; ?>
    <section class="bg-surface-container-lowest rounded-2xl border border-surface-container p-space-xl space-y-space-sm">
        <h2 class="text-headline-sm font-semibold">Preparación para recibir información de 2025</h2>
        <p class="text-body-md">La estructura queda vacía, sin importar registros. Cuando recibamos el archivo .rar, revisaremos su contenido y las columnas antes de cargar la información de la empresa correspondiente.</p>
    </section>
    <?php if ($company): ?>
        <section class="bg-surface-container-lowest rounded-2xl border border-surface-container p-space-xl space-y-space-md">
            <h2 class="text-headline-md font-semibold taxpayer-name"><?= e($company['nombre']) ?> · <?= e($year) ?></h2>
            <p class="text-body-md"><?= count($records) ?> registros guardados.</p>
            <a class="inline-block rounded-xl bg-inverse-surface text-on-secondary px-4 py-2" href="<?= e(url($config,'exogena/5247/registro').'&contribuyente='.$company['id'].'&anio='.$year) ?>">Nuevo registro 5247</a>
            <?php foreach ($records as $row): ?>
                <article class="rounded-xl border border-surface-container p-4 space-y-space-sm taxpayer-name">
                    <h3 class="font-semibold"><?= e($row['datos']['razon_social']?:trim($row['datos']['primer_nombre'].' '.$row['datos']['otros_nombres'].' '.$row['datos']['primer_apellido'].' '.$row['datos']['segundo_apellido'])) ?></h3>
                    <p class="text-body-md">Documento <?= e($row['datos']['identificacion']) ?> · Concepto <?= e($row['datos']['concepto']) ?> · Pago $<?= e(number_format((int)$row['datos']['pago'],0,',','.')) ?></p>
                    <p class="text-body-md text-secondary">Participante <?= e($row['datos']['participante_identificacion']) ?> · <?= e($row['estructura']) ?></p>
                    <a class="text-secondary underline" href="<?= e(url($config,'exogena/5247/registro').'&id='.$row['id']) ?>">Ver y editar los 22 campos</a>
                </article>
            <?php endforeach; ?>
        </section>
    <?php endif; ?>
    <a class="text-secondary underline" href="<?= e(url($config,'exogena')) ?>">Volver a Exógena</a>
</main>
<?php require __DIR__.'/../partials/footer.php'; ?>
