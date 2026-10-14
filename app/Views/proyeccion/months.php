<?php $names=['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre']; ?>
<section class="bg-surface-container-lowest rounded-2xl border border-surface-container p-space-xl space-y-space-md">
    <h2 class="text-headline-md font-semibold">Seguimiento mensual de ingresos brutos</h2>
    <p class="text-body-md text-secondary">Los registros guardados alimentan este resumen. Revisa las reducciones en las casillas; esta tabla muestra solo ingresos brutos.</p>
    <?php foreach ($summary['meses'] as $month=>$values): ?>
        <article class="rounded-xl border border-surface-container p-4 space-y-space-sm">
            <h3 class="font-semibold"><?= e($names[$month-1]) ?> · <?= $values['registros']===0?'Sin registros':e($values['registros']).' registros de partidas' ?></h3>
            <div class="profile-grid text-body-md">
                <p>Real: $<?= e(number_format($values['real']['ingreso'],0,',','.')) ?></p>
                <p>Esperado adicional: $<?= e(number_format($values['proyectado']['ingreso'],0,',','.')) ?></p>
                <p>Total parcial: $<?= e(number_format($values['real']['ingreso']+$values['proyectado']['ingreso'],0,',','.')) ?></p>
            </div>
        </article>
    <?php endforeach; ?>
</section>
