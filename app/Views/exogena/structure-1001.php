<section class="bg-surface-container-lowest rounded-2xl border border-surface-container p-space-xl space-y-space-md">
    <h2 class="text-headline-md font-semibold">Estructura del formato 1001</h2>
    <p class="text-body-md text-secondary">Año 2025 · Versión 10 · Veinte campos. Las letras indican su orden en esta pantalla.</p>
    <div class="profile-grid">
        <?php foreach (['A'=>['C','Concepto e identificación'],'D'=>['L','Beneficiario y ubicación'],'M'=>['P','Pagos e IVA mayor valor'],'Q'=>['T','Retenciones practicadas y asumidas']] as $first=>[$last,$heading]): ?>
            <article class="rounded-xl border border-surface-container p-4 space-y-space-sm">
                <h3 class="font-semibold"><?= e($heading) ?></h3>
                <ul class="space-y-space-sm text-body-md">
                    <?php foreach (\App\Services\Exogena1001Fields::all() as [$column,$label]): ?>
                        <?php if ($column<$first || $column>$last) { continue; } ?>
                        <li class="taxpayer-name"><?= e($column.' · '.$label) ?></li>
                    <?php endforeach; ?>
                </ul>
            </article>
        <?php endforeach; ?>
    </div>
</section>
