<?php $title='Crear usuario';require __DIR__.'/../partials/head.php'; ?>
<body class="bg-background min-h-screen font-body-md text-on-surface antialiased">
<?php require __DIR__.'/../partials/header.php'; ?>
<main class="portal-container py-8 space-y-space-lg">
<section class="bg-surface-container-lowest rounded-2xl border border-surface-container p-4 space-y-space-md">
<h1 class="text-headline-lg font-semibold">Crear usuario</h1>
<p>Crea el acceso de un socio o tercero. Su ficha como contribuyente se conserva por separado.</p>
<?php if ($errors): ?><div role="alert" class="bg-error-container text-on-error-container rounded-xl p-4"><?php foreach ($errors as $error): ?><p><?= e($error) ?></p><?php endforeach; ?></div><?php endif; ?>
<form method="post" action="<?= e(url($config,'usuarios/crear')) ?>" class="space-y-space-md">
<?php require __DIR__.'/../partials/csrf.php';require __DIR__.'/identity.php'; ?>
<label class="block">Perfil *<select name="perfil" class="w-full bg-surface-container-low rounded-xl p-2"><option value="usuario" <?= input($_POST,'perfil')!=='socio'?'selected':'' ?>>Usuario · solo su contribuyente</option><option value="socio" <?= input($_POST,'perfil')==='socio'?'selected':'' ?>>Socio · acceso total</option></select></label>
<div data-user-taxpayer><label class="block">Su contribuyente *<select name="contribuyente_id" class="w-full bg-surface-container-low rounded-xl p-2"><option value="">Selecciona su ficha</option><?php foreach ($taxpayers as $taxpayer): ?><option value="<?= e($taxpayer['id']) ?>" <?= input($_POST,'contribuyente_id')===(string)$taxpayer['id']?'selected':'' ?>><?= e($taxpayer['nombre']) ?></option><?php endforeach; ?></select></label></div>
<input type="hidden" name="activo" value="1">
<?php require __DIR__.'/password.php'; ?>
<p>Comunica el usuario y la contraseña inicial al interesado por un medio privado. Deberá cambiarla al ingresar. Esta pantalla no envía correos.</p>
<button class="rounded-xl bg-inverse-surface text-on-secondary px-4 py-2">Crear usuario</button>
<a class="text-secondary underline" href="<?= e(url($config,'accesos')) ?>">Volver a usuarios y accesos</a>
</form></section></main>
<script src="<?= e(asset($config,'js/accesos.js').'?v='.filemtime(dirname(__DIR__,3).'/public/assets/js/accesos.js')) ?>" defer></script>
<?php require __DIR__.'/../partials/footer.php'; ?>
