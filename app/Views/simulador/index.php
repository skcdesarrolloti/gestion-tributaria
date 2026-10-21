<?php $title='Hoja de planeación'; require __DIR__.'/../partials/head.php'; ?>
<body class="bg-background min-h-screen font-body-md text-on-surface antialiased">
<?php require __DIR__.'/../partials/header.php'; ?>
<main class="portal-container space-y-space-lg py-8">
    <section class="bg-inverse-surface text-on-secondary rounded-2xl p-space-xl space-y-space-sm">
        <h1 class="text-headline-lg font-semibold">Hoja de planeación de renta</h1>
        <p>Cambia las celdas, consulta la operación y compara versiones. Personas naturales residentes en régimen ordinario · 2025 y 2026.</p>
        <?php if ($profile): ?><p class="taxpayer-name"><?= e($profile['nombre']) ?> · Año <?= e($profile['anio']) ?></p><?php endif; ?>
    </section>
    <section class="bg-surface-container-lowest rounded-2xl border border-surface-container p-4 space-y-space-md">
        <form method="get" action="<?= e($config['base_path'].'/index.php') ?>">
            <input type="hidden" name="route" value="simulador">
            <label>Persona y perfil anual
                <select name="id" required class="mt-2 w-full px-space-md py-space-md rounded-xl bg-surface-container-low">
                    <option value="">Selecciona un perfil propio</option>
                    <?php foreach ($profiles as $p): ?><option value="<?= e($p['id']) ?>" <?= $profile && (int)$profile['id']===(int)$p['id']?'selected':'' ?>><?= e($p['nombre'].' · '.$p['anio']) ?></option><?php endforeach; ?>
                </select>
            </label>
            <button class="rounded-xl bg-inverse-surface text-on-secondary px-4 py-2 mt-2" type="submit">Abrir hoja</button>
        </form>
        <p class="text-body-md text-secondary">Si aún no aparece la persona, registra su contribuyente y crea su perfil en Ingresos por año. La hoja guarda escenarios independientes; no importa ni modifica los movimientos de ingresos existentes.</p>
    </section>
    <?php if ($profile): ?>
        <?php require __DIR__.'/scope.php'; ?>
        <?php if ($errors): ?><div role="alert"><?php foreach ($errors as $error): ?><p><?= e($error) ?></p><?php endforeach; ?></div><?php endif; ?>
        <?php require __DIR__.'/versions.php'; ?>
        <form id="renta-sheet" method="post" action="<?= e(url($config,'simulador').'&id='.$profile['id']) ?>" data-calculate="<?= e(url($config,'simulador/calcular').'&id='.$profile['id']) ?>" class="space-y-space-lg">
            <?php require __DIR__.'/../partials/csrf.php'; ?>
            <aside class="renta-live-summary bg-surface-container-lowest border border-surface-container rounded-xl p-4" aria-label="Resumen del cálculo actual">
                <?php foreach (['impuesto_neto'=>'Impuesto estimado','saldo_pagar'=>'Saldo por pagar','saldo_favor'=>'Saldo a favor'] as $key=>$label): ?>
                    <p class="text-label-sm"><?= e($label) ?><strong class="block text-body-md" data-result-key="<?= e($key) ?>">Pendiente</strong></p>
                <?php endforeach; ?>
                <a class="text-label-sm text-secondary underline" href="#renta-results">Ver operación y comparación</a>
            </aside>
            <?php require __DIR__.'/settings.php'; ?>
            <?php require __DIR__.'/monthly.php'; ?>
            <?php require __DIR__.'/basic.php'; ?>
            <?php require __DIR__.'/benefits.php'; ?>
            <?php require __DIR__.'/confirmations.php'; ?>
            <button class="rounded-xl bg-inverse-surface text-on-secondary px-4 py-2" type="submit">Guardar nueva versión</button>
            <p class="text-label-sm">Se guarda una copia con fecha, entradas y resultado. Las versiones anteriores se conservan, incluso cuando el resultado está pendiente.</p>
        </form>
        <?php require __DIR__.'/results.php'; ?>
        <script src="<?= e(asset($config,'js/renta-sheet.js')) ?>" defer></script>
    <?php endif; ?>
</main>
<?php require __DIR__.'/../partials/footer.php'; ?>
