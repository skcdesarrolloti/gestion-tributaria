<section class="bg-primary-container rounded-xl p-4 space-y-space-sm">
    <h2 class="text-headline-sm font-semibold">RUT y perfil tributario ampliado</h2>
    <p>Este formulario conserva tu identificación básica. El perfil ampliado permite diligenciar por año actividades, responsabilidades, ubicación, representantes, familia, patrimonio y condiciones tributarias, en bloques independientes.</p>
    <?php if ($id!==null): ?>
        <a class="inline-block rounded-xl bg-inverse-surface text-on-secondary px-4 py-2" href="<?= e(url($config,'contribuyentes/perfil').'&id='.$id) ?>">Completar perfil tributario y RUT</a>
        <a class="inline-block rounded-xl bg-inverse-surface text-on-secondary px-4 py-2" href="<?= e(url($config,'contribuyentes/rut').'&id='.$id) ?>">Subir RUT en PDF</a>
    <?php else: ?>
        <p>Primero pulsa Guardar contribuyente. En el listado encontrarás el botón Completar perfil tributario y RUT.</p>
    <?php endif; ?>
    <p class="text-body-md">Puedes guardar borradores sin disponer aún del RUT. El perfil mostrará datos pendientes; la información no se presenta ni actualiza en la DIAN.</p>
</section>
