<?php $title='Registro 1007 · Ingresos Recibidos'; require __DIR__.'/../partials/head.php'; ?>
<body class="bg-background min-h-screen font-body-md text-on-surface antialiased">
<?php require __DIR__.'/../partials/header.php'; ?>
<main class="portal-container space-y-space-lg py-8">
    <section class="bg-inverse-surface text-on-secondary rounded-2xl p-space-xl">
        <p class="text-label-md uppercase tracking-wide">Exógena · 1007 · Ingresos Recibidos</p>
        <h1 class="text-headline-lg font-semibold mt-2"><?= $id===null?'Nuevo registro':'Editar registro' ?></h1>
        <p class="text-body-md mt-2 taxpayer-name">Empresa informante: <?= e($company['nombre']) ?></p>
    </section>
    <?php require __DIR__.'/notice-1007.php'; ?>
    <?php if ($errors): ?><div role="alert" class="bg-primary-container rounded-xl p-4"><ul><?php foreach ($errors as $error): ?><li><?= e($error) ?></li><?php endforeach; ?></ul></div><?php endif; ?>
    <form method="post" action="<?= e(url($config,'exogena/1007/registro').($id===null?'&contribuyente='.$company['id']:'&id='.$id)) ?>" class="space-y-space-lg">
        <fieldset <?= $readOnly?'disabled':'' ?> class="space-y-space-md">
        <?php if ($readOnly): ?><legend>Solo consulta: no se guardan cambios en esta ficha.</legend><?php endif; ?>
        <?php require __DIR__.'/../partials/csrf.php'; ?>
        <section class="bg-surface-container-lowest rounded-2xl border border-surface-container p-space-xl space-y-space-md">
            <label class="text-body-md">Año gravable *
                <input name="anio" value="<?= e($record['anio']) ?>" inputmode="numeric" pattern="20[0-9]{2}" maxlength="4" required class="mt-2 w-full px-space-md py-space-md border-0 rounded-xl bg-surface-container-low">
            </label>
        </section>
        <section class="bg-surface-container-lowest rounded-2xl border border-surface-container p-space-xl space-y-space-md">
            <h2 class="text-headline-sm font-semibold">Datos del registro</h2>
            <div class="profile-grid">
                <?php foreach (\App\Services\Exogena1007Fields::all() as $key=>[$column,$label,$max,$help]): ?>
                    <?php $required=in_array($key,['concepto','documento_tipo','identificacion','pais','ingresos_brutos','devoluciones'],true); ?>
                    <label class="text-body-md taxpayer-name"><?= e($column.' · '.$label) ?><?= $required?' *':'' ?>
                        <input name="<?= e($key) ?>" value="<?= e($record[$key]) ?>" maxlength="<?= e($max) ?>" <?= $required?'required':'' ?> <?= in_array($key,\App\Services\Exogena1007Fields::money(),true)?'inputmode="numeric" pattern="(0|[1-9][0-9]{0,17})"':'' ?> class="mt-2 w-full px-space-md py-space-md border-0 rounded-xl bg-surface-container-low">
                        <span class="block text-secondary mt-2"><?= e($help) ?></span>
                    </label>
                <?php endforeach; ?>
            </div>
        </section>
        <p class="text-body-md text-secondary">Los códigos se capturan manualmente y requieren revisión del catálogo del año. Los importes son enteros en COP, sin separadores. Un valor desconocido no se convierte a cero.</p>
        <button class="rounded-xl bg-inverse-surface text-on-secondary px-4 py-2" type="submit">Guardar borrador 1007</button>
        <a class="text-secondary underline" href="<?= e(url($config,'exogena/1007').'&contribuyente='.$company['id'].'&anio='.$record['anio']) ?>">Volver a los registros</a>
        </fieldset>
    </form>
</main>
<?php require __DIR__.'/../partials/footer.php'; ?>
