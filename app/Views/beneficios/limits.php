<section class="bg-surface-container-lowest rounded-2xl border border-surface-container p-4 space-y-space-md">
    <h2 class="text-headline-sm font-semibold">Cómo se relacionan los límites · <?= e($year) ?></h2>
    <p class="text-body-md">UVT del año: <strong>$<?= number_format($parameters['uvt'],0,',','.') ?></strong>. <a class="text-secondary underline" href="<?= e($parameters['source']) ?>" target="_blank" rel="noopener noreferrer">Resolución oficial</a>. Valores siguientes: conversión directa de referencia; no son cupos aprobados ni cifras redondeadas de una declaración.</p>
    <div class="profile-grid">
        <p class="text-body-md"><strong>Costos y gastos:</strong> reducen la renta de su actividad si proceden; no integran por esa razón el cupo general de deducciones personales. En servicios independientes se compara su uso con la exención del 25%.</p>
        <p class="text-body-md"><strong>Deducciones y exentas limitadas:</strong> en conjunto, menor entre 40% de la base del art. 336.2 y 1.340 UVT ($<?= number_format(1340*$parameters['uvt'],0,',','.') ?>). Los topes de cada beneficio se revisan además de este límite.</p>
        <p class="text-body-md"><strong>Excepciones:</strong> 72 UVT por dependiente, máximo cuatro, y 1% de compras elegibles hasta 240 UVT tienen reglas adicionales al cupo general. Las pensiones tienen depuración propia. Los descuentos reducen el impuesto, con sus límites particulares.</p>
    </div>
    <details><summary class="text-secondary underline">Ver topes de referencia en pesos</summary>
        <ul class="space-y-space-sm mt-2 text-body-md">
        <?php foreach (['Vivienda · anual'=>1200,'Salud prepagada · mensual'=>16,'Dependientes art. 387 · mensual total'=>32,'Dependiente adicional · anual por persona'=>72,'Compras 1% · deducción máxima anual'=>240,'Exención 25% de trabajo · anual'=>790] as $label=>$units): ?>
            <li><?= e($label) ?>: <?= e($units) ?> UVT = $<?= number_format($units*$parameters['uvt'],0,',','.') ?>.</li>
        <?php endforeach; ?>
        </ul>
    </details>
    <p class="text-body-md text-secondary">ET 107, 206, 330, 332, 336 y 337. Un mismo pago no puede utilizarse en varios beneficios incompatibles. El 60% del art. 336-1 es un umbral indicativo de control para trabajo con costos, no una deducción automática sin soportes.</p>
</section>
