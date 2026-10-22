<section class="bg-surface-container-lowest rounded-xl border border-surface-container p-4 space-y-space-md">
    <h2 class="text-headline-md font-semibold">RUT y documentos</h2>
    <?php if (!$readOnly): ?><p>Sube el RUT completo. Se leen las hojas reconocidas y se rellenan los campos vacíos de cada pestaña. Los datos diferentes que ya guardaste se conservan.</p>
    <form method="post" enctype="multipart/form-data" action="<?= e(url($config,'contribuyentes/rut').'&id='.$taxpayer['id'].'&anio='.$year) ?>" class="space-y-space-md">
        <?php require __DIR__.'/../partials/csrf.php'; ?>
        <input type="hidden" name="accion" value="leer"><input type="hidden" name="aplicar_vacios" value="1"><input type="hidden" name="MAX_FILE_SIZE" value="5242880">
        <label class="block" for="perfil-rut-pdf">RUT completo en PDF · hasta 5 MB</label>
        <input id="perfil-rut-pdf" name="rut_pdf" type="file" accept="application/pdf,.pdf" required class="w-full bg-surface-container-low rounded-xl p-2">
        <button type="submit" class="rounded-xl bg-inverse-surface text-on-secondary px-4 py-2">Subir RUT y completar perfil</button>
    </form>
    <p class="text-body-md text-secondary">PDF digital con texto; las hojas no legibles se informan para completarlas manualmente. La lectura no consulta el estado actual en la DIAN.</p>
    <?php endif; ?>
    <?php if ($documents): ?><h3 class="font-semibold">Soportes guardados</h3>
        <?php foreach ($documents as $document): ?><p class="taxpayer-name"><?= e($document['titulo']) ?> · <?= e($document['created_at']) ?> · <a class="underline text-secondary" href="<?= e(url($config,'contribuyentes/rut/documento').'&documento='.$document['id']) ?>">Descargar RUT</a></p><?php endforeach; ?>
    <?php else: ?><p>No has guardado un RUT en este perfil.</p><?php endif; ?>
</section>
