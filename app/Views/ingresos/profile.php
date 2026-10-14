<?php $title='Ver o editar perfil anual'; require __DIR__.'/../partials/head.php'; ?>
<body class="bg-background min-h-screen font-body-md text-on-surface antialiased">
<?php require __DIR__.'/../partials/header.php'; ?>
<main class="portal-container space-y-space-lg py-8">
    <section class="bg-inverse-surface text-on-secondary rounded-2xl p-space-xl">
        <h1 class="text-headline-lg font-semibold">Ver o editar perfil anual</h1>
        <p class="text-body-md mt-2"><?= e($profile['nombre']) ?> · Año guardado: <?= e($profile['anio']) ?></p>
    </section>
    <section class="bg-surface-container-lowest rounded-2xl border border-surface-container p-space-xl space-y-space-md">
        <p class="text-body-md text-secondary">Los campos están cargados con los datos guardados. Cambiarlos en pantalla no los modifica hasta pulsar Guardar cambios.</p>
        <p class="text-body-md text-secondary"><?= $hasMovements?'Este perfil ya tiene movimientos: se permite actualizar la evidencia. El servidor bloqueará cambios de año, residencia, régimen o servicios que alteren su clasificación.':'Este perfil no tiene movimientos: puedes corregir el año y las selecciones; se recalculará el formulario aplicable.' ?></p>
        <?php require __DIR__.'/errors.php'; ?>
        <?php
        $profileAction=url($config,'ingresos/perfil').'&id='.$profile['id'];
        $profileButton='Guardar cambios del perfil';
        require __DIR__.'/profile-form.php';
        ?>
        <a class="text-secondary underline" href="<?= e(url($config,'ingresos').'&contribuyente='.$profile['contribuyente_id']) ?>">Volver a los perfiles guardados</a>
    </section>
</main>
<script src="<?= e(asset($config,'js/ingresos-guidance.js')) ?>" defer></script>
<?php require __DIR__.'/../partials/footer.php'; ?>
