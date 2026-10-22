<?php $title='Activar acceso independiente';require __DIR__.'/../partials/head.php'; ?>
<body class="bg-background min-h-screen font-body-md text-on-surface antialiased">
<main class="portal-container py-8"><section class="bg-surface-container-lowest rounded-2xl border border-surface-container p-4 space-y-space-md">
<h1 class="text-headline-lg font-semibold">Activar acceso independiente</h1>
<p>Solo el Socio previamente autorizado puede activar su cuenta inicial. No se consultan funcionarios ni se copian sus contraseñas. Los contribuyentes y documentos se conservan.</p>
<?php foreach ($errors as $error): ?><p role="alert" class="bg-error-container text-on-error-container rounded-xl p-4"><?= e($error) ?></p><?php endforeach; ?>
<form method="post" action="<?= e(url($config,'activar')) ?>" class="space-y-space-md">
<?php require __DIR__.'/../partials/csrf.php';require __DIR__.'/../accesos/identity.php'; ?>
<p>Usa el usuario del Socio inicial autorizado y define una contraseña nueva para este aplicativo.</p>
<label class="block">Código de activación entregado al responsable (solo si tu sesión anterior venció)<input name="activation_key" type="password" maxlength="64" autocomplete="off" class="w-full bg-surface-container-low rounded-xl p-2"></label>
<details><summary class="text-secondary underline">? · ¿Qué código debo escribir?</summary><p>Usa el código privado entregado con esta actualización. Sirve una sola vez para activar la cuenta del Socio ya autorizado. Si tu sesión previa sigue vigente puedes dejarlo vacío. Como recuperación, el responsable del sitio puede consultar el valor «clave» del archivo privado storage/activacion-inicial.json. El archivo se elimina después de activar.</p></details>
<?php require __DIR__.'/../accesos/password.php'; ?>
<button class="rounded-xl bg-inverse-surface text-on-secondary px-4 py-2">Activar mi cuenta</button>
</form></section></main>
<?php require __DIR__.'/../partials/footer.php'; ?>
