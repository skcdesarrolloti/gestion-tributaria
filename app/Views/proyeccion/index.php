<?php $title='Proyección anual de renta'; require __DIR__.'/../partials/head.php'; ?>
<body class="bg-background min-h-screen font-body-md text-on-surface antialiased">
<?php require __DIR__.'/../partials/header.php'; ?>
<main class="portal-container space-y-space-lg py-8">
    <section class="bg-inverse-surface text-on-secondary rounded-2xl p-space-xl">
        <p class="text-label-md uppercase tracking-wide">Seguimiento del año gravable <?= e($profile['anio']) ?></p>
        <h1 class="text-headline-lg font-semibold mt-2">Proyección anual de renta</h1>
        <p class="text-body-md mt-2"><?= e($profile['nombre']) ?> · Formulario <?= e($profile['formulario']) ?> · Consulta <?= e($today) ?></p>
        <p class="text-body-md mt-2">Revisa lo ocurrido, actualiza lo que esperas recibir y prepara las decisiones antes del cierre del año.</p>
    </section>
    <div class="flex items-center gap-space-md flex-wrap">
        <a class="rounded-xl bg-inverse-surface text-on-secondary px-4 py-2" href="<?= e(url($config,'ingresos/movimiento').'&id='.$profile['id']) ?>">Registrar ingreso o partida</a>
        <a class="text-secondary underline" href="<?= e(url($config,'ingresos/detalle').'&id='.$profile['id']) ?>">Ver casillas y editar registros</a>
        <a class="text-secondary underline" href="<?= e(url($config,'ingresos/perfil').'&id='.$profile['id']) ?>">Consultar perfil fiscal</a>
    </div>
    <section class="bg-primary-container rounded-2xl p-space-xl space-y-space-sm">
        <h2 class="text-headline-sm font-semibold">Primera estructura de seguimiento</h2>
        <p class="text-body-md">Este tablero reúne los movimientos guardados. Todavía no calcula el impuesto ni el ahorro: faltan bloques de ingresos y la revisión de beneficios, tarifas y pagos.</p>
        <p class="text-body-md">Cobertura actual: <?= $profile['formulario']==='210'?'trabajo, servicios personales, capital y no laborales; faltan pensiones, dividendos, ECE y ganancias ocasionales.':'ingresos ordinarios, financieros, otros y costos/gastos; faltan dividendos y tratamientos especiales.' ?></p>
        <p class="text-body-md">Un mes sin registros significa información pendiente. Los valores registrados tampoco confirman que el mes esté completo.</p>
        <?php if ($summary['proyecciones_por_revisar']>0): ?><p role="status" class="text-body-md"><strong>Revisar <?= e($summary['proyecciones_por_revisar']) ?> proyecciones con fecha pasada:</strong> actualízalas si ya ocurrieron o cambió lo esperado. Edita el registro existente para evitar duplicarlo.</p><?php endif; ?>
    </section>
    <?php require __DIR__.'/totals.php'; ?>
    <?php require __DIR__.'/months.php'; ?>
    <?php require __DIR__.'/steps.php'; ?>
</main>
<?php require __DIR__.'/../partials/footer.php'; ?>
