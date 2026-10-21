<section class="bg-surface-container-lowest rounded-2xl border border-surface-container p-4 space-y-space-md">
    <h2 class="text-headline-md font-semibold">2 · Trabajo y pensión mes a mes</h2>
    <p>Separa salario de pensión. Prepagada: pago elegible; la hoja aplica 16 UVT por mes. Marca dependiente solo en meses con derecho acreditado; el límite de 32 UVT es total mensual. Salud de pensión no se repite en INCR de trabajo.</p>
    <div class="renta-sheet-scroll"><table class="renta-sheet-table text-body-md">
        <caption>Importes recibidos o previstos por mes · <?= e($profile['anio']) ?></caption>
        <thead><tr><th>Mes</th><th>Salario bruto</th><th>Pensión bruta</th><th>Salud de pensión</th><th>Prepagada elegible</th><th>Dependiente art. 387</th></tr></thead>
        <tbody><?php foreach (\App\Services\SimuladorFields::MONTHS as $m=>$month): ?><tr>
            <th scope="row"><?= e($month) ?></th>
            <?php foreach (['salario','pension','salud_pension','prepagada'] as $key): $field=$key.'_'.($m+1); ?>
                <td><input name="<?= e($field) ?>" value="<?= e($record[$field]) ?>" inputmode="numeric" pattern="[0-9]*" maxlength="12" aria-label="<?= e($key.' '.$month) ?>" class="w-full rounded-xl bg-surface-container-low p-2"></td>
            <?php endforeach; ?>
            <td><input type="checkbox" name="dep387_<?= e($m+1) ?>" value="1" <?= $record['dep387_'.($m+1)]==='1'?'checked':'' ?> aria-label="<?= e('Dependiente art. 387 en '.$month) ?>"></td>
        </tr><?php endforeach; ?></tbody>
    </table></div>
    <p class="text-label-sm">Pensión exenta: cálculo por mes después de salud, hasta 1.000 UVT mensuales. No se reparte un límite anual entre meses. Cesantías se registran aparte.</p>
</section>
