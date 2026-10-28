<?php $title=$projection?'Reserva estimada de nómina':'Revisar retención laboral';require __DIR__.'/../partials/head.php'; ?>
<body class="bg-background min-h-screen font-body-md text-on-surface antialiased">
<?php require __DIR__.'/../partials/header.php'; ?>
<main class="portal-container py-8 space-y-space-lg">
<a class="text-secondary underline" href="<?= e(url($config,'registro').'&contribuyente='.$profile['contribuyente_id'].'&anio='.$profile['anio'].'&componente=ingresos&cedula=general&renta=trabajo#nomina') ?>">← Volver a ingresos laborales y acumulados</a>
<section class="bg-inverse-surface text-on-secondary rounded-2xl p-4"><h1 class="text-headline-lg font-semibold"><?= e($title) ?></h1><p><?= e($profile['nombre'].' · Año '.$profile['anio']) ?></p></section>
<?php if ($errors): ?><div class="bg-error-container rounded-xl p-4" role="alert"><?php foreach ($errors as $error): ?><p><?= e($error) ?></p><?php endforeach; ?></div><?php endif; ?>
<?php if (input($_GET,'guardado')==='1'): ?><p class="bg-primary-container rounded-xl p-4" role="status">Revisión guardada. Los importes originales de nómina se conservan.</p><?php endif; ?>
<?php if (!$projection): require __DIR__.'/selector-mes.php'; endif; ?>
<?php require __DIR__.($projection?'/reserva.php':'/mes.php'); ?>
</main><script src="<?= e(asset($config,'js/normas.js')) ?>" defer></script><script src="<?= e(asset($config,'js/retenciones-guia.js').'?v='.filemtime(dirname(__DIR__,3).'/public/assets/js/retenciones-guia.js')) ?>" defer></script><?php require __DIR__.'/../partials/footer.php'; ?>
