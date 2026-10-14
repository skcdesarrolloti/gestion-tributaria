<?php $total=$summary['total']; ?>
<section class="bg-surface-container-lowest rounded-2xl border border-surface-container p-space-xl space-y-space-md">
    <h2 class="text-headline-md font-semibold">Cómo van los ingresos registrados</h2>
    <p class="text-body-md text-secondary">Proyectado adicional: solo lo que falta por ocurrir. El cierre suma lo real y esa proyección; no estima automáticamente meses faltantes.</p>
    <div class="profile-grid">
        <?php foreach (['real'=>'Ya ocurrió','proyectado'=>'Esperado adicional','cierre'=>'Cierre proyectado parcial'] as $state=>$label): ?>
            <?php
            $income=$state==='cierre'?$total['real']['ingreso']+$total['proyectado']['ingreso']:$total[$state]['ingreso'];
            $reduction=$state==='cierre'?$total['real']['reduccion']+$total['proyectado']['reduccion']:$total[$state]['reduccion'];
            ?>
            <article class="rounded-xl border border-surface-container p-4 space-y-space-sm">
                <h3 class="text-headline-sm font-semibold"><?= e($label) ?></h3>
                <p>Ingresos brutos: <strong>$<?= e(number_format($income,0,',','.')) ?></strong></p>
                <p>Partidas que se restan: $<?= e(number_format($reduction,0,',','.')) ?></p>
                <p>Resultado preliminar: $<?= e(number_format($income-$reduction,0,',','.')) ?></p>
                <?php if ($income-$reduction<0): ?><p role="status">Revisar: las partidas que se restan superan los ingresos. No acredita una pérdida fiscal.</p><?php endif; ?>
            </article>
        <?php endforeach; ?>
    </div>
    <p class="text-body-md text-secondary">El resultado preliminar resta únicamente las partidas del catálogo actual (ingresos no constitutivos, devoluciones y costos según la casilla). No es la base gravable final ni el impuesto. Cero registrado no demuestra ausencia de ingresos.</p>
</section>
