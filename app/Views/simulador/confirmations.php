<section class="bg-primary-container rounded-2xl p-4 space-y-space-sm">
    <h2 class="text-headline-sm font-semibold">5 · Confirmaciones y alcance</h2>
    <?php foreach (['ceros_confirmados'=>'Confirmo que todas las celdas monetarias vacías son cero conocido. Si falta un dato, no marco esta confirmación.',
        'elegibilidad'=>'Confirmo que los costos, aportes, compras y deducciones ingresados son elegibles, tienen soporte y no están duplicados.',
        'ganancias_ocasionales'=>'Hay ganancias ocasionales, herencias, donaciones recibidas o venta de activos por revisar.',
        'dividendos_otro_regimen'=>'Hay dividendos extranjeros, de utilidades anteriores a 2017 o con tratamiento particular.',
        'casos_especiales'=>'Hay pensión retroactiva/sustitutiva, rentas extranjeras, ECE, compensaciones, exenciones especiales u otro caso fuera del alcance.',
        'beneficios_revision'=>'Hay un beneficio que requiere validación particular y aún no he podido incorporarlo.'] as $key=>$text): ?>
        <label class="block"><input type="checkbox" name="<?= e($key) ?>" value="1" <?= $record[$key]==='1'?'checked':'' ?>> <?= e($text) ?></label>
    <?php endforeach; ?>
    <button class="rounded-xl bg-inverse-surface text-on-secondary px-4 py-2" type="button" id="recalculate-renta">Recalcular ahora</button>
    <p>El cálculo se actualiza al modificar datos; solo se guarda al pulsar Guardar nueva versión. Puedes guardar borradores pendientes.</p>
</section>
