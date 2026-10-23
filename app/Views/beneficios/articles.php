<details class="space-y-space-sm norma-consulta">
    <summary class="text-secondary underline">Consultar artículos aquí</summary>
    <p class="text-label-sm text-secondary">Escoge un artículo para leerlo sin salir de esta ficha. Se muestra completo; el fundamento de la ficha indica el numeral o parágrafo pertinente.</p>
    <?php foreach (\App\Services\NormaCatalog::references($item) as $reference): ?>
    <details class="border border-surface-container rounded-xl p-2" data-norma-url="<?= e(url($config,'beneficios/articulo').'&articulo='.$reference['id']) ?>">
        <summary class="font-semibold text-secondary"><?= e($reference['label']) ?></summary>
        <div data-norma-body aria-live="polite" class="space-y-space-sm mt-2"><p>El texto se carga al abrir el artículo.</p></div>
        <noscript>Para leer aquí el artículo, habilita JavaScript en el navegador.</noscript>
    </details>
    <?php endforeach; ?>
</details>
