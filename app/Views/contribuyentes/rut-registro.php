<section class="bg-primary-container rounded-xl p-4 space-y-space-md">
    <h2 class="text-headline-sm font-semibold">Comienza subiendo el RUT</h2>
    <p>El PDF rellena la identificación de abajo. Al guardar se crea también el perfil con los datos reconocidos de sus hojas; después completas solo lo que falte.</p>
    <form method="post" enctype="multipart/form-data" action="<?= e($action) ?>" class="space-y-space-sm">
        <?php require __DIR__.'/../partials/csrf.php'; ?>
        <input type="hidden" name="accion" value="leer_rut"><input type="hidden" name="MAX_FILE_SIZE" value="5242880">
        <div class="profile-grid">
            <div><label class="block" for="registro-rut-pdf">RUT completo en PDF · hasta 5 MB</label><input id="registro-rut-pdf" name="rut_pdf" type="file" accept="application/pdf,.pdf" required class="w-full bg-surface-container-low rounded-xl p-2"></div>
            <div><label class="block" for="registro-rut-anio">Año del perfil que vas a completar</label><select id="registro-rut-anio" name="rut_anio" class="w-full bg-surface-container-low rounded-xl p-2"><?php foreach (['2026','2025'] as $option): ?><option value="<?= e($option) ?>" <?= ($rutDraft['year']??'2026')===$option?'selected':'' ?>><?= e($option) ?></option><?php endforeach; ?></select></div>
        </div>
        <button type="submit" class="rounded-xl bg-inverse-surface text-on-secondary px-4 py-2"><?= $rutDraft?'Leer otro RUT':'Subir RUT y rellenar datos' ?></button>
    </form>
    <?php if ($rutDraft): ?>
        <p role="status" class="font-semibold">RUT leído · <?= e($rutDraft['file']['parsed']['pages']) ?> página(s) · <?= e($rutDraft['count']) ?> campos y renglones reconocidos para <?= e($rutDraft['year']) ?>.</p>
        <p>Revisa los datos de abajo y confirma el tipo de contribuyente, especialmente si corresponde a una sucesión ilíquida. Todavía no se ha creado ningún registro.</p>
        <details><summary class="underline text-secondary">Ver datos del perfil que se guardarán y comprobaciones</summary>
            <?php require __DIR__.'/rut-registro-preview.php'; ?>
            <ul class="list-disc pl-4"><?php foreach ($rutDraft['warnings'] as $warning): ?><li><?= e($warning) ?></li><?php endforeach; ?></ul>
        </details>
        <form method="post" action="<?= e($action) ?>"><?php require __DIR__.'/../partials/csrf.php'; ?><input type="hidden" name="accion" value="descartar_rut"><input type="hidden" name="rut_registro_token" value="<?= e($rutDraft['token']) ?>"><button class="underline text-secondary" type="submit">Descartar RUT y diligenciar manualmente</button></form>
    <?php else: ?><a class="underline text-secondary" href="#datos-identificacion">No tengo el RUT: continuar con registro manual ↓</a><p class="text-body-md">Se admiten PDF digitales de la DIAN; un escaneo requiere transcripción. La lectura no consulta el estado actual ante la DIAN.</p><?php endif; ?>
</section>
