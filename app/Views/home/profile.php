<?php $title = 'Mi perfil'; require __DIR__ . '/../partials/head.php'; ?>
<body class="bg-background min-h-screen font-body-md text-on-surface antialiased">
<?php require __DIR__ . '/../partials/header.php'; ?>
<main class="portal-container py-8">
    <section class="bg-surface-container-lowest rounded-2xl border border-surface-container p-space-xl">
        <h1 class="text-headline-lg font-semibold">Mi perfil</h1>
        <p class="text-secondary mt-2">Si necesitas actualizar tus datos, contacta al administrador.</p>
        <dl class="profile-grid mt-6">
            <?php foreach (['nombre' => 'Nombre', 'user_others_apss' => 'Usuario', 'rol' => 'Rol', 'id_empleado' => 'Código de empleado', 'id_cargo' => 'Código de cargo'] as $key => $label): ?>
                <div><dt class="text-secondary text-label-sm"><?= e($label) ?></dt><dd class="font-semibold mt-1"><?= e($user[$key] ?: 'Sin asignar') ?></dd></div>
            <?php endforeach; ?>
        </dl>
    </section>
</main>
<?php require __DIR__ . '/../partials/footer.php'; ?>
