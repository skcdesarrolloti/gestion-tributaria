<?php
$title='Perfil del contribuyente'; $base=url($config,'contribuyentes/perfil').'&id='.$taxpayer['id'].'&anio='.$year;
$formAction=$base.'&tab='.rawurlencode($tab); if (!isset($tabs[$tab])) { $formAction=$base.'&bloque='.rawurlencode($block); }
require __DIR__.'/../partials/head.php';
?>
<body class="bg-background min-h-screen font-body-md text-on-surface antialiased">
<?php require __DIR__.'/../partials/header.php'; ?>
<main class="portal-container space-y-space-md py-8">
    <a class="underline text-secondary" href="<?= e(url($config,'contribuyentes')) ?>">← Mis contribuyentes</a>
    <section class="bg-inverse-surface text-on-secondary rounded-2xl p-4 space-y-space-sm">
        <h1 class="text-headline-md font-semibold taxpayer-name"><?= e($taxpayer['nombre']) ?></h1>
        <p><?= e(\App\Services\ContribuyenteData::TIPOS[$taxpayer['tipo']].' · '.$taxpayer['documento_tipo'].' '.$taxpayer['documento_numero']) ?> · Perfil <?= e($year) ?></p>
        <div class="flex items-center gap-space-md flex-wrap">
            <a class="rounded-xl bg-primary-container text-on-surface px-4 py-2" href="<?= e($base.'&tab=documentos') ?>">Subir o actualizar RUT</a>
            <form method="get" action="<?= e($config['base_path'].'/index.php') ?>">
                <input type="hidden" name="route" value="contribuyentes/perfil"><input type="hidden" name="id" value="<?= e($taxpayer['id']) ?>"><input type="hidden" name="tab" value="<?= e($tab) ?>">
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
</main>
<script src="<?= e(asset($config,'js/caracterizacion.js').'?v='.filemtime(dirname(__DIR__,3).'/public/assets/js/caracterizacion.js')) ?>" defer></script>
<?php require __DIR__.'/../partials/footer.php'; ?>
