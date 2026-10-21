<section class="bg-surface-container-lowest rounded-2xl border border-surface-container p-4 space-y-space-md">
    <h2 class="text-headline-md font-semibold">1 · Escenario y condiciones</h2>
    <div class="profile-grid">
        <label>Nombre del escenario<input name="nombre" value="<?= e($record['nombre']) ?>" maxlength="100" placeholder="Ejemplo: situación actual" class="mt-2 w-full px-space-md py-space-md rounded-xl bg-surface-container-low"></label>
        <?php foreach (['pensionado'=>['¿Recibe pensión?',['si'=>'Sí','no'=>'No']],
            'servicios'=>['Tratamiento de honorarios',['costos'=>'Costos procedentes','exencion'=>'Exención del 25%','no_aplica'=>'No recibo honorarios']],
            'declaraciones'=>['Año de declaración para anticipo',['1'=>'Primera · 25%','2'=>'Segunda · 50%','3'=>'Tercera o posterior · 75%']],
            'metodo_anticipo'=>['Base del anticipo',['actual'=>'Impuesto neto de este año','promedio'=>'Promedio de este año y el anterior']]] as $key=>[$label,$options]): ?>
            <label><?= e($label) ?><select name="<?= e($key) ?>" class="mt-2 w-full px-space-md py-space-md rounded-xl bg-surface-container-low"><option value="">Selecciona</option>
                <?php foreach ($options as $value=>$text): ?><option value="<?= e($value) ?>" <?= $record[$key]===(string)$value?'selected':'' ?>><?= e($text) ?></option><?php endforeach; ?>
            </select></label>
        <?php endforeach; ?>
    </div>
    <label class="block">Hipótesis, documentos y datos por confirmar<textarea name="notas" maxlength="1500" class="mt-2 w-full px-space-md py-space-md rounded-xl bg-surface-container-low"><?= e($record['notas']) ?></textarea></label>
</section>
