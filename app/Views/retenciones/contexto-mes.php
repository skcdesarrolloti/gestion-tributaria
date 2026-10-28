<h3 class="text-headline-md font-semibold">1. Esto ya lo sabemos por tu nómina</h3>
<p>La retención es un adelanto de tu impuesto de renta que el empleador descuenta de tus pagos. Aquí revisamos si el descuento del mes coincide con el método y los datos que usó nómina. El impuesto anual se revisa después con todos tus ingresos.</p>
<div class="renta-sheet-scroll" role="region" aria-label="Datos recuperados de nómina" tabindex="0"><table class="renta-sheet-table"><thead><tr><th>Concepto</th><th>COP</th><th>Qué significa</th></tr></thead><tbody>
<?php foreach (['devengado'=>['Total ganado antes de descuentos','Incluye salario y los demás pagos importados del mes.'],
    'aportes'=>['Salud, pensión y solidaridad obligatorias','Se recuperan del archivo; no tienes que digitarlos de nuevo.'],
    'retencion'=>['Retención que te descontaron','Es el valor que vamos a comparar; no es el impuesto anual definitivo.']] as $knownKey=>[$knownLabel,$knownHelp]): ?>
<tr><th scope="row"><?= e($knownLabel) ?></th><td>$<?= e(number_format($group['valores'][$knownKey]/100,2,',','.')) ?></td><td><?= e($knownHelp) ?></td></tr>
<?php endforeach; ?></tbody></table></div>
<details><summary class="text-secondary underline">? · Qué información pedir a quien prepara mi nómina</summary>
<p>Puedes pedirle este detalle:</p>
<blockquote class="border border-surface-container rounded-xl p-3">Para revisar mi retención de <?= e(\App\Services\NominaPreview::MONTHS[$group['mes']-1].' de '.$profile['anio']) ?>, por favor confírmame: procedimiento 1 o 2; porcentaje vigente si es 2; base sobre la que calcularon; beneficios aplicados por vivienda, prepagada y dependientes; beneficios acumulados antes de este mes; y criterio de redondeo. Necesito la liquidación que explica el descuento.</blockquote>
<p>«Depuración» significa pasar del ingreso bruto a la base sobre la que se calcula la retención, restando únicamente los conceptos permitidos y soportados.</p>
</details>
