<?php $title='Análisis y acciones';require __DIR__.'/../partials/head.php'; ?>
<body class="bg-background min-h-screen font-body-md text-on-surface antialiased">
<?php require __DIR__.'/../partials/header.php'; ?>
<main class="portal-container py-8 space-y-space-lg">
<section class="bg-inverse-surface text-on-secondary rounded-2xl p-4"><h1 class="text-headline-lg font-semibold">4 · Análisis y acciones</h1><p>Revisa el año, compara escenarios y documenta las acciones antes del cierre.</p></section>
<?php $contextRoute='analisis';require __DIR__.'/context.php'; ?>
<?php if ($taxpayer): require __DIR__.'/preguntas.php'; endif; ?>
<?php if ($profile): ?><section class="bg-surface-container-lowest border border-surface-container rounded-xl p-4 space-y-space-md">
<h2 class="text-headline-md font-semibold"><?= e($profile['nombre'].' · '.$profile['anio']) ?></h2>
<div class="flex items-center gap-space-md flex-wrap">
<a class="rounded-xl bg-inverse-surface text-on-secondary px-4 py-2" href="<?= e(url($config,'proyeccion').'&id='.$profile['id']) ?>">Seguimiento y proyección anual</a>
<?php if ($profile['tipo']==='natural' && $profile['residencia']==='residente'): ?><a class="rounded-xl bg-inverse-surface text-on-secondary px-4 py-2" href="<?= e(url($config,'simulador').'&id='.$profile['id']) ?>">Hoja de cálculos y escenarios</a><?php else: ?><p>La hoja de cálculo de renta disponible corresponde a personas naturales residentes del régimen ordinario.</p><?php endif; ?>
<a class="text-secondary underline" href="<?= e(url($config,'registro').\App\Services\RegistroNavegacion::query($state,(int)$taxpayer['id'])) ?>">Volver al registro del año</a>
</div><p>La hoja de escenarios mantiene sus propios valores. No importa automáticamente los movimientos registrados: comprueba las cifras antes de comparar.</p>
</section><?php endif; ?>
<div class="flex items-center gap-space-md flex-wrap"><a class="text-secondary underline" href="<?= e(url($config,'beneficios').'&anio='.$state['anio']) ?>">Consultar beneficios y requisitos</a><a class="text-secondary underline" href="<?= e(url($config,'seguridad-social')) ?>">Seguridad social · revisión existente</a></div>
</main><?php require __DIR__.'/../partials/footer.php'; ?>
