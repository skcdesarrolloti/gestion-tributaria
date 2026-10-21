<?php $getResult=static function (string $key) use ($result) { $value=$result; foreach (explode('.',$key) as $part) { $value=is_array($value)?($value[$part]??null):null; } return $value; }; ?>
<section id="renta-results" class="bg-surface-container-lowest rounded-2xl border border-surface-container p-4 space-y-space-md"
    data-base-tax="<?= e(($base['resultado']['estado']??'')==='calculado'?$base['resultado']['impuesto_neto']:'') ?>"
    data-base-pay="<?= e(($base['resultado']['estado']??'')==='calculado'?$base['resultado']['saldo_pagar']:'') ?>">
    <h2 class="text-headline-md font-semibold">Resultado y operaciones</h2>
    <button class="rounded-xl bg-inverse-surface text-on-secondary px-4 py-2" type="button" id="print-renta">Imprimir hoja o guardar como PDF</button>
    <p id="renta-status" role="status" aria-live="polite"><?= ($result['estado']??'')==='calculado'?'Estimación calculada; revisa su alcance.':'Cálculo pendiente: completa y confirma los datos.' ?></p>
    <ul id="renta-errors" role="alert"><?php foreach ($result['errores']??[] as $message): ?><li><?= e($message) ?></li><?php endforeach; ?></ul>
    <div class="profile-grid">
    <?php foreach (['impuesto_neto'=>'Impuesto neto estimado','saldo_pagar'=>'Saldo por pagar estimado','saldo_favor'=>'Saldo a favor estimado'] as $key=>$label): ?>
        <p><strong><?= e($label) ?></strong><span class="block text-headline-md" data-result-key="<?= e($key) ?>"><?= $getResult($key)!==null?'$'.number_format($getResult($key),0,',','.'):'Pendiente' ?></span></p>
    <?php endforeach; ?></div>
    <?php if ($base): ?><p>Disminución de impuesto frente a la base: <strong id="renta-tax-saving">Pendiente</strong>. Disminución de saldo por pagar: <strong id="renta-pay-saving">Pendiente</strong>. Valores negativos indican aumento; no incluyen el costo de realizar una acción.</p><?php endif; ?>
    <div class="renta-sheet-scroll"><table class="renta-sheet-table text-body-md">
        <thead><tr><th>Operación</th><th>Resultado COP</th><th>Regla aplicada</th></tr></thead><tbody>
        <?php foreach (['trabajo_neto'=>['Trabajo neto antes de beneficios','Ingresos − INCR − costos de servicios elegidos'],
            'capital_neto'=>['Capital neto antes de beneficios','Capital bruto − INCR − costos propios'],
            'no_laboral_neto'=>['No laboral neto','Bruto − devoluciones − INCR − costos propios'],
            'deducciones.exencion25'=>['Exención del 25% solicitada','Base elegible depurada × 25%, máximo 790 UVT'],
            'deducciones.cesantias_exentas'=>['Cesantías exentas solicitadas','Tabla del promedio salarial · art. 206.4'],
            'deducciones.solicitado'=>['Exentas y deducciones limitadas solicitadas','Suma antes de límite conjunto'],
            'deducciones.limite'=>['Tope conjunto','Menor entre 40% de base art. 336.2 y 1.340 UVT'],
            'beneficios_limitados'=>['Beneficios limitados admitidos','Menor entre solicitado, tope y renta disponible'],
            'deducciones.dependientes72'=>['Dependientes adicionales','72 UVT por dependiente, máximo cuatro; requiere trabajo'],
            'deducciones.compras1'=>['Deducción 1% solicitada','1% compras elegibles, máximo 240 UVT, sin pérdida'],
            'renta_general'=>['Renta general gravable','Renta neta − beneficios admitidos; mínimo cero'],
            'pension_exenta'=>['Pensión exenta','Suma mensual de menor entre pensión neta y 1.000 UVT'],
            'pension_gravada'=>['Pensión gravada','Pensión − salud − exención mensual'],
            'dividendos_base'=>['Dividendos para base progresiva','No gravados + gravados después del impuesto previo'],
            'base_gravable'=>['Base agregada para tarifa','General + pensión gravada + dividendos; referencia a miles'],
            'tarifa.impuesto'=>['Impuesto progresivo art. 241','(Base COP − umbral UVT × UVT) × tarifa + fijo UVT × UVT'],
            'impuesto_dividendos'=>['Impuesto previo sobre dividendos gravados','35% sobre supuesto nacional ordinario 2017+'],
            'descuento_dividendos'=>['Descuento de dividendos','19% del exceso de base de dividendos sobre 1.090 UVT'],
            'descuento_donaciones'=>['Descuento de donaciones admitido','25% donación, límite art. 258 y cupo de impuesto'],
            'retenciones'=>['Retenciones','Pagos previos, no ahorro de impuesto'],
            'anticipo'=>['Anticipo siguiente','25/50/75% base elegida − retenciones; mínimo cero'],
            'patrimonio_liquido'=>['Patrimonio líquido','Patrimonio bruto − deudas; separado del impuesto de renta']] as $key=>[$label,$rule]): ?>
            <tr><th scope="row"><?= e($label) ?></th><td data-result-key="<?= e($key) ?>"><?= $getResult($key)!==null?'$'.number_format($getResult($key),0,',','.'):'Pendiente' ?></td><td><?= e($rule) ?></td></tr>
        <?php endforeach; ?></tbody>
    </table></div>
    <p id="renta-tariff-detail"></p>
    <ul id="renta-warnings"><?php foreach ($result['advertencias']??[] as $message): ?><li><?= e($message) ?></li><?php endforeach; ?></ul>
</section>
