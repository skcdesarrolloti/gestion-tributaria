<section class="bg-surface-container-lowest border border-surface-container rounded-xl p-4 space-y-space-md">
<h2 class="text-headline-md font-semibold">Hipótesis anual · solo nómina importada y salarios futuros</h2>
<p class="bg-primary-container rounded-xl p-4">Saldo completo pendiente: faltan pensión, rendimientos, otros ingresos y beneficios personales. Este ejercicio no estima todavía tu obligación total. Los demás componentes se consideran cero únicamente para comparar esta hipótesis salarial.</p>
<?php if ($result['estado']==='calculado'): ?>
<p>Corte de nómina: <?= e($result['corte']) ?>. Los meses o quincenas faltantes no se completan automáticamente.</p>
<div class="renta-sheet-scroll" role="region" aria-label="Proyección salarial" tabindex="0"><table class="renta-sheet-table"><thead><tr><th>Concepto</th><th>COP estimados</th></tr></thead><tbody>
<?php foreach (['impuesto_neto'=>'Impuesto anual de esta hipótesis salarial','retenido_real'=>'Retención real acumulada de nómina · aporte a 132',
    'retencion_futura'=>'Retención laboral adicional prevista hasta diciembre','anticipo'=>'Anticipo estimado para el año siguiente',
    'saldo_pagar'=>'Faltante de esta hipótesis, después de retenciones y saldos anteriores','reserva'=>'Reserva sugerida por cuota ('.$result['cuotas'].' cuotas)'] as $key=>$label): ?>
<tr><th scope="row"><?= e($label) ?></th><td>$<?= e(number_format($result[$key],0,',','.')) ?></td></tr><?php endforeach; ?>
</tbody></table></div>
<p>El faltante incluye el anticipo; se calcula con la base del impuesto actual. Un resultado de $0 no confirma que tu declaración completa esté cubierta.</p>
<?php else: ?><p role="status"><?= e($result['motivo']) ?></p><?php endif; ?>
<details><summary class="text-secondary underline">? · Reserva, mayor retención y cálculo completo</summary>
<p>Reserva significa dinero que separas para el pago futuro. No es una orden de abono a la DIAN. Puedes solicitar por escrito al pagador una retención superior; conforme al artículo 383, se aplica desde el mes siguiente a la solicitud. Antes de definirla integra todos los ingresos, beneficios y saldos de tu declaración.</p>
<p>Se utiliza la tabla anual del artículo 241 y la exención laboral del 25% con sus topes anuales. No se usa la tabla mensual de retención para estimar el impuesto anual. Las primas ordinarias se incluyen en el ingreso futuro; cesantías, indemnizaciones y otros pagos especiales requieren el simulador completo.</p>
<?php $item=['id'=>'','norm'=>'ET 206.10, 241, 383 y 807'];require __DIR__.'/../beneficios/articles.php'; ?>
</details>
<a class="text-secondary underline" href="<?= e(url($config,'simulador').'&id='.$profile['id']) ?>">Abrir simulador anual con todos los ingresos y beneficios</a>
<?php if (!$readOnly && $payroll): $saved=$settings['reserva']??[];$values=$_SERVER['REQUEST_METHOD']==='POST'?$_POST:$saved; ?>
<form method="post" class="space-y-space-md">
<?php require __DIR__.'/../partials/csrf.php'; ?>
<input type="hidden" name="revision" value="<?= e($revision) ?>"><input type="hidden" name="huella" value="<?= e(\App\Services\NominaReserva::fingerprint($payroll)) ?>">
<input type="hidden" name="parametros" value="<?= e(\App\Services\ParametrosAnuales::fingerprint((int)$profile['anio'])) ?>">
<p>Valores futuros = lo que aún falta por recibir o descontar, incluidas quincenas pendientes de meses ya iniciados. No repitas importes ya importados. Escribe 0 solo cuando corresponda.</p>
<div class="grid md:grid-cols-2 gap-space-md">
<?php foreach (\App\Services\NominaReserva::MONEY as $field=>$label): ?><div><label><?= e($label) ?> (COP)<input name="<?= e($field) ?>" required aria-describedby="ayuda-<?= e($field) ?>" inputmode="numeric" pattern="[0-9]+" value="<?= e($values[$field]??'') ?>" class="w-full rounded-xl bg-surface-container-low p-3"></label><?php require __DIR__.'/ayuda-campo.php'; ?></div><?php endforeach; ?>
<div><label>Declaración que presentarás por este año<select required name="declaraciones" aria-describedby="ayuda-declaraciones" class="w-full rounded-xl bg-surface-container-low p-3"><option value="">Selecciona</option><?php foreach (['1'=>'Primera · anticipo 25%','2'=>'Segunda · anticipo 50%','3'=>'Tercera o posterior · anticipo 75%'] as $value=>$label): ?><option value="<?= e($value) ?>" <?= (string)($values['declaraciones']??'')===(string)$value?'selected':'' ?>><?= e($label) ?></option><?php endforeach; ?></select></label><?php $field='declaraciones';require __DIR__.'/ayuda-campo.php'; ?></div>
<div><label>Número de cuotas para separar la reserva<input name="cuotas" aria-describedby="ayuda-cuotas" type="number" min="1" max="12" required value="<?= e($values['cuotas']??'') ?>" class="w-full rounded-xl bg-surface-container-low p-3"></label><?php $field='cuotas';require __DIR__.'/ayuda-campo.php'; ?></div>
</div>
<label class="block"><input name="hipotesis" type="checkbox" value="1" aria-describedby="ayuda-hipotesis" required> Confirmo los valores futuros y saldos anteriores. Este ejercicio cubre solo salarios ordinarios, sin otros ingresos ni beneficios personales, cesantías o indemnizaciones; revisaré el cálculo completo por separado.</label><?php $field='hipotesis';require __DIR__.'/ayuda-campo.php'; ?>
<button class="rounded-xl bg-inverse-surface text-on-secondary px-4 py-2">Guardar hipótesis y calcular reserva</button>
</form><?php endif; ?>
</section>
