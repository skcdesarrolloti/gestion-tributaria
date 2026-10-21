<?php $parsed=$preview['file']['parsed']; ?>
<section class="bg-surface-container-lowest rounded-xl border border-surface-container p-4 space-y-space-md">
    <h2 class="text-headline-md font-semibold">Datos leídos · revisa antes de aplicar</h2>
    <p>NIT <?= e($parsed['nit'].'-'.$parsed['dv']) ?> · Formulario <?= e($parsed['formulario']) ?> · <?= e($parsed['pages']) ?> página(s)</p>
    <p>Generación del PDF: <?= e($parsed['generado']?:'No identificada') ?> · Trámite que consta: <?= e($parsed['formalizado']?:'No identificado') ?></p>
    <details><summary class="text-secondary underline">Ver comprobaciones y pendientes</summary><ul class="list-disc pl-4"><?php foreach ($preview['plan']['warnings'] as $warning): ?><li><?= e($warning) ?></li><?php endforeach; ?></ul></details>
    <form method="post" action="<?= e($action) ?>" class="space-y-space-md">
        <?php require __DIR__.'/../partials/csrf.php'; ?>
        <input type="hidden" name="preview_token" value="<?= e($preview['token']) ?>">
        <?php foreach ($schemas as $block=>$schema): $items=array_filter($preview['plan']['items'],static fn($item)=>$item['block']===$block); if (!$items) { continue; } ?>
        <details class="border border-surface-container rounded-xl p-4">
            <summary class="font-semibold"><?= e($schema['title']) ?> · <?= count($items) ?> campos/grupos</summary>
            <div class="renta-sheet-scroll"><table class="renta-sheet-table text-body-md">
                <thead><tr><th>Usar</th><th>Campo</th><th>Valor del PDF</th><th>Dato actual del perfil</th></tr></thead>
                <tbody><?php foreach ($items as $key=>$item): ?>
                    <?php $existing=$blocks[$block]['datos']??[];
                    if ($item['kind']==='filas') {
                        $new=implode(', ',array_column($item['value'],'codigo')); $old=implode(', ',array_column($existing['filas']??[],'codigo'));
                    } else {
                        $new=$schema['fields'][$item['field']]['options'][$item['value']]??$item['value'];
                        $oldValue=$existing['campos'][$item['field']]??''; $old=$schema['fields'][$item['field']]['options'][$oldValue]??$oldValue;
                    } ?>
                    <tr><td><input type="checkbox" name="seleccion[]" value="<?= e($key) ?>" checked aria-label="<?= e('Importar '.$item['label']) ?>"></td><th scope="row"><?= e($item['label']) ?></th><td class="taxpayer-name"><?= e($new) ?></td><td class="taxpayer-name"><?= e($old!==''?$old:'Sin diligenciar') ?></td></tr>
                <?php endforeach; ?></tbody>
            </table></div>
        </details>
        <?php endforeach; ?>
        <label class="block"><input type="checkbox" name="reemplazar" value="1"> También reemplazar los campos y grupos seleccionados que ya tienen un dato diferente.</label>
        <p class="text-body-md text-secondary">Sin marcar esta opción, se completan únicamente los campos vacíos y se conservan los datos diferentes. Los bloques modificados quedan en borrador para su revisión; no se crean condiciones fiscales ni se cambia el impuesto.</p>
        <div class="flex items-center gap-space-md flex-wrap">
            <button type="submit" name="accion" value="aplicar" class="rounded-xl bg-inverse-surface text-on-secondary px-4 py-2">Aplicar datos seleccionados al perfil</button>
            <button type="submit" name="accion" value="cancelar" class="text-secondary underline">Descartar esta lectura</button>
        </div>
    </form>
</section>
