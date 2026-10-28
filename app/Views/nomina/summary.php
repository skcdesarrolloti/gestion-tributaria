<?php if ($payroll): ?>
<h3 class="font-semibold">Resumen mensual y acumulado de nómina</h3>
<p>Revisa «Devengado del mes» y «Devengado acumulado». Los descuentos reducen el neto recibido; no todos reducen la renta.</p>
<div class="renta-sheet-scroll" role="region" aria-label="Resumen mensual y acumulado de nómina" tabindex="0">
<table class="renta-sheet-table"><thead><tr><th>Mes</th><th>Quincenas</th><th>Devengado del mes · 32</th><th>Aportes del mes · 33</th><th>Retención del mes · 132</th><th>Neto del mes</th><th>Devengado acumulado</th><th>Control retención</th></tr></thead><tbody>
<?php $running=0;$retention=0;foreach ($payMonths as $i=>$m): if (!$m) { continue; }$running+=$m['devengado'];$retention+=$m['retencion']; ?>
<tr><th scope="row"><?= e(\App\Services\NominaPreview::MONTHS[$i-1]) ?></th><td><?= e($m['registros']) ?></td>
<?php $monthChecks=$retentionControl['meses'][$i]??[];foreach ([$m['devengado'],$m['aportes'],$m['retencion'],$m['neto'],$running] as $value): ?><td>$<?= e(number_format($value/100,2,',','.')) ?></td><?php endforeach; ?><td><?php require __DIR__.'/control-retencion.php'; ?></td></tr>
<tr><td colspan="8"><?php require __DIR__.'/explicacion-retencion.php'; ?></td></tr>
<?php endforeach; ?></tbody></table></div>
<p>Solo aparecen meses con nómina importada. Una quincena no equivale a un mes completo.</p>
<h3 class="font-semibold">Casillas DIAN · acumulado real del año</h3>
<div class="renta-sheet-scroll" role="region" aria-label="Casillas DIAN acumuladas de trabajo" tabindex="0"><table class="renta-sheet-table"><thead><tr><th>Campo exacto que debes revisar</th><th>Valor acumulado</th></tr></thead><tbody>
<?php foreach ([32=>'Ingresos brutos de rentas de trabajo',33=>'Ingresos no constitutivos de renta',34=>'Renta líquida de trabajo antes de beneficios'] as $code=>$label): ?>
<tr><th scope="row"><?= e($code.' · '.$label) ?></th><td>$<?= e(number_format($totals[$code]['real']??0,0,',','.')) ?></td></tr>
<?php endforeach; ?><tr><th scope="row">132 · Retenciones laborales aportadas por esta nómina</th><td>$<?= e(number_format($retention/100,2,',','.')) ?></td></tr>
</tbody></table></div>
<p>32 y 33 incluyen las capturas reales de trabajo del año; 34 = 32 − 33. Los movimientos se redondean a pesos por quincena, por eso pueden diferir unos pesos del detalle con centavos. 132 muestra aquí solo las retenciones de nómina, sin incluir otras retenciones del contribuyente. No es todavía el impuesto final.</p>
<?php endif; ?>
