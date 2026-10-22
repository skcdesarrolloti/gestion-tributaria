<?php
use App\Services\ContribuyenteData;
$title = $id === null ? 'Registrar contribuyente' : 'Editar contribuyente';
$action = url($config, 'contribuyentes/formulario') . ($id === null ? '' : '&id=' . $id);
require __DIR__ . '/../partials/head.php';
?>
<body class="bg-background min-h-screen font-body-md text-on-surface antialiased">
<?php require __DIR__ . '/../partials/header.php'; ?>
<main class="portal-container space-y-space-lg py-8">
    <section class="bg-inverse-surface text-on-secondary rounded-2xl p-space-xl">
        <p class="text-label-md uppercase tracking-wide">Paso 1 · Identificación</p>
        <h1 class="text-headline-lg font-semibold mt-2"><?= e($title) ?></h1>
        <p class="text-body-md mt-2"><?= $id===null?'Empieza subiendo el RUT para rellenar tus datos. Si no lo tienes, puedes registrar manualmente.':'Actualiza los datos básicos de identificación del contribuyente.' ?></p>
    </section>
    <?php if ($id===null): require __DIR__.'/rut-registro.php'; else: ?><a class="underline text-secondary" href="<?= e(url($config,'contribuyentes/perfil').'&id='.$id) ?>">← Volver a la ficha del contribuyente</a><?php endif; ?>

    <section id="datos-identificacion" class="bg-surface-container-lowest rounded-2xl border border-surface-container p-space-xl">
        <?php if ($errors): ?>
            <div role="alert" class="p-3 mb-4 rounded-xl bg-error-container text-on-error-container">
                <p class="font-semibold">Revisa los siguientes datos:</p>
                <ul class="list-disc pl-4"><?php foreach ($errors as $error): ?><li><?= e($error) ?></li><?php endforeach; ?></ul>
                <a class="underline" href="<?= e(url($config,'contribuyentes')) ?>">Ver mis contribuyentes</a>
            </div>
        <?php endif; ?>
        <form method="post" action="<?= e($action) ?>" class="space-y-space-lg">
            <?php require __DIR__ . '/../partials/csrf.php'; ?>
            <?php if ($rutDraft): ?><input type="hidden" name="rut_registro_token" value="<?= e($rutDraft['token']) ?>"><?php endif; ?>
            <?php $identity=$record; $identityPrefix=''; $identitySuffix=''; require __DIR__.'/identity-fields.php'; ?>


            <p class="text-body-md text-secondary"><?= $rutDraft?'Al guardar se crea el contribuyente, se aplican los datos reconocidos de todas las hojas del RUT y se conserva el PDF como soporte privado.':'* Campos obligatorios de identificación. Puedes registrar manualmente si no tienes el RUT.' ?></p>
            <div class="flex items-center gap-space-md flex-wrap">
                <button class="rounded-xl bg-inverse-surface text-on-secondary px-4 py-2" type="submit">Guardar contribuyente</button>
                <a class="text-secondary underline" href="<?= e(url($config, 'contribuyentes')) ?>">Volver al listado</a>
            </div>
        </form>
    </section>
</main>
<script src="<?= e(asset($config,'js/caracterizacion.js').'?v='.filemtime(dirname(__DIR__,3).'/public/assets/js/caracterizacion.js')) ?>" defer></script>
<?php require __DIR__ . '/../partials/footer.php'; ?>
