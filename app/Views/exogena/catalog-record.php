<?php $title='Registro '.$schema['code']; require __DIR__.'/../partials/head.php'; ?>
<body class="bg-background min-h-screen font-body-md text-on-surface antialiased">
<?php require __DIR__.'/../partials/header.php'; ?>
<main class="portal-container space-y-space-lg py-8">
    <section class="bg-inverse-surface text-on-secondary rounded-2xl p-space-xl">
        <p class="text-label-md uppercase tracking-wide">Exógena · <?= e($schema['code']) ?> · <?= e($schema['year']) ?></p>
        <h1 class="text-headline-lg font-semibold mt-2"><?= e(($id===null?'Nuevo registro · ':'Editar registro · ').$schema['name']) ?></h1>
        <p class="text-body-md mt-2 taxpayer-name">Cliente: <?= e($company['nombre']) ?></p>
    </section>
    <?php require __DIR__.'/catalog-notice.php'; ?>
    <?php if ($errors): ?><div role="alert" class="bg-primary-container rounded-xl p-4"><ul><?php foreach ($errors as $error): ?><li><?= e($error) ?></li><?php endforeach; ?></ul></div><?php endif; ?>
    <form method="post" action="<?= e(url($config,'exogena/'.$schema['code'].'/registro').($id===null?'&contribuyente='.$company['id']:'&id='.$id)) ?>" class="space-y-space-lg">
        <fieldset <?= $readOnly?'disabled':'' ?> class="space-y-space-md">
        <?php if ($readOnly): ?><legend>Solo consulta: no se guardan cambios en esta ficha.</legend><?php endif; ?>
        <?php require __DIR__.'/../partials/csrf.php'; ?>
        <section class="bg-surface-container-lowest rounded-2xl border border-surface-container p-space-xl">
            <label class="text-body-md">Año gravable *
                <input name="anio" value="<?= e($record['anio']) ?>" inputmode="numeric" pattern="2025" maxlength="4" required class="mt-2 w-full px-space-md py-space-md border-0 rounded-xl bg-surface-container-low">
            </label>
        </section>
        <?php foreach (\App\Services\ExogenaSchemas::sections($schema) as $section): ?>
            <fieldset class="bg-surface-container-lowest rounded-2xl border border-surface-container p-space-xl space-y-space-md">
                <legend class="text-headline-sm font-semibold"><?= e($section['name']) ?></legend>
                <div class="profile-grid">
                    <?php foreach ($section['fields'] as $key=>[$label,$kind,$max,$required,$help]): ?>
                        <label class="text-body-md taxpayer-name"><?= e($label) ?><?= $required?' *':'' ?>
                            <input name="<?= e($key) ?>" value="<?= e($record[$key]) ?>" maxlength="<?= e($max) ?>" <?= $required?'required':'' ?> <?= in_array($kind,['money','digits','code'],true)?'inputmode="numeric"':'' ?> class="mt-2 w-full px-space-md py-space-md border-0 rounded-xl bg-surface-container-low">
                            <span class="block text-secondary mt-2"><?= e($help) ?></span>
                        </label>
                    <?php endforeach; ?>
                </div>
            </fieldset>
        <?php endforeach; ?>
        <p class="text-body-md text-secondary">Los campos están vacíos al crear el registro. Escribe cero solo cuando esté confirmado; no se calculan automáticamente importes, retenciones ni porcentajes.</p>
        <button class="rounded-xl bg-inverse-surface text-on-secondary px-4 py-2" type="submit">Guardar borrador <?= e($schema['code']) ?></button>
        <a class="text-secondary underline" href="<?= e(url($config,'exogena/'.$schema['code']).'&contribuyente='.$company['id'].'&anio='.$schema['year']) ?>">Volver a registros del cliente</a>
        </fieldset>
    </form>
</main>
<?php require __DIR__.'/../partials/footer.php'; ?>
