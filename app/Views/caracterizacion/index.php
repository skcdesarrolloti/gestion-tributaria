<?php
$title=$component?'Datos para planeación':'Perfil del contribuyente'; $profileBase=url($config,'contribuyentes/perfil').'&id='.$taxpayer['id'].'&anio='.$year;
$base=url($config,$component?'planeacion/datos':'contribuyentes/perfil').'&id='.$taxpayer['id'].'&anio='.$year;
$formAction=$base.'&tab='.rawurlencode($tab); if (!isset($tabs[$tab])) { $formAction=$base.'&bloque='.rawurlencode($block); }
require __DIR__.'/../partials/head.php';
?>
<body class="bg-background min-h-screen font-body-md text-on-surface antialiased">
<?php require __DIR__.'/../partials/header.php'; ?>
<main class="portal-container space-y-space-md py-8">
    <a class="underline text-secondary" href="<?= e($component?$profileBase:url($config,'contribuyentes')) ?>"><?= $component?'← Volver al perfil':'← Mis contribuyentes' ?></a>
    <section class="bg-inverse-surface text-on-secondary rounded-2xl p-4 space-y-space-sm">
        <h1 class="text-headline-md font-semibold taxpayer-name"><?= e($taxpayer['nombre']) ?></h1>
        <p><?= e(\App\Services\ContribuyenteData::TIPOS[$taxpayer['tipo']].' · '.$taxpayer['documento_tipo'].' '.$taxpayer['documento_numero']) ?> · <?= $component?'Planeación':'Perfil' ?> <?= e($year) ?></p>
        <div class="flex items-center gap-space-md flex-wrap">
            <?php if (!$component): ?><a class="rounded-xl bg-primary-container text-on-surface px-4 py-2" href="<?= e($base.'&tab=documentos') ?>">Subir o actualizar RUT</a><?php endif; ?>
            <form method="get" action="<?= e($config['base_path'].'/index.php') ?>">
                <input type="hidden" name="route" value="<?= $component?'planeacion/datos':'contribuyentes/perfil' ?>"><input type="hidden" name="id" value="<?= e($taxpayer['id']) ?>"><input type="hidden" name="tab" value="<?= e($tab) ?>">
                <label for="perfil-anio">Año del perfil</label>
                <select id="perfil-anio" name="anio" class="rounded-xl bg-surface-container-low text-on-surface p-2"><?php foreach (['2025','2026'] as $option): ?><option <?= $option===$year?'selected':'' ?>><?= e($option) ?></option><?php endforeach; ?></select>
                <button type="submit" class="underline">Abrir</button>
            </form>
        </div>
    </section>
    <?php if ($notice!==''): ?><p role="status" class="bg-primary-container rounded-xl p-3"><?= e($notice) ?></p><?php endif; ?>
    <p id="tabs-ayuda" class="contribuyente-tab-hint text-body-md text-secondary">Desliza las pestañas para ver más secciones.</p>
    <nav class="contribuyente-tabs border-b border-surface-container" aria-label="Información del contribuyente" aria-describedby="tabs-ayuda">
        <?php foreach ($tabs as $key=>$definition): ?><a class="<?= $key===$tab?'bg-inverse-surface text-on-secondary':'bg-surface-container-low text-secondary' ?> rounded-xl px-3 py-2" href="<?= e($base.'&tab='.$key) ?>" <?= $key===$tab?'aria-current="page"':'' ?>><?= e($definition[0]) ?></a><?php endforeach; ?>
    </nav>
    <?php if ($tab==='documentos'): require __DIR__.'/documents.php'; else: require __DIR__.'/form.php'; endif; ?>
    <?php if (!$component): ?><details class="border border-surface-container rounded-xl p-4">
        <summary class="text-secondary underline">Datos que se diligencian en otros componentes</summary>
        <p class="mt-2">Las preguntas de ingresos, beneficios, aportes y obligaciones del año están separadas del perfil. Lo ya guardado se conserva; estas fichas no calculan ni transfieren importes al simulador.</p>
        <div class="flex flex-wrap gap-space-md mt-2"><?php foreach (\App\Services\PerfilAlcance::components() as $key=>$item): ?><a class="text-secondary underline" href="<?= e(url($config,'planeacion/datos').'&id='.$taxpayer['id'].'&anio='.$year.'&tab='.$key) ?>"><?= e($item[0]) ?></a><?php endforeach; ?></div>
    </details><?php endif; ?>
</main>
<script src="<?= e(asset($config,'js/caracterizacion.js').'?v='.filemtime(dirname(__DIR__,3).'/public/assets/js/caracterizacion.js')) ?>" defer></script>
<?php require __DIR__.'/../partials/footer.php'; ?>
