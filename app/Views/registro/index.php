<?php $title='Registro de información';require __DIR__.'/../partials/head.php'; ?>
<body class="bg-background min-h-screen font-body-md text-on-surface antialiased">
<?php require __DIR__.'/../partials/header.php'; ?>
<main class="portal-container py-8 space-y-space-lg">
<section class="bg-inverse-surface text-on-secondary rounded-2xl p-4"><h1 class="text-headline-lg font-semibold">3 · Registro de información</h1><p>Selecciona contribuyente y año. Registra ingresos, costos y gastos en sus pestañas, con sus soportes.</p></section>
<?php $contextRoute='registro';require __DIR__.'/context.php'; ?>
<?php if ($profile): ?>
<section class="bg-surface-container-lowest border border-surface-container rounded-xl p-4 space-y-space-md">
<h2 class="text-headline-md font-semibold"><?= e($profile['nombre'].' · '.$profile['anio']) ?></h2>
<?php require __DIR__.'/tabs.php'; ?>
<div class="scenario-settings-grid">
<?php foreach ($concepts as $concept): $values=$totals[(int)$concept['casilla']]; ?>
<article class="border border-surface-container rounded-xl p-4 space-y-space-sm">
<h3 class="font-semibold"><?= e($concept['casilla'].' · '.$concept['nombre']) ?></h3>
<p>Real: $<?= e(number_format($values['real'],0,',','.')) ?> · Proyectado adicional: $<?= e(number_format($values['proyectado'],0,',','.')) ?></p>
<details><summary class="text-secondary underline">? · Definición, alcance y soportes</summary><p><?= e($concept['explicacion']) ?></p><p><?= e($concept['fundamento']) ?></p><p><?= e($concept['soportes']) ?></p></details>
<?php if (\App\Services\IngresoData::allowed($concept,$profile)): ?><a class="inline-block rounded-xl bg-inverse-surface text-on-secondary px-4 py-2" href="<?= e(url($config,'ingresos/movimiento').'&id='.$profile['id'].'&concepto='.$concept['id'].'&origen=registro'.$contextQuery) ?>">Registrar valor</a><?php else: ?><p>Revisa el tratamiento de servicios personales en el perfil fiscal antes de registrar este concepto.</p><?php endif; ?>
</article>
<?php endforeach; ?></div>
<?php if (!$concepts): ?><p role="status"><?php if ($profile['formulario']==='210' && $state['cedula']!=='general'): ?>El detalle de movimientos de esta cédula aún está pendiente. No se asignan sus valores a otra cédula ni se habilitan costos automáticamente.<?php else: ?>No hay casillas de captura habilitadas para esta combinación de componente y renta. Revisa el apartado que corresponda a tu operación y su tratamiento fiscal; no registres el valor en otra categoría solo para guardarlo.<?php endif; ?></p><?php endif; ?>
<a class="text-secondary underline" href="<?= e(url($config,'ingresos/perfil').'&id='.$profile['id']) ?>">Revisar perfil fiscal guardado</a>
</section>
<details class="border border-surface-container rounded-xl p-4"><summary class="font-semibold">Movimientos de esta pestaña (<?= count($movements) ?>)</summary><?php $registration=$state;require __DIR__.'/../ingresos/movements.php'; ?></details>
<a class="text-secondary underline" href="<?= e(url($config,'analisis').$contextQuery) ?>">Continuar a Análisis y acciones</a>
<?php endif; ?>
</main><?php require __DIR__.'/../partials/footer.php'; ?>
