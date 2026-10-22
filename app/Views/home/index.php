<?php $title = 'Inicio'; require __DIR__ . '/../partials/head.php'; ?>
<body class="bg-background min-h-screen font-body-md text-on-surface antialiased">
<?php require __DIR__ . '/../partials/header.php'; ?>
<main class="portal-container space-y-space-lg py-8">
    <section class="bg-inverse-surface text-on-secondary rounded-2xl p-space-xl">
        <p class="text-label-md uppercase tracking-wide"><?= e($config['name']) ?></p>
        <h1 class="text-headline-lg font-semibold mt-2">Hola, <?= e($user['nombre']) ?></h1>
        <p class="text-body-md mt-2">Bienvenido al portal. Aquí encontrarás las herramientas de tu equipo.</p>
    </section>
    <div class="scenario-settings-grid" aria-label="Módulos de Gestión Tributaria">
    <?php if (\App\Services\AccesoRoles::socio($user)): ?><section class="bg-surface-container-lowest rounded-2xl border border-surface-container p-4">
        <h2 class="text-headline-md font-semibold">Usuarios y accesos</h2><p class="text-secondary mt-2">Crea cuentas propias, asigna el contribuyente de cada tercero y administra los permisos.</p><a class="inline-block mt-4 rounded-xl bg-inverse-surface text-on-secondary px-4 py-2" href="<?= e(url($config,'accesos')) ?>">Administrar usuarios</a>
    </section><?php endif; ?>
    <section class="bg-surface-container-lowest rounded-2xl border border-surface-container p-4">
        <span aria-hidden="true" class="material-symbols-outlined bg-primary-container rounded-xl p-3">group</span>
        <h2 class="text-headline-md font-semibold mt-4">Paso 1 · Contribuyentes</h2>
        <p class="text-body-md text-secondary mt-2">Abre su perfil y completa manualmente los campos que el RUT no diligenció. Revisa y guarda cada pestaña.</p>
        <a class="inline-block mt-4 rounded-xl bg-inverse-surface text-on-secondary px-4 py-2" href="<?= e(url($config, 'contribuyentes')) ?>">Ir a contribuyentes</a>
    </section>
    <section class="bg-surface-container-lowest rounded-2xl border border-surface-container p-4">
        <span aria-hidden="true" class="material-symbols-outlined bg-primary-container rounded-xl p-3">calculate</span>
        <h2 class="text-headline-md font-semibold mt-4">Paso 2 · Beneficios tributarios</h2>
        <p class="text-body-md text-secondary mt-2">Consulta las 52 fichas por categoría con definición, artículos, límites y soportes antes de registrar tus valores.</p>
        <a class="inline-block mt-4 rounded-xl bg-inverse-surface text-on-secondary px-4 py-2" href="<?= e(url($config,'beneficios')) ?>">Consultar deducciones y normas</a>
    </section>
    <section class="bg-surface-container-lowest rounded-2xl border border-surface-container p-4">
        <span aria-hidden="true" class="material-symbols-outlined bg-primary-container rounded-xl p-3">menu_book</span>
        <h2 class="text-headline-md font-semibold mt-4">Paso 3 · Registro de información</h2>
        <p class="text-body-md text-secondary mt-2">Selecciona 2025 o 2026 y registra Ingresos o Costos y gastos por cédula, conservando sus movimientos y soportes.</p>
        <a class="inline-block mt-4 rounded-xl bg-inverse-surface text-on-secondary px-4 py-2" href="<?= e(url($config,'registro')) ?>">Registrar información del año</a>
    </section>
    <section class="bg-surface-container-lowest rounded-2xl border border-surface-container p-4">
        <h2 class="text-headline-md font-semibold">Paso 4 · Análisis y acciones</h2><p class="text-secondary mt-2">Revisa la proyección anual y abre la hoja de cálculos para comparar escenarios.</p><a class="inline-block mt-4 rounded-xl bg-inverse-surface text-on-secondary px-4 py-2" href="<?= e(url($config,'analisis')) ?>">Abrir análisis y acciones</a>
    </section>
    <section class="bg-surface-container-lowest rounded-2xl border border-surface-container p-4">
        <span aria-hidden="true" class="material-symbols-outlined bg-primary-container rounded-xl p-3">folder_open</span>
        <h2 class="text-headline-md font-semibold mt-4">Exógena · Módulo independiente</h2>
        <p class="text-body-md text-secondary mt-2">Abre los formatos de información exógena por empresa y año, con sus campos y referencias.</p>
        <a class="inline-block mt-4 rounded-xl bg-inverse-surface text-on-secondary px-4 py-2" href="<?= e(url($config,'exogena')) ?>">Abrir Exógena</a>
    </section>
    </div>
</main>
<?php require __DIR__ . '/../partials/footer.php'; ?>
