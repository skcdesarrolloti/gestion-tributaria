<?php $title='Ingresos por casilla'; require __DIR__.'/../partials/head.php'; ?>
<body class="bg-background min-h-screen font-body-md text-on-surface antialiased">
<?php require __DIR__.'/../partials/header.php'; ?>
<main class="portal-container space-y-space-lg py-8">
    <section class="bg-inverse-surface text-on-secondary rounded-2xl p-space-xl">
        <p class="text-label-md uppercase tracking-wide">Paso 2 · Ingresos · <?= e($profile['anio']) ?></p>
        <h1 class="text-headline-lg font-semibold mt-2"><?= e($profile['nombre']) ?></h1>
        <p class="text-body-md mt-2">Formulario <?= e($profile['formulario']) ?> · Referencia <?= e($profile['version']) ?> · Régimen ordinario · <?= e($profile['residencia']) ?></p>
    </section>
    <section class="bg-primary-container rounded-2xl p-space-xl space-y-space-sm">
        <h2 class="text-headline-sm font-semibold">Depuración preliminar de los movimientos registrados</h2>
        <p class="text-body-md">No calcula impuesto, exenciones personales, pérdidas fiscales ni UGPP. Las referencias normativas no validan automáticamente las partidas registradas.</p>
        <p class="text-body-md"><?= $profile['formulario']==='210'?'Alcance: primera depuración de trabajo, servicios personales, capital y no laborales. Pensiones, dividendos y ECE quedan pendientes.':'Alcance: ingresos ordinarios, financieros, otros y costos/gastos. Dividendos de las casillas 49–56 y tratamientos especiales quedan pendientes; los totales de ingresos son parciales.' ?></p>
        <p class="text-body-md">Los valores son pesos fiscales sin redondeo de presentación DIAN. Los totales negativos requieren revisión; no son pérdidas fiscales aceptadas.</p>
    </section>
    <div class="flex items-center gap-space-md flex-wrap">
        <a class="rounded-xl bg-inverse-surface text-on-secondary px-4 py-2" href="<?= e(url($config,'ingresos/movimiento').'&id='.$profile['id']) ?>">Registrar movimiento</a>
        <a class="text-secondary underline" href="<?= e(url($config,'ingresos').'&contribuyente='.$profile['contribuyente_id']) ?>">Volver a los años</a>
    </div>
    <section class="space-y-space-md">
        <h2 class="text-headline-md font-semibold">Casillas y fichas de respaldo</h2>
        <p class="text-body-md text-secondary">Real: lo ocurrido. Proyectado adicional: lo que falta por ocurrir; no repitas allí el ingreso real. El total del escenario suma ambos.</p>
        <?php foreach ($concepts as $concept): ?>
            <?php $values=$totals[(int)$concept['casilla']]; ?>
            <article class="bg-surface-container-lowest rounded-2xl border border-surface-container p-4 space-y-space-sm">
                <h3 class="text-headline-sm font-semibold"><?= e($concept['casilla']) ?> · <?= e($concept['nombre']) ?></h3>
                <p class="text-body-md text-secondary"><?= e($concept['bloque']) ?><?= $concept['clase']==='calculado'?' · Calculado, no editable':'' ?></p>
                <div class="profile-grid text-body-md">
                    <p>Real: <strong>$<?= e(number_format($values['real'],0,',','.')) ?></strong></p>
                    <p>Proyectado adicional: <strong>$<?= e(number_format($values['proyectado'],0,',','.')) ?></strong></p>
                    <p>Total escenario: <strong>$<?= e(number_format($values['real']+$values['proyectado'],0,',','.')) ?></strong></p>
                </div>
                <?php if ($values['real']<0 || $values['real']+$values['proyectado']<0): ?><p role="status" class="text-body-md">Revisar: las partidas que se restan superan los ingresos registrados. Este resultado no acredita una pérdida fiscal.</p><?php endif; ?>
                <details>
                    <summary class="text-body-md underline">Explicación, artículos y soportes</summary>
                    <p class="text-body-md mt-2"><?= e($concept['explicacion']) ?></p>
                    <p class="text-body-md mt-2"><strong>Fundamento inicial:</strong> <a class="underline" href="https://normograma.dian.gov.co/dian/compilacion/docs/estatuto_tributario.htm" target="_blank" rel="noopener noreferrer"><?= e($concept['fundamento']) ?></a></p>
                    <p class="text-body-md mt-2"><strong>Ejemplo:</strong> <?= e($concept['ejemplo']) ?></p>
                    <p class="text-body-md mt-2"><strong>Soportes:</strong> <?= e($concept['soportes']) ?></p>
                    <?php if ($concept['formula']!==''): ?><p class="text-body-md mt-2"><strong>Operación de esta etapa:</strong> <?= e($concept['formula']) ?></p><?php endif; ?>
                    <p class="text-body-md mt-2"><a class="underline" href="<?= e('https://www.dian.gov.co/atencionciudadano/formulariosinstructivos/Formularios/2026/Formulario_'.$profile['formulario'].'_2026.pdf') ?>" target="_blank" rel="noopener noreferrer">Consultar instructivo DIAN</a> · Revisión inicial: 14/10/2026. Verificar norma particular y vigencia antes de presentar.</p>
                </details>
            </article>
        <?php endforeach; ?>
    </section>
    <?php require __DIR__.'/movements.php'; ?>
</main>
<?php require __DIR__.'/../partials/footer.php'; ?>
