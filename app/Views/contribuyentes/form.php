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
        <p class="text-body-md mt-2">Ten a mano el documento de identidad o el RUT. No necesitas ingresar valores de ingresos todavía.</p>
    </section>
    <?php require __DIR__ . '/guide.php'; ?>
    <?php require __DIR__ . '/profile-scope.php'; ?>
    <section class="bg-surface-container-lowest rounded-2xl border border-surface-container p-space-xl">
        <?php if ($errors): ?>
            <div role="alert" class="p-3 mb-4 rounded-xl bg-error-container text-on-error-container">
                <p class="font-semibold">Revisa los siguientes datos:</p>
                <ul class="list-disc pl-4"><?php foreach ($errors as $error): ?><li><?= e($error) ?></li><?php endforeach; ?></ul>
            </div>
        <?php endif; ?>
        <form method="post" action="<?= e($action) ?>" class="space-y-space-lg">
            <?php require __DIR__ . '/../partials/csrf.php'; ?>
            <fieldset class="space-y-space-md">
                <legend class="text-headline-md font-semibold">Datos de identificación</legend>
                <div class="profile-grid">
                    <?php foreach (['tipo' => ['Tipo de contribuyente', ContribuyenteData::TIPOS], 'documento_tipo' => ['Tipo de documento', ContribuyenteData::DOCUMENTOS]] as $field => [$label, $options]): ?>
                        <div>
                            <label class="block text-body-md mb-1" for="<?= e($field) ?>"><?= e($label) ?> *</label>
                            <select class="w-full px-3 py-2.5 bg-surface-container-low rounded-xl border border-transparent focus:border-outline-variant" id="<?= e($field) ?>" name="<?= e($field) ?>" required>
                                <option value="">Selecciona una opción</option>
                                <?php foreach ($options as $key => $value): ?><option value="<?= e($key) ?>" <?= $record[$field] === $key ? 'selected' : '' ?>><?= e($value) ?></option><?php endforeach; ?>
                            </select>
                            <?php $helpTitle=$label; $helpArticle=null;
                            $helpText=$field==='tipo'
                                ? 'Escoge la persona o entidad que vas a registrar. Para una persona fallecida, revisa si corresponde registrar su sucesión ilíquida con el NIT del RUT; no la registres como persona jurídica por ese solo hecho. Ser pensionado, empleado o independiente se indica como fuente de ingresos, no como tipo de contribuyente.'
                                : 'Copia el tipo de documento del soporte de identificación. Para personas jurídicas y sucesiones ilíquidas, este formulario exige NIT. Escribe el número sin puntos ni guiones y el dígito de verificación en su campo separado.';
                            require __DIR__.'/../partials/field-help.php'; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div>
                    <label class="block text-body-md mb-1" for="nombre">Nombre completo o razón social *</label>
                    <input class="w-full px-3 py-2.5 bg-surface-container-low rounded-xl border border-transparent focus:border-outline-variant" id="nombre" name="nombre" maxlength="150" value="<?= e($record['nombre']) ?>" required>
                </div>
                <div class="profile-grid">
                    <div>
                        <label class="block text-body-md mb-1" for="documento_numero">Número de documento *</label>
                        <input class="w-full px-3 py-2.5 bg-surface-container-low rounded-xl border border-transparent focus:border-outline-variant" id="documento_numero" name="documento_numero" maxlength="30" value="<?= e($record['documento_numero']) ?>" aria-describedby="documento-ayuda" required>
                        <p id="documento-ayuda" class="text-body-md text-secondary mt-2">Sin puntos, espacios ni guiones. Si usas NIT, escribe el dígito de verificación por separado.</p>
                    </div>
                    <div>
                        <label class="block text-body-md mb-1" for="dv">Dígito de verificación del NIT (opcional)</label>
                        <input class="w-full px-3 py-2.5 bg-surface-container-low rounded-xl border border-transparent focus:border-outline-variant" id="dv" name="dv" inputmode="numeric" pattern="[0-9]" maxlength="1" value="<?= e($record['dv']) ?>">
                    </div>
                </div>
            </fieldset>
            <?php require __DIR__ . '/sources.php'; ?>
            <?php require __DIR__ . '/declaration-guide.php'; ?>
            <p class="text-body-md text-secondary">* Campos obligatorios. El siguiente paso será revisar el perfil tributario por año y las cédulas correspondientes.</p>
            <div class="flex items-center gap-space-md flex-wrap">
                <button class="rounded-xl bg-inverse-surface text-on-secondary px-4 py-2" type="submit">Guardar contribuyente</button>
                <a class="text-secondary underline" href="<?= e(url($config, 'contribuyentes')) ?>">Volver al listado</a>
            </div>
        </form>
    </section>
</main>
<script src="<?= e(asset($config, 'js/contribuyentes.js')) ?>" defer></script>
<?php require __DIR__ . '/../partials/footer.php'; ?>
