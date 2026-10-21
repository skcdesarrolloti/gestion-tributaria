<?php $checklist=\App\Services\CaracterizacionChecklist::review($taxpayer,$blocks,$schemas,$annual); ?>
<section class="bg-primary-container rounded-xl p-4 space-y-space-sm">
    <h2 class="text-headline-md font-semibold">Qué falta completar</h2>
    <p><?= count($blocks) ?> de <?= count($schemas) ?> bloques guardados · <?= e($checklist['reviewed']) ?> contrastados por el usuario.</p>
    <p>Sin respuesta significa pendiente; «No aplica» exige una explicación. Puedes diligenciar ahora y contrastar después con los documentos.</p>
    <?php if ($checklist['alerts']): ?><ul class="list-disc pl-4"><?php foreach ($checklist['alerts'] as $alert): ?><li><?= e($alert) ?></li><?php endforeach; ?></ul><?php endif; ?>
</section>
<div class="exogena-format-grid">
    <?php foreach ($schemas as $key=>$definition): $check=$checklist['blocks'][$key]; ?>
        <article class="bg-surface-container-lowest rounded-xl border border-surface-container p-4 space-y-space-sm">
            <h2 class="text-headline-sm font-semibold"><?= e($definition['title']) ?></h2>
            <p class="text-body-md text-secondary"><?= e($check['status']) ?></p>
            <?php if ($check['missing']): ?><details><summary class="underline text-secondary"><?= count($check['missing']) ?> pendientes</summary><ul class="list-disc pl-4"><?php foreach ($check['missing'] as $missing): ?><li><?= e($missing) ?></li><?php endforeach; ?></ul></details><?php endif; ?>
            <a class="inline-block rounded-xl bg-inverse-surface text-on-secondary px-4 py-2" href="<?= e($base.'&bloque='.$key) ?>">Diligenciar bloque</a>
        </article>
    <?php endforeach; ?>
</div>
