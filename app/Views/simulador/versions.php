<section class="bg-surface-container-lowest rounded-2xl border border-surface-container p-4 space-y-space-sm">
    <h2 class="text-headline-sm font-semibold">Versiones y comparación</h2>
    <?php if ($saved): ?><p>Versión cargada: <?= e($saved['nombre']) ?> · <?= e($saved['created_at']) ?>. Cada guardado crea una copia nueva.</p><?php endif; ?>
    <?php if (!$versions): ?><p>Aún no hay versiones guardadas para esta persona y año.</p><?php endif; ?>
    <?php foreach ($versions as $version): ?>
        <p class="text-body-md taxpayer-name"><?= e($version['nombre'].' · '.$version['created_at']) ?>
            <a class="text-secondary underline" href="<?= e(url($config,'simulador').'&escenario='.$version['id']) ?>">Cargar</a> ·
            <a class="text-secondary underline" href="<?= e(url($config,'simulador').'&id='.$profile['id'].($saved?'&escenario='.$saved['id']:'').'&base='.$version['id']) ?>">Usar como base de comparación</a></p>
    <?php endforeach; ?>
    <?php if ($base): ?><p class="font-semibold">Comparando con: <?= e($base['nombre']) ?>. Cambio de impuesto y cambio de saldo se muestran por separado.</p><?php endif; ?>
</section>
