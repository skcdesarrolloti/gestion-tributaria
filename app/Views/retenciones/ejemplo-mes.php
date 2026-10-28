<?php $example=\App\Services\RetencionEjemplo::build($group,(int)$profile['anio']);if ($example): ?>
<details><summary class="text-secondary underline">? · Ver un ejemplo con mis cifras para entender la cuenta</summary>
<p class="font-semibold">Ejemplo didáctico: no es una validación de tu retención y no se guarda.</p>
<p>Suponemos procedimiento 1, sin deducciones por vivienda, prepagada ni dependientes, con los cupos anuales disponibles y redondeo al peso. Tu empleador puede estar usando datos diferentes; por eso debes confirmar el paso 2.</p>
<p>Ganado $<?= e(number_format($group['valores']['devengado']/100,2,',','.')) ?> − aportes $<?= e(number_format($group['valores']['aportes']/100,2,',','.')) ?> − beneficios del ejemplo $<?= e(number_format($example['beneficios']/100,2,',','.')) ?> = base $<?= e(number_format($example['base']/100,2,',','.')) ?>.</p>
<p>Aplicando la tabla mensual del artículo 383 a esa base, el ejemplo da <strong>$<?= e(number_format($example['esperada']/100,0,',','.')) ?></strong> de retención. En tu nómina aparecen $<?= e(number_format($example['real']/100,0,',','.')) ?>. Esta diferencia hipotética no demuestra un error.</p>
</details><?php endif; ?>
