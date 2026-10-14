<?php $title='Ingresos · Perfil anual'; require __DIR__.'/../partials/head.php'; ?>
<body class="bg-background min-h-screen font-body-md text-on-surface antialiased">
<?php require __DIR__.'/../partials/header.php'; ?>
<main class="portal-container space-y-space-lg py-8">
    <section class="bg-inverse-surface text-on-secondary rounded-2xl p-space-xl">
        <p class="text-label-md uppercase tracking-wide">Paso 2 · Ingresos</p>
        <h1 class="text-headline-lg font-semibold mt-2"><?= e($taxpayer['nombre']) ?></h1>
        <p class="text-body-md mt-2">Primero confirma el perfil del año. Cada movimiento quedará relacionado con su casilla y soporte.</p>
    </section>
    <section class="bg-surface-container-lowest rounded-2xl border border-surface-container p-space-xl space-y-space-md">
        <h2 class="text-headline-md font-semibold">Años registrados</h2>
        <?php if (!$profiles): ?><p class="text-body-md text-secondary">Todavía no hay perfiles anuales.</p><?php endif; ?>
        <?php foreach ($profiles as $p): ?>
            <a class="inline-block rounded-xl bg-inverse-surface text-on-secondary px-4 py-2" href="<?= e(url($config,'ingresos/detalle').'&id='.$p['id']) ?>"><?= e($p['anio']) ?> · Formulario <?= e($p['formulario']) ?></a>
        <?php endforeach; ?>
    </section>
    <section class="bg-surface-container-lowest rounded-2xl border border-surface-container p-space-xl space-y-space-md">
        <h2 class="text-headline-md font-semibold">Crear perfil anual</h2>
        <p class="text-body-md text-secondary">Primera etapa de régimen ordinario para 2025 y 2026. SIMPLE y otros tratamientos requieren su propio módulo. El perfil queda fijado al crearlo: comprueba los datos antes de guardar.</p>
        <p class="text-body-md text-secondary">No contribuyente y no declarante son condiciones distintas del régimen. Se revisarán con las obligaciones del RUT; estas opciones no cubren todos los tratamientos especiales.</p>
        <?php if ($taxpayer['tipo']==='sucesion'): ?><p class="text-body-md text-secondary">Para sucesiones, indica la residencia del causante al fallecer. Las fechas y el detalle de representación se desarrollarán en su etapa específica.</p><?php endif; ?>
        <?php require __DIR__.'/errors.php'; ?>
        <form method="post" action="<?= e(url($config,'ingresos').'&contribuyente='.$taxpayer['id']) ?>" class="space-y-space-md">
            <?php require __DIR__.'/../partials/csrf.php'; ?>
            <div class="profile-grid">
                <?php foreach (\App\Services\PerfilGuidance::fields($taxpayer['tipo']) as $field=>$definition): ?>
                <?php $label=$definition['label']; $options=$definition['options']; ?>
                <div>
                    <label class="block text-body-md mb-1" for="<?= e($field) ?>"><?= e($label) ?> *</label>
                    <select class="w-full px-3 py-2.5 bg-surface-container-low rounded-xl border border-transparent" id="<?= e($field) ?>" name="<?= e($field) ?>" aria-describedby="<?= e($field.'-help') ?>" data-guidance="<?= e(json_encode($options,JSON_UNESCAPED_UNICODE|JSON_THROW_ON_ERROR)) ?>" required>
                        <option value="">Selecciona una opción</option>
                        <?php foreach ($options as $key=>$option): ?><option value="<?= e($key) ?>" <?= $record[$field]===$key?'selected':'' ?>><?= e($option['label']) ?></option><?php endforeach; ?>
                    </select>
                    <?php require __DIR__.'/option-help.php'; ?>
                </div>
                <?php endforeach; ?>
            </div>
            <label class="block text-body-md" for="soporte_perfil">Evidencia del perfil fiscal *</label>
            <textarea class="w-full px-3 py-2.5 bg-surface-container-low rounded-xl" id="soporte_perfil" name="soporte_perfil" maxlength="500" rows="3" required><?= e($record['soporte_perfil']) ?></textarea>
            <p class="text-body-md text-secondary">Indica referencias del RUT y del análisis de residencia. Es una referencia documental, no una carga de archivos. La elección de tratamiento no acredita por sí sola un beneficio.</p>
            <p id="regimen-availability" class="text-body-md text-secondary" role="status">El régimen ordinario tiene depuración inicial disponible. Las otras opciones se ofrecen para consultar su definición y alcance.</p>
            <button id="create-profile" class="rounded-xl bg-inverse-surface text-on-secondary px-4 py-2" type="submit">Crear perfil y abrir ingresos</button>
        </form>
        <a class="text-secondary underline" href="<?= e(url($config,'contribuyentes')) ?>">Volver a contribuyentes</a>
    </section>
</main>
<script src="<?= e(asset($config,'js/ingresos-guidance.js')) ?>" defer></script>
<?php require __DIR__.'/../partials/footer.php'; ?>
