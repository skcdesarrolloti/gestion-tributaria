<?php $title='Usuarios y accesos'; require __DIR__.'/../partials/head.php'; ?>
<body class="bg-background min-h-screen font-body-md text-on-surface antialiased">
<?php require __DIR__.'/../partials/header.php'; ?>
<main class="portal-container space-y-space-lg py-8">
    <section class="bg-inverse-surface text-on-secondary rounded-2xl p-4 space-y-space-sm">
        <h1 class="text-headline-lg font-semibold">Usuarios y accesos</h1>
        <p>Socio: consulta todos los contribuyentes. Usuario: consulta y completa solo su ficha asignada.</p>
    </section>
    <section class="bg-surface-container-lowest border border-surface-container rounded-xl p-4 space-y-space-md">
        <p>Selecciona una cuenta institucional existente. Aquí asignas permisos de Gestión Tributaria; no creas cuentas ni cambias contraseñas del sistema central.</p>
        <form method="get" action="<?= e($config['base_path'].'/index.php') ?>">
            <input type="hidden" name="route" value="accesos">
            <label for="cuenta">Cuenta institucional activa</label>
            <select id="cuenta" name="funcionario" required class="w-full bg-surface-container-low rounded-xl p-2"><option value="">Selecciona una cuenta</option><?php foreach ($directory as $account): ?><option value="<?= e($account['_ID']) ?>" <?= (int)$account['_ID']===$selected?'selected':'' ?>><?= e($account['nombre'].' · '.$account['user_others_apss']) ?></option><?php endforeach; ?></select>
            <button class="rounded-xl bg-inverse-surface text-on-secondary px-4 py-2 mt-2">Abrir asignación</button>
        </form>
    </section>
    <?php if ($errors): ?><div role="alert" class="bg-error-container text-on-error-container rounded-xl p-3"><?php foreach ($errors as $error): ?><p><?= e($error) ?></p><?php endforeach; ?></div><?php endif; ?>
    <?php if (input($_GET,'guardado')==='1'): ?><p role="status" class="bg-primary-container rounded-xl p-3">Acceso guardado. Se comprobará al abrir cada página, incluso en una sesión ya iniciada.</p><?php endif; ?>
    <?php if ($target): ?><section class="bg-surface-container-lowest border border-surface-container rounded-xl p-4 space-y-space-md">
        <h2 class="text-headline-md font-semibold"><?= e($target['nombre']) ?></h2>
        <form method="post" action="<?= e(url($config,'accesos').'&funcionario='.$selected) ?>" class="space-y-space-md">
            <?php require __DIR__.'/../partials/csrf.php'; ?><input type="hidden" name="revision" value="<?= e($record['revision']) ?>">
            <label class="block">Perfil de acceso<select name="perfil" class="w-full bg-surface-container-low rounded-xl p-2"><?php foreach (['usuario'=>'Usuario · solo su contribuyente','socio'=>'Socio · consulta global y administración de accesos'] as $key=>$label): ?><option value="<?= e($key) ?>" <?= $record['perfil']===$key?'selected':'' ?>><?= e($label) ?></option><?php endforeach; ?></select></label>
            <label class="block">Su contribuyente<select name="contribuyente_id" class="w-full bg-surface-container-low rounded-xl p-2"><option value="">Sin asignación</option><?php foreach ($taxpayers as $taxpayer): ?><option value="<?= e($taxpayer['id']) ?>" <?= (int)$record['contribuyente_id']===(int)$taxpayer['id']?'selected':'' ?>><?= e($taxpayer['nombre'].' · '.$taxpayer['documento_tipo'].' '.$taxpayer['documento_numero']) ?></option><?php endforeach; ?></select></label>
            <p class="text-body-md text-secondary">Para un Usuario activo, asigna únicamente la persona o entidad cuya documentación está autorizado a completar. El Socio conserva la edición de las fichas que creó y de su ficha asignada; las demás son de consulta.</p>
            <label class="block">Acceso a Gestión Tributaria<select name="activo" class="w-full bg-surface-container-low rounded-xl p-2"><option value="1" <?= $record['activo']?'selected':'' ?>>Habilitado</option><option value="0" <?= !$record['activo']?'selected':'' ?>>Suspendido</option></select></label>
            <button type="submit" class="rounded-xl bg-inverse-surface text-on-secondary px-4 py-2">Guardar acceso</button>
        </form>
    </section><?php endif; ?>
    <details class="border border-surface-container rounded-xl p-4"><summary class="font-semibold">Asignaciones actuales (<?= count($assignments) ?>)</summary>
        <?php foreach ($directory as $account): if (!isset($assignments[$account['_ID']])) { continue; } $assignment=$assignments[$account['_ID']]; ?>
            <p class="mt-2"><?= e($account['nombre']) ?> · <?= e(ucfirst($assignment['perfil'])) ?> · <?= $assignment['activo']?'Habilitado':'Suspendido' ?> · <?= e($assignment['contribuyente']??'Sin ficha asignada') ?> · <a class="text-secondary underline" href="<?= e(url($config,'accesos').'&funcionario='.$account['_ID']) ?>">Revisar acceso</a></p>
        <?php endforeach; ?>
    </details>
</main>
<?php require __DIR__.'/../partials/footer.php'; ?>
