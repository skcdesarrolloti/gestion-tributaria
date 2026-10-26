<section class="bg-surface-container-lowest rounded-2xl border border-surface-container p-space-xl space-y-space-md">
    <h2 class="text-headline-md font-semibold">Detalle registrado (<?= count($movements) ?>)</h2>
    <?php if (!$movements): ?><p class="text-body-md text-secondary">Registra el primer movimiento. Cada registro alimenta una sola casilla.</p><?php endif; ?>
    <?php foreach ($movements as $movement): ?>
        <article class="rounded-xl border border-surface-container p-4 space-y-space-sm taxpayer-name">
            <h3 class="font-semibold"><?= e($movement['fecha']) ?> · Casilla <?= e($movement['casilla']) ?> · <?= e($movement['estado']) ?> · $<?= e(number_format((int)$movement['valor_cop'],0,',','.')) ?></h3>
            <p class="text-body-md"><?= e($movement['descripcion']) ?></p>
            <p class="text-body-md text-secondary">Tercero: <?= e($movement['tercero']) ?> · País: <?= e($movement['pais']) ?> · Original: <?= e($movement['moneda']) ?> <?= e($movement['valor_original']) ?></p>
            <p class="text-body-md text-secondary">Soporte: <?= e($movement['soporte']?:'Pendiente (proyección)') ?></p>
            <?php if ($movement['justificacion']!==''): ?><p class="text-body-md text-secondary">Justificación: <?= e($movement['justificacion']) ?></p><?php endif; ?>
            <?php if ($movement['conversion']!==''): ?><p class="text-body-md text-secondary">Conversión: <?= e($movement['conversion']) ?></p><?php endif; ?>
            <?php if (!empty($movement['nomina_id'])): ?><a class="text-secondary underline" href="<?= e(url($config,'nomina').'&anio='.$profile['anio']) ?>">Corregir desde nómina</a><?php else: ?>
            <a class="text-secondary underline" href="<?= e(url($config,'ingresos/movimiento').'&id='.$profile['id'].'&movimiento='.$movement['id'].(isset($registration)?'&origen=registro'.\App\Services\RegistroNavegacion::query($registration,(int)$profile['contribuyente_id']):'')) ?>">Editar movimiento</a><?php endif; ?>
        </article>
    <?php endforeach; ?>
</section>
