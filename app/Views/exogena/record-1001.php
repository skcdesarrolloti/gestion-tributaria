<?php $title='Registro 1001'; require __DIR__.'/../partials/head.php'; ?>
<body class="bg-background min-h-screen font-body-md text-on-surface antialiased">
<?php require __DIR__.'/../partials/header.php'; ?>
<main class="portal-container space-y-space-lg py-8">
    <section class="bg-inverse-surface text-on-secondary rounded-2xl p-space-xl">
        <p class="text-label-md uppercase tracking-wide">Exógena · 1001 · Año 2025 · Versión 10</p>
        <h1 class="text-headline-lg font-semibold mt-2"><?= $id===null?'Nuevo registro':'Editar registro' ?></h1>
        <p class="text-body-md mt-2 taxpayer-name">Empresa informante: <?= e($company['nombre']) ?></p>
    </section>
    <?php require __DIR__.'/notice-1001.php'; ?>
    <?php if ($errors): ?><div role="alert" class="bg-primary-container rounded-xl p-4"><ul><?php foreach ($errors as $error): ?><li><?= e($error) ?></li><?php endforeach; ?></ul></div><?php endif; ?>
    <form method="post" action="<?= e(url($config,'exogena/1001/registro').($id===null?'&contribuyente='.$company['id']:'&id='.$id)) ?>" class="space-y-space-lg">
        <?php require __DIR__.'/../partials/csrf.php'; ?>
        <section class="bg-surface-container-lowest rounded-2xl border border-surface-container p-space-xl">
            <label class="text-body-md">Año gravable *
                <input name="anio" value="<?= e($record['anio']) ?>" inputmode="numeric" pattern="2025" maxlength="4" required class="mt-2 w-full px-space-md py-space-md border-0 rounded-xl bg-surface-container-low">
            </label>
        </section>
        <?php $fields=\App\Services\Exogena1001Fields::all(); $money=\App\Services\Exogena1001Fields::money(); ?>
        <?php foreach (['A'=>['C','Concepto e identificación'],'D'=>['L','Beneficiario y ubicación'],'M'=>['P','Pagos e IVA mayor valor'],'Q'=>['T','Retenciones']] as $first=>[$last,$heading]): ?>
            <fieldset class="bg-surface-container-lowest rounded-2xl border border-surface-container p-space-xl space-y-space-md">
                <legend class="text-headline-sm font-semibold"><?= e($heading) ?></legend>
                <div class="profile-grid">
                    <?php foreach ($fields as $key=>[$column,$label,$max,$help]): ?>
                        <?php if ($column<$first || $column>$last) { continue; } ?>
                        <?php $required=in_array($key,array_merge($money,['concepto','documento_tipo','identificacion','pais']),true); ?>
                        <label class="text-body-md taxpayer-name"><?= e($column.' · '.$label) ?><?= $required?' *':'' ?>
                            <input name="<?= e($key) ?>" value="<?= e($record[$key]) ?>" maxlength="<?= e($max) ?>" <?= $required?'required':'' ?> <?= in_array($key,$money,true)?'inputmode="numeric" pattern="(0|[1-9][0-9]{0,17})"':'' ?> class="mt-2 w-full px-space-md py-space-md border-0 rounded-xl bg-surface-container-low">
                            <span class="block text-secondary mt-2"><?= e($help) ?></span>
                        </label>
                    <?php endforeach; ?>
                </div>
            </fieldset>
        <?php endforeach; ?>
        <p class="text-body-md text-secondary">Importes en COP enteros, sin separadores. Cero debe indicarse expresamente. Revisar el catálogo y los soportes; no se calcula automáticamente ninguna retención.</p>
        <button class="rounded-xl bg-inverse-surface text-on-secondary px-4 py-2" type="submit">Guardar borrador 1001</button>
        <a class="text-secondary underline" href="<?= e(url($config,'exogena/1001').'&contribuyente='.$company['id'].'&anio=2025') ?>">Volver a los registros</a>
    </form>
</main>
<?php require __DIR__.'/../partials/footer.php'; ?>
