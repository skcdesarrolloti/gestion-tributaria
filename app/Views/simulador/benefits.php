<section class="space-y-space-md">
    <h2 class="text-headline-md font-semibold">4 · Las 52 fichas y sus celdas</h2>
    <p>Los costos se asignan a una sola fuente. Vivienda e ICETEX: intereses pagados; GMF: impuesto pagado completo; compras: total de adquisiciones elegibles, no su 1%. Aportes FPV/AFC: valor aportado; donaciones: valor donado. Los límites se aplican después.</p>
    <?php $coverage=\App\Services\SimuladorFields::coverage(); $mapped=\App\Services\SimuladorFields::benefitInputs(); ?>
    <?php foreach (\App\Services\BeneficioCatalog::groups() as $group=>$label): ?>
    <details class="bg-surface-container-lowest rounded-2xl border border-surface-container p-4">
        <summary class="text-headline-sm font-semibold"><?= e($label) ?></summary>
        <div class="renta-sheet-scroll"><table class="renta-sheet-table text-body-md">
            <thead><tr><th>Concepto y norma</th><th>Tratamiento en esta hoja</th><th>Celdas editables / origen</th></tr></thead>
            <tbody><?php foreach ($coverage as $id=>$item): if ($item['group']!==$group) { continue; } ?>
                <tr><th scope="row" class="taxpayer-name"><?= e($item['title']) ?><span class="block text-label-sm text-secondary"><?= e($item['norm']) ?></span>
                    <details><summary class="text-label-sm underline">Condiciones</summary><p class="text-body-md font-normal"><?= e($item['limit']) ?></p><p class="text-body-md font-normal"><?= e($item['evidence']) ?></p></details>
                </th>
                <td><?= e(['costos'=>'Costo procedente por fuente','automatico'=>'Regla común automatizada','dato-verificado'=>'Entrada verificada + control','revision'=>'Regla particular pendiente','control'=>'Control: no se resta'][$item['mode']]) ?></td>
                <td>
                <?php if ($item['mode']==='costos'): foreach (['servicios'=>'Servicios independientes','capital'=>'Capital / arriendos','no_laborales'=>'No laborales'] as $source=>$text): $field='c_'.$id.'_'.$source; ?>
                    <label class="block"><?= e($text) ?><input name="<?= e($field) ?>" value="<?= e($record[$field]) ?>" inputmode="numeric" pattern="[0-9]*" maxlength="12" class="w-full rounded-xl bg-surface-container-low p-2"></label>
                <?php endforeach; elseif (isset($mapped[$id]) || $item['mode']==='revision'): $field=$mapped[$id]??('r_'.$id); ?>
                    <label> <?= $id==='dependientes-72'?'Número de dependientes (0 a 4)':'Importe elegible en COP' ?>
                        <input name="<?= e($field) ?>" value="<?= e($record[$field]) ?>" inputmode="numeric" pattern="[0-9]*" maxlength="12" class="w-full rounded-xl bg-surface-container-low p-2">
                    </label>
                    <?php if ($item['mode']==='revision'): ?><p class="text-label-sm">Un importe positivo se conserva, pero bloquea el saldo final hasta validar esta regla.</p><?php endif; ?>
                <?php else: ?>
                    <p><?= e(match($id) {
                        'salud-prepagada','dependientes-387'=>'Se calcula desde las celdas mensuales de la sección 2.',
                        'trabajo-25'=>'Resultado automático según ingresos, elección de honorarios y límites.',
                        'pensiones'=>'Pensiones y salud de la sección 2; exención calculada por mes.',
                        'cesantias-laborales'=>'Cesantías y promedio salarial de la sección 3; exención por tabla.',
                        'salud-obligatoria','pension-obligatoria'=>'Incluidos en INCR procedentes de la sección 3; no duplicar.',
                        'inflacionario'=>'Incluido solo si fue verificado en INCR de capital de la sección 3.',
                        'dividendos'=>'Se calcula desde dividendos nacionales 2017+ de la sección 3.',
                        default=>'Ficha de control. No registra una nueva deducción.',
                    }) ?></p>
                <?php endif; ?>
                <a class="text-label-sm text-secondary underline" href="<?= e(url($config,'beneficios').'&buscar='.rawurlencode($item['title'])) ?>" target="_blank" rel="noopener noreferrer">Ver ficha completa</a>
                </td></tr>
            <?php endforeach; ?></tbody>
        </table></div>
    </details><?php endforeach; ?>
</section>
