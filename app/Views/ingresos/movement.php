<?php
$title=$id===null?'Registrar movimiento':'Editar movimiento';
$action=url($config,'ingresos/movimiento').'&id='.$profile['id'].($id===null?'':'&movimiento='.$id);
require __DIR__.'/../partials/head.php';
?>
<body class="bg-background min-h-screen font-body-md text-on-surface antialiased">
<?php require __DIR__.'/../partials/header.php'; ?>
<main class="portal-container space-y-space-lg py-8">
    <section class="bg-inverse-surface text-on-secondary rounded-2xl p-space-xl">
        <h1 class="text-headline-lg font-semibold"><?= e($title) ?></h1>
        <p class="text-body-md mt-2"><?= e($profile['nombre']) ?> · <?= e($profile['anio']) ?> · Formulario <?= e($profile['formulario']) ?></p>
    </section>
    <section class="bg-surface-container-lowest rounded-2xl border border-surface-container p-space-xl space-y-space-md">
        <?php require __DIR__.'/errors.php'; ?>
        <p class="text-body-md text-secondary">Cada movimiento se asigna una sola vez. Para registrar una reducción real, consulta antes su ficha y documenta la norma particular. Los totales calculados no se capturan aquí.</p>
        <form method="post" action="<?= e($action) ?>" class="space-y-space-md">
            <?php require __DIR__.'/../partials/csrf.php'; ?>
            <label class="block text-body-md" for="concepto_id">Casilla y concepto *</label>
            <select class="w-full px-3 py-2.5 bg-surface-container-low rounded-xl" id="concepto_id" name="concepto_id" required>
                <option value="">Selecciona una casilla</option>
                <?php foreach ($concepts as $concept): ?>
                    <?php if (!\App\Services\IngresoData::allowed($concept,$profile)) { continue; } ?>
                    <option value="<?= e($concept['id']) ?>" <?= (string)$record['concepto_id']===(string)$concept['id']?'selected':'' ?>><?= e($concept['casilla'].' · '.$concept['bloque'].' · '.$concept['nombre']) ?></option>
                <?php endforeach; ?>
            </select>
            <p class="text-body-md text-secondary">Las casillas 43–45 solo se habilitan con la opción de costos del perfil. Si el tratamiento está pendiente, revisa la clasificación antes de registrar servicios personales en otro bloque.</p>
            <div class="profile-grid">
                <?php foreach ([
                    'fecha'=>['Fecha de reconocimiento fiscal','date',10],
                    'descripcion'=>['Descripción particular (p. ej., arriendo de septiembre)','text',300],
                    'tercero'=>['Pagador o tercero relacionado','text',150],
                    'pais'=>['País de origen','text',80],
                    'moneda'=>['Moneda original (COP, USD, EUR…)','text',3],
                    'valor_original'=>['Valor original (sin separadores; punto decimal)','text',17],
                    'valor_cop'=>['Valor fiscal en COP (pesos enteros, sin separadores)','text',12],
                ] as $field=>[$label,$type,$length]): ?>
                    <div>
                        <label class="block text-body-md mb-1" for="<?= e($field) ?>"><?= e($label) ?> *</label>
                        <input class="w-full px-3 py-2.5 bg-surface-container-low rounded-xl" id="<?= e($field) ?>" name="<?= e($field) ?>" type="<?= e($type) ?>" maxlength="<?= e($length) ?>" value="<?= e($record[$field]) ?>" required>
                    </div>
                <?php endforeach; ?>
                <div>
                    <label class="block text-body-md mb-1" for="estado">Estado *</label>
                    <select class="w-full px-3 py-2.5 bg-surface-container-low rounded-xl" id="estado" name="estado" required>
                        <option value="">Selecciona una opción</option>
                        <?php foreach (['real'=>'Real / ocurrido','proyectado'=>'Proyectado adicional'] as $key=>$label): ?><option value="<?= e($key) ?>" <?= $record['estado']===$key?'selected':'' ?>><?= e($label) ?></option><?php endforeach; ?>
                    </select>
                </div>
            </div>
            <p class="text-body-md text-secondary">En COP ambos valores deben coincidir. En moneda extranjera registra el valor fiscal revisado y justifica su conversión; no se aplica una TRM automática.</p>
            <?php foreach (['conversion'=>'Conversión: tasa, fecha y evidencia (obligatoria si no es COP)','soporte'=>'Referencia del soporte (obligatoria en movimientos reales)','justificacion'=>'Norma particular, condiciones y justificación (obligatoria en partidas que se restan)'] as $field=>$label): ?>
                <div>
                    <label class="block text-body-md mb-1" for="<?= e($field) ?>"><?= e($label) ?></label>
                    <textarea class="w-full px-3 py-2.5 bg-surface-container-low rounded-xl" id="<?= e($field) ?>" name="<?= e($field) ?>" maxlength="500" rows="2"><?= e($record[$field]) ?></textarea>
                </div>
            <?php endforeach; ?>
            <p class="text-body-md text-secondary">Por ahora se guarda la referencia, no se adjuntan archivos. No escribas contraseñas bancarias ni enlaces públicos a documentos privados.</p>
            <button class="rounded-xl bg-inverse-surface text-on-secondary px-4 py-2" type="submit">Guardar movimiento</button>
        </form>
        <a class="text-secondary underline" href="<?= e(url($config,'ingresos/detalle').'&id='.$profile['id']) ?>">Volver a casillas y fichas</a>
    </section>
</main>
<?php require __DIR__.'/../partials/footer.php'; ?>
