<?php $title='Ingresos · Perfil anual'; require __DIR__.'/../partials/head.php'; ?>
<body class="bg-background min-h-screen font-body-md text-on-surface antialiased">
<?php require __DIR__.'/../partials/header.php'; ?>
<main class="portal-container space-y-space-lg py-8">
    <section class="bg-inverse-surface text-on-secondary rounded-2xl p-space-xl">
        <p class="text-label-md uppercase tracking-wide">Paso 2 · Ingresos</p>
        <h1 class="text-headline-lg font-semibold mt-2"><?= e($taxpayer['nombre']) ?></h1>
        <p class="text-body-md mt-2">Primero confirma el perfil del año. Cada movimiento quedará relacionado con su casilla y soporte.</p>
    </section>
    <section class="bg-surface-container-lowest rounded-2xl border border-surface-container p-space-xl space-y-space-md">
        <h2 class="text-headline-md font-semibold">Años registrados</h2>
        <?php if (!$profiles): ?><p class="text-body-md text-secondary">Todavía no hay perfiles anuales.</p><?php endif; ?>
        <?php if ($profiles): ?><p class="text-body-md text-secondary">Estos perfiles están guardados. Abre sus ingresos o consulta sus selecciones con Ver o editar perfil.</p><?php endif; ?>
        <?php foreach ($profiles as $p): ?>
            <?php $savedFields=\App\Services\PerfilGuidance::fields($p['tipo']); ?>
            <article class="rounded-xl border border-surface-container p-4 space-y-space-sm taxpayer-name">
                <h3 class="text-headline-sm font-semibold"><?= e($p['anio']) ?> · Formulario <?= e($p['formulario']) ?> · Perfil guardado</h3>
                <?php foreach (['residencia','regimen','servicios'] as $savedField): ?>
                    <p class="text-body-md"><strong><?= e($savedFields[$savedField]['label']) ?>:</strong> <?= e($savedFields[$savedField]['options'][$p[$savedField]]['label']??$p[$savedField]) ?></p>
                <?php endforeach; ?>
                <p class="text-body-md"><strong>Evidencia guardada:</strong> <?= e($p['soporte_perfil']) ?></p>
                <div class="flex items-center gap-space-md flex-wrap">
                    <a class="rounded-xl bg-inverse-surface text-on-secondary px-4 py-2" href="<?= e(url($config,'proyeccion').'&id='.$p['id']) ?>">Proyección anual de <?= e($p['anio']) ?></a>
                    <a class="rounded-xl bg-inverse-surface text-on-secondary px-4 py-2" href="<?= e(url($config,'ingresos/detalle').'&id='.$p['id']) ?>">Abrir ingresos de <?= e($p['anio']) ?></a>
                    <a class="text-secondary underline" href="<?= e(url($config,'ingresos/perfil').'&id='.$p['id']) ?>">Ver o editar perfil</a>
                </div>
            </article>
        <?php endforeach; ?>
    </section>
    <details class="bg-surface-container-lowest rounded-2xl border border-surface-container p-space-xl space-y-space-md" <?= !$profiles||$errors?'open':'' ?>>
        <summary class="text-headline-md font-semibold"><?= $profiles?'Crear otro perfil anual':'Crear perfil anual' ?></summary>
        <p class="text-body-md text-secondary">Primera etapa de régimen ordinario para 2025 y 2026. SIMPLE y otros tratamientos requieren su propio módulo. Podrás corregirlo mientras no tenga movimientos; los datos guardados se consultan arriba.</p>
        <p class="text-body-md text-secondary">No contribuyente y no declarante son condiciones distintas del régimen. Se revisarán con las obligaciones del RUT; estas opciones no cubren todos los tratamientos especiales.</p>
        <?php if ($taxpayer['tipo']==='sucesion'): ?><p class="text-body-md text-secondary">Para sucesiones, indica la residencia del causante al fallecer. Las fechas y el detalle de representación se desarrollarán en su etapa específica.</p><?php endif; ?>
        <?php require __DIR__.'/errors.php'; ?>
        <?php
        $profileAction=url($config,'ingresos').'&contribuyente='.$taxpayer['id'];
        if ($registration) { $profileAction.='&origen=registro'.\App\Services\RegistroNavegacion::query($registration,(int)$taxpayer['id']); }
        $profileButton='Crear perfil y abrir ingresos';
        require __DIR__.'/profile-form.php';
        ?>
        <a class="text-secondary underline" href="<?= e(url($config,'contribuyentes')) ?>">Volver a contribuyentes</a>
    </details>
</main>
<script src="<?= e(asset($config,'js/ingresos-guidance.js')) ?>" defer></script>
<?php require __DIR__.'/../partials/footer.php'; ?>
