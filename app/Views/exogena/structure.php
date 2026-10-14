<section class="bg-surface-container-lowest rounded-2xl border border-surface-container p-space-xl space-y-space-md">
    <h2 class="text-headline-md font-semibold">Estructura del formato 5247</h2>
    <p class="text-body-md text-secondary">Empresa informante y año gravable <?= e($year??\App\Services\Exogena5247Fields::DEFAULT_YEAR) ?>, seguidos de 22 campos en el orden de tu plantilla. Los campos de captura inician vacíos.</p>
    <div class="profile-grid">
        <?php foreach (['A'=>['D','Contrato y concepto'],'E'=>['M','Datos del informado'],'N'=>['S','Valores y retenciones'],'T'=>['V','Fideicomiso y participante']] as $first=>[$last,$heading]): ?>
            <article class="rounded-xl border border-surface-container p-4 space-y-space-sm">
                <p class="text-label-md text-secondary">Columnas <?= e($first.'–'.$last) ?></p>
                <h3 class="font-semibold"><?= e($heading) ?></h3>
                <ul class="space-y-space-sm text-body-md">
                    <?php foreach (\App\Services\Exogena5247Fields::all() as [$column,$label]): ?>
                        <?php if ($column<$first || $column>$last) { continue; } ?>
                        <li class="taxpayer-name"><?= e($column.' · '.$label) ?></li>
                    <?php endforeach; ?>
                </ul>
            </article>
        <?php endforeach; ?>
    </div>
</section>
