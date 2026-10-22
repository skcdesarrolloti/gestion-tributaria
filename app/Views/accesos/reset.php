<details class="border border-surface-container rounded-xl p-4"><summary class="font-semibold">Restablecer contraseña de <?= e($target['nombre']) ?></summary>
<form method="post" action="<?= e(url($config,'usuarios/restablecer')) ?>" class="space-y-space-md mt-2">
<?php require __DIR__.'/../partials/csrf.php'; ?><input type="hidden" name="usuario_id" value="<?= e($selected) ?>">
<label class="block">Tu contraseña actual como Socio *<input name="current_password" type="password" maxlength="72" autocomplete="current-password" required class="w-full bg-surface-container-low rounded-xl p-2"></label>
<?php require __DIR__.'/password.php'; ?>
<p>La persona deberá sustituir esta contraseña temporal al ingresar. Sus sesiones anteriores quedarán invalidadas.</p>
<button class="rounded-xl bg-inverse-surface text-on-secondary px-4 py-2">Restablecer contraseña</button>
</form></details>
