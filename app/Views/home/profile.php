<?php $title = 'Mi perfil'; require __DIR__ . '/../partials/head.php'; ?>
<body class="bg-background min-h-screen font-body-md text-on-surface antialiased">
<?php require __DIR__ . '/../partials/header.php'; ?>
<main class="portal-container py-8">
    <section class="bg-surface-container-lowest rounded-2xl border border-surface-container p-space-xl">
        <h1 class="text-headline-lg font-semibold">Mi perfil</h1>
        <p class="text-secondary mt-2">Cuenta independiente de Gestión Tributaria.</p>
        <p class="mt-2">Perfil en Gestión Tributaria: <strong><?= \App\Services\AccesoRoles::socio($user)?'Socio':(!empty($user['_acceso']['activo'])?'Usuario':'Pendiente de asignación o suspendido') ?></strong>.</p>
        <dl class="profile-grid mt-6">
            <?php foreach (['nombre' => 'Nombre', 'usuario' => 'Usuario', 'correo' => 'Correo'] as $key => $label): ?>
                <div><dt class="text-secondary text-label-sm"><?= e($label) ?></dt><dd class="font-semibold mt-1"><?= e($user[$key] ?: 'Sin asignar') ?></dd></div>
            <?php endforeach; ?>
        </dl>
        <?php if ($user['cambiar_clave']): ?><p role="status" class="bg-primary-container rounded-xl p-4 mt-2">Antes de continuar, cambia tu contraseña inicial por una personal.</p><?php endif; ?>
        <?php if (input($_GET,'guardado')==='1'): ?><p role="status" class="bg-primary-container rounded-xl p-4 mt-2">Contraseña actualizada. Las demás sesiones anteriores quedan invalidadas.</p><?php endif; ?>
        <?php foreach ($errors as $error): ?><p role="alert" class="bg-error-container text-on-error-container rounded-xl p-4 mt-2"><?= e($error) ?></p><?php endforeach; ?>
        <h2 class="text-headline-md font-semibold mt-6">Cambiar mi contraseña</h2>
        <form method="post" action="<?= e(url($config,'perfil')) ?>" class="space-y-space-md mt-2">
            <?php require __DIR__.'/../partials/csrf.php'; ?>
            <label class="block">Contraseña actual *<input name="current_password" type="password" maxlength="72" autocomplete="current-password" required class="w-full bg-surface-container-low rounded-xl p-2"></label>
            <?php require __DIR__.'/../accesos/password.php'; ?>
            <button class="rounded-xl bg-inverse-surface text-on-secondary px-4 py-2">Cambiar contraseña</button>
        </form>
    </section>
</main>
<?php require __DIR__ . '/../partials/footer.php'; ?>
