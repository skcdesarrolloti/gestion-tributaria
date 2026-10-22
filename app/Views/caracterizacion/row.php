<details class="bg-surface-container-lowest rounded-xl border border-surface-container p-4" data-profile-row <?= $rowIndex==='__INDEX__' || $errors?'open':'' ?>>
    <summary class="font-semibold">Renglón <?= $rowIndex==='__INDEX__'?'nuevo':e((int)$rowIndex+1) ?> · <?= e($row['nombre']??$row['descripcion']??$row['codigo']??'Detalle') ?></summary>
    <input type="hidden" name="<?= e('filas['.$rowIndex.'][_origen]') ?>" value="<?= e($row['_origen']??'') ?>">
    <div class="scenario-settings-grid mt-2">
        <?php foreach ($schema['rows'] as $key=>$definition): $name='filas['.$rowIndex.']['.$key.']'; $inputId='fila-'.$rowIndex.'-'.$key; $value=$row[$key]??''; require __DIR__.'/field.php'; endforeach; ?>
    </div>
    <button type="button" data-remove-profile-row class="text-secondary underline mt-2">Quitar este renglón</button>
</details>
