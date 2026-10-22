<?php
use App\Services\ContribuyenteData;
$title = 'Contribuyentes'; require __DIR__ . '/../partials/head.php';
?>
<body class="bg-background min-h-screen font-body-md text-on-surface antialiased">
<?php require __DIR__ . '/../partials/header.php'; ?>
<main class="portal-container space-y-space-lg py-8">
    <section class="bg-inverse-surface text-on-secondary rounded-2xl p-space-xl">
        <p class="text-label-md uppercase tracking-wide">Paso 1 · Identificación</p>
        <h1 class="text-headline-lg font-semibold mt-2">Contribuyentes</h1>
        <p class="text-body-md mt-2">Abre el perfil y completa los campos que el RUT no diligenció automáticamente. Revisa cada pestaña y guarda sus cambios.</p>
    </section>

    <?php if ($notice !== ''): ?>
        <p role="status" class="bg-primary-container rounded-xl p-3"><?= e($notice) ?></p>
    <?php endif; ?>
    <section class="bg-surface-container-lowest rounded-2xl border border-surface-container p-space-xl space-y-space-md">
        <h2 class="text-headline-md font-semibold"><?= \App\Services\AccesoRoles::socio($user)?'Todos los contribuyentes':'Mi contribuyente' ?> (<?= count($rows) ?>)</h2>
        <p class="text-body-md text-secondary"><?= \App\Services\AccesoRoles::socio($user)?'Tienes acceso total para consultar y completar la información de todos los contribuyentes.':'Aquí aparece únicamente tu contribuyente asignado. Puedes completar sus datos y documentación.' ?></p>
        <?php if (\App\Services\AccesoRoles::socio($user)): ?>
        <a class="inline-block rounded-xl bg-inverse-surface text-on-secondary px-4 py-2" href="<?= e(url($config, 'contribuyentes/formulario')) ?>">Registrar contribuyente</a>
        <?php endif; ?>
        <?php if (!$rows): ?>
            <p class="text-body-md text-secondary">Todavía no tienes contribuyentes disponibles. <?= \App\Services\AccesoRoles::socio($user)?'Comienza subiendo su RUT o registrando su identificación.':'Solicita a un Socio que te asigne tu ficha desde Usuarios y accesos.' ?></p>
        <?php endif; ?>
        <div class="space-y-space-md">
            <?php foreach ($rows as $row): ?>
                <article class="rounded-xl border border-surface-container p-4 space-y-space-sm">
                    <h3 class="text-headline-sm font-semibold taxpayer-name"><?= e($row['nombre']) ?></h3>
                    <p class="text-body-md text-secondary"><?= e(ContribuyenteData::TIPOS[$row['tipo']]) ?> · <?= e($row['documento_tipo']) ?> <?= e($row['documento_numero']) ?><?= $row['dv'] !== '' ? '-' . e($row['dv']) : '' ?></p>
                    <a class="inline-block rounded-xl bg-inverse-surface text-on-secondary px-4 py-2" href="<?= e(url($config,'contribuyentes/perfil').'&id='.$row['id']) ?>">Abrir perfil</a>
                </article>
            <?php endforeach; ?>
        </div>
    </section>
</main>
<?php require __DIR__ . '/../partials/footer.php'; ?>
