<details><summary class="text-secondary underline">Revisar soportes de retención por mes y empleador</summary>
<p>«OK» confirma el cálculo ordinario con los datos y soportes revisados. Procedimiento 2: comprueba la aplicación del porcentaje certificado, no su cálculo histórico. Una nómina corregida requiere una nueva revisión. Las diferencias de dos empleadores se muestran separadas y no se compensan.</p>
<?php foreach (\App\Services\RetencionLaboral::groups($payroll) as $group): ?>
<p><a class="text-secondary underline" href="<?= e(url($config,'retenciones').'&id='.$profile['id'].'&mes='.$group['mes'].'&empleador='.rawurlencode($group['documento'])) ?>">
<?= e(\App\Services\NominaPreview::MONTHS[$group['mes']-1].' · '.$group['empleador']) ?></a></p>
<?php endforeach; ?></details>
<a class="inline-block rounded-xl bg-inverse-surface text-on-secondary px-4 py-2" href="<?= e(url($config,'retenciones').'&id='.$profile['id'].'&vista=reserva') ?>">Ver reserva estimada al corte de nómina</a>
<p class="text-secondary">La reserva inicial es una hipótesis salarial. El saldo completo requiere integrar pensión y demás ingresos, beneficios y saldos anteriores.</p>
