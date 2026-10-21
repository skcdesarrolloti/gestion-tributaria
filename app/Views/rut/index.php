<?php $title='Subir RUT'; $action=url($config,'contribuyentes/rut').'&id='.$taxpayer['id'].'&anio='.$year; require __DIR__.'/../partials/head.php'; ?>
<body class="bg-background min-h-screen font-body-md text-on-surface antialiased">
<?php require __DIR__.'/../partials/header.php'; ?>
<main class="portal-container space-y-space-lg py-8">
    <section class="bg-inverse-surface text-on-secondary rounded-2xl p-space-xl space-y-space-sm">
        <h1 class="text-headline-lg font-semibold">Subir RUT en PDF</h1>
        <p class="taxpayer-name"><?= e($taxpayer['nombre']) ?> · Perfil <?= e($year) ?></p>
        <p>1 · Subir PDF → 2 · Revisar datos → 3 · Aplicar al perfil.</p>
    </section>
    <a class="text-secondary underline" href="<?= e(url($config,'contribuyentes/perfil').'&id='.$taxpayer['id'].'&anio='.$year) ?>">← Volver al perfil tributario</a>
    <?php if ($errors): ?><div role="alert" class="bg-error-container text-on-error-container rounded-xl p-4"><ul class="list-disc pl-4"><?php foreach ($errors as $error): ?><li><?= e($error) ?></li><?php endforeach; ?></ul></div><?php endif; ?>
    <section class="bg-surface-container-lowest rounded-xl border border-surface-container p-4 space-y-space-md">
        <h2 class="text-headline-md font-semibold">Seleccionar documento</h2>
        <form method="post" enctype="multipart/form-data" action="<?= e($action) ?>" class="space-y-space-md">
            <?php require __DIR__.'/../partials/csrf.php'; ?>
            <input type="hidden" name="accion" value="leer"><input type="hidden" name="MAX_FILE_SIZE" value="5242880">
            <label class="block" for="rut_pdf">RUT descargado de la DIAN · PDF con texto · Hasta 5 MB y 10 páginas</label>
            <input id="rut_pdf" name="rut_pdf" type="file" accept="application/pdf,.pdf" required class="block w-full bg-surface-container-low rounded-xl p-2">
            <button type="submit" class="rounded-xl bg-inverse-surface text-on-secondary px-4 py-2">Leer PDF y mostrar datos</button>
        </form>
        <p class="text-body-md text-secondary">Se recorren todas las páginas: identificación, entidad, representantes, socios/junta, contador, establecimientos y estados de las hojas 1–7 reconocidas. Los datos legibles completan el perfil; los campos ausentes quedan para diligenciar manualmente. Los escaneos o diseños no reconocidos se informan para revisión. El PDF se conserva como soporte privado al aplicar.</p>
    </section>
    <?php if ($preview): require __DIR__.'/preview.php'; endif; ?>
    <details class="bg-primary-container rounded-xl p-4 space-y-space-sm" open>
        <summary class="font-semibold">? · Vigencia y estado actual del RUT</summary>
        <p>La inscripción del RUT tiene vigencia indefinida. La fecha de generación del PDF y la fecha del trámite no son fechas de vencimiento. Debe actualizarse cuando cambien sus datos.</p>
        <p>Esta lectura verifica coincidencia de identidad, NIT/dígito y campos reconocidos. No autentica el PDF ni consulta automáticamente el estado actual en la DIAN. Para confirmar Activo, Suspendido o Cancelado, utiliza la consulta oficial y registra el resultado en RUT · Identificación.</p>
        <p><a class="text-secondary underline" href="https://www.dian.gov.co/impuestos/RUT/Paginas/Consultas-RUT.aspx" target="_blank" rel="noopener noreferrer">Abrir consultas oficiales DIAN → Consulte su estado RUT</a> · <a class="text-secondary underline" href="https://www.dian.gov.co/impuestos/RUT/Paginas/Conozca-el-RUT.aspx" target="_blank" rel="noopener noreferrer">DIAN · Vigencia indefinida</a></p>
    </details>
    <?php if ($documents): ?>
    <details class="bg-surface-container-lowest rounded-xl border border-surface-container p-4">
        <summary class="font-semibold">PDF guardados como soporte (<?= count($documents) ?> más recientes)</summary>
        <?php foreach ($documents as $document): ?><p class="mt-2 taxpayer-name"><?= e($document['titulo']) ?> · <?= e($document['created_at']) ?> · <a class="text-secondary underline" href="<?= e(url($config,'contribuyentes/rut/documento').'&documento='.$document['id']) ?>">Descargar PDF privado</a></p><?php endforeach; ?>
    </details>
    <?php endif; ?>
</main>
<?php require __DIR__.'/../partials/footer.php'; ?>
