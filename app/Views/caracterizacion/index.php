<?php
$title='Perfil tributario'; $base=url($config,'contribuyentes/perfil').'&id='.$taxpayer['id'].'&anio='.$year;
require __DIR__.'/../partials/head.php';
?>
<body class="bg-background min-h-screen font-body-md text-on-surface antialiased">
<?php require __DIR__.'/../partials/header.php'; ?>
<main class="portal-container space-y-space-lg py-8">
    <section class="bg-inverse-surface text-on-secondary rounded-2xl p-space-xl space-y-space-sm">
        <h1 class="text-headline-lg font-semibold">Perfil tributario · <?= e($year) ?></h1>
        <p class="taxpayer-name"><?= e($taxpayer['nombre']) ?> · <?= e($taxpayer['documento_tipo'].' '.$taxpayer['documento_numero']) ?></p>
        <p>Completa un bloque a la vez. Puedes guardar borradores sin tener aún el RUT.</p>
    </section>
    <section class="bg-primary-container rounded-xl p-4 space-y-space-sm">
        <h2 class="text-headline-sm font-semibold">¿Ya tienes el RUT en PDF?</h2>
        <p>Sube el documento para leer sus campos y revisarlos antes de aplicarlos. Los datos existentes se conservan salvo que elijas reemplazarlos.</p>
        <a class="inline-block rounded-xl bg-inverse-surface text-on-secondary px-4 py-2" href="<?= e(url($config,'contribuyentes/rut').'&id='.$taxpayer['id'].'&anio='.$year) ?>">Subir RUT en PDF</a>
    </section>
    <div class="flex items-center gap-space-md flex-wrap">
        <a class="text-secondary underline" href="<?= e(url($config,'contribuyentes/formulario').'&id='.$taxpayer['id']) ?>">Editar identificación básica</a>
        <a class="text-secondary underline" href="<?= e(url($config,'ingresos').'&contribuyente='.$taxpayer['id']) ?>">Ingresos por año</a>
        <form method="get" action="<?= e($config['base_path'].'/index.php') ?>">
            <input type="hidden" name="route" value="contribuyentes/perfil"><input type="hidden" name="id" value="<?= e($taxpayer['id']) ?>">
            <label for="perfil-anio">Año de la información</label>
            <select id="perfil-anio" name="anio" class="rounded-xl bg-surface-container-low p-2"><?php foreach (['2025','2026'] as $option): ?><option <?= $option===$year?'selected':'' ?>><?= e($option) ?></option><?php endforeach; ?></select>
            <button class="rounded-xl bg-inverse-surface text-on-secondary px-4 py-2" type="submit">Abrir año</button>
        </form>
    </div>
    <?php if ($notice!==''): ?><p role="status" class="bg-primary-container rounded-xl p-3"><?= e($notice) ?></p><?php endif; ?>
    <?php if ($block===''): require __DIR__.'/overview.php'; else: ?>
        <a class="text-secondary underline" href="<?= e($base) ?>">← Ver bloques y pendientes</a>
        <?php require __DIR__.'/form.php'; ?>
    <?php endif; ?>
    <details class="bg-surface-container-lowest rounded-xl border border-surface-container p-4">
        <summary class="font-semibold">? · Alcance y fuentes</summary>
        <p class="mt-2">Es una ficha interna de planeación. No presenta ni actualiza el RUT de la DIAN. Las respuestas no activan beneficios ni modifican los perfiles y escenarios existentes. Los datos se revisan por año; no se copian automáticamente de otro año.</p>
        <p class="mt-2">Captura completa no equivale a revisión profesional. Hay que contrastar documentos, obligaciones, hechos y normas del año; el control muestra pendientes e incoherencias detectables, sin garantizar que cubra cualquier situación especial.</p>
        <p class="mt-2"><a class="underline text-secondary" href="<?= e(\App\Services\CaracterizacionSchema::SOURCE) ?>" target="_blank" rel="noopener noreferrer">DIAN · Secciones del RUT</a> · <a class="underline text-secondary" href="https://normograma.dian.gov.co/dian/compilacion/docs/resolucion_dian_0227_2025.htm" target="_blank" rel="noopener noreferrer">Resolución 227/2025 · Actividades</a> · <a class="underline text-secondary" href="https://www.dian.gov.co/impuestos/RUT/Paginas/Responsabilidades-y-Usuarios-Aduaneros.aspx" target="_blank" rel="noopener noreferrer">Responsabilidades y aduanas</a></p>
        <p>Referencia consultada: 21/10/2026. Las importaciones del RUT conservan el soporte privado; el resto de la planeación se completa por año.</p>
    </details>
</main>
<?php require __DIR__.'/../partials/footer.php'; ?>
