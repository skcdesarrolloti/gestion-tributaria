<?php $title = 'Primer acceso'; require __DIR__ . '/../partials/head.php'; ?>
<body class="bg-background min-h-screen flex items-center justify-center p-4 font-body-md text-on-surface antialiased">
<main class="w-full max-w-md bg-surface-container-lowest p-space-xl rounded-2xl shadow-xl border border-surface-container space-y-space-md">
    <div class="flex items-center gap-space-sm">
        <span aria-hidden="true" class="material-symbols-outlined bg-primary-container p-2 rounded-xl">domain</span>
        <div><strong class="block">SuCasa Inmobiliaria</strong><span class="text-secondary text-label-sm"><?= e($config['name']) ?></span></div>
    </div>
    <h1 class="text-headline-lg font-bold">Configura tu primer acceso</h1>
    <p class="text-body-md text-secondary">Crea la cuenta administradora para empezar a trabajar.</p>
    <?php if ($error !== ''): ?>
        <p role="alert" class="p-3 rounded-xl bg-error-container text-on-error-container"><?= e($error) ?></p>
    <?php endif; ?>
    <form method="post" action="<?= e(url($config, 'instalacion')) ?>" class="space-y-space-md">
        <?php require __DIR__ . '/../partials/csrf.php'; ?>
        <?php foreach ([
            'setup_key' => ['Clave de instalación', 'password', 'off', 128],
            'name' => ['Nombre completo', 'text', 'name', 150],
            'username' => ['Usuario', 'text', 'username', 100],
            'password' => ['Contraseña nueva', 'password', 'new-password', 72],
            'confirmation' => ['Repetir contraseña', 'password', 'new-password', 72],
        ] as $field => [$label, $type, $autocomplete, $length]): ?>
            <div>
                <label class="block text-label-sm text-secondary mb-1" for="<?= e($field) ?>"><?= e($label) ?></label>
                <input class="w-full px-3 py-2.5 bg-surface-container-low rounded-xl border border-transparent focus:border-outline-variant"
                    id="<?= e($field) ?>" name="<?= e($field) ?>" type="<?= e($type) ?>"
                    autocomplete="<?= e($autocomplete) ?>" maxlength="<?= e($length) ?>" required>
            </div>
        <?php endforeach; ?>
        <button type="submit" class="w-full py-3 rounded-xl bg-inverse-surface hover:bg-secondary text-on-secondary font-semibold">Crear administrador</button>
    </form>
</main>
</body>
</html>
