<?php $title='Extractos bancarios';require __DIR__.'/../partials/head.php'; ?>
<body class="bg-background min-h-screen font-body-md text-on-surface antialiased">
<?php require __DIR__.'/../partials/header.php'; ?>
<main class="portal-container py-8 space-y-space-lg">
<section class="bg-inverse-surface text-on-secondary rounded-2xl p-4">
<h1 class="text-headline-lg font-semibold">Extractos bancarios por mes</h1>
<p>Un soporte para consultar pensión recibida, rendimientos, 4×1000 y los demás movimientos de la cuenta.</p>
</section>
<form method="get" class="scenario-settings-grid">
<input type="hidden" name="route" value="extractos">
<label>Contribuyente <select name="contribuyente" required class="w-full rounded-xl bg-surface-container-low p-3">
<option value="">Selecciona una persona o entidad</option>
<?php foreach ($rows as $r): ?><option value="<?= (int)$r['id'] ?>" <?= $taxpayer && (int)$taxpayer['id']===(int)$r['id']?'selected':'' ?>><?= e($r['nombre']) ?></option><?php endforeach; ?>
</select></label>
<label>Año del extracto <select name="anio" class="w-full rounded-xl bg-surface-container-low p-3"><?php foreach (['2025','2026'] as $y): ?><option <?= $year===$y?'selected':'' ?>><?= e($y) ?></option><?php endforeach; ?></select></label>
<button class="rounded-xl bg-inverse-surface text-on-secondary px-4 py-2">Abrir año</button>
</form>
<?php if ($errors): ?><div role="alert" class="bg-error-container text-on-error-container rounded-xl p-4"><?php foreach ($errors as $error): ?><p><?= e($error) ?></p><?php endforeach; ?></div><?php endif; ?>
<?php if ($taxpayer): $extractQuery='&contribuyente='.(int)$taxpayer['id'].'&anio='.$year; ?>
<nav class="contribuyente-tabs" aria-label="Volver a registro">
<a class="text-secondary underline" href="<?= e(url($config,'registro').$extractQuery.'&componente=ingresos&cedula=pensiones') ?>">Ingresos · Pensiones</a>
<a class="text-secondary underline" href="<?= e(url($config,'registro').$extractQuery.'&componente=ingresos&cedula=general&renta=capital') ?>">Ingresos · Capital</a>
<a class="text-secondary underline" href="<?= e(url($config,'registro').$extractQuery.'&componente=costos&cedula=general&renta=capital') ?>">Costos y gastos</a>
</nav>
<?php if (input($_GET,'guardado')==='1'): ?><p role="status" class="bg-primary-container rounded-xl p-4">PDF conservado. Revisa abajo el estado de lectura, los totales bancarios y los movimientos. La clasificación fiscal no se ha confirmado.</p><?php endif; ?>
<?php require __DIR__.'/upload.php';require __DIR__.'/months.php'; ?>
<?php if ($selected): require __DIR__.'/detail.php'; endif; ?>
<?php else: ?><p>Selecciona un contribuyente para subir y consultar únicamente sus extractos.</p><?php endif; ?>
</main><script src="<?= e(asset($config,'js/pdf-carga.js').'?v='.filemtime(dirname(__DIR__,3).'/public/assets/js/pdf-carga.js')) ?>" defer></script><script src="<?= e(asset($config,'js/extractos.js').'?v='.filemtime(dirname(__DIR__,3).'/public/assets/js/extractos.js')) ?>" defer></script><?php require __DIR__.'/../partials/footer.php'; ?>
