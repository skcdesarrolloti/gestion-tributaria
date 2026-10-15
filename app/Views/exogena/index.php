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
        <?php if ($company): ?><p class="text-body-md taxpayer-name"><strong>Cliente seleccionado: <?= e($company['nombre']) ?>.</strong> El conteo de registros no acredita que un formato esté completo o validado.</p><?php endif; ?>
        <details><summary class="text-body-md underline">Sobre la futura carga mensual</summary><p class="text-body-md mt-2">La carga mensual y su conciliación con los acumulados anuales se desarrollarán después de recibir los archivos. Los saldos de cuentas representan un corte y no deben sumarse entre meses.</p></details>
    </section>
    <section class="bg-surface-container-lowest rounded-2xl border border-surface-container p-4 space-y-space-md">
        <h2 class="text-headline-md font-semibold">Formatos disponibles · 2025</h2>
        <table class="exogena-format-table text-body-md">
            <caption class="text-secondary">14 formatos organizados por grupo. Selecciona un cliente para consultar sus registros.</caption>
            <thead class="bg-surface-container-low text-secondary"><tr><th scope="col">Código</th><th scope="col">Formato</th><th scope="col">Grupo</th><th scope="col">Registros</th><th scope="col">Acceso</th></tr></thead>
            <tbody>
            <?php foreach (\App\Services\ExogenaFormats::grouped() as $group): ?>
            <?php foreach ($group['formats'] as $code=>$format): ?>
                <tr class="border-b border-surface-container">
                    <th scope="row"><span class="inline-block bg-primary-container rounded-xl p-2 font-semibold"><?= e($code) ?></span></th>
                    <td class="font-semibold"><?= e($format['name']) ?></td>
                    <td data-label="Grupo" class="text-secondary"><?= e($group['name']) ?></td>
                    <td data-label="Registros"><?php if ($company): ?><?= (int)($counts[$code]??0)>0?e($counts[$code]).' en preparación':'Sin información' ?><?php else: ?>Selecciona cliente<?php endif; ?></td>
                    <td><a class="inline-block rounded-xl bg-inverse-surface text-on-secondary px-4 py-2" aria-label="Abrir formato <?= e($code) ?>" href="<?= e(url($config,$format['route']).($company?'&contribuyente='.$company['id'].'&anio='.$year:'')) ?>">Abrir</a></td>
                </tr>
            <?php endforeach; ?>
            <?php endforeach; ?>
            </tbody>
        </table>
    </section>
</main>
<?php require __DIR__.'/../partials/footer.php'; ?>
