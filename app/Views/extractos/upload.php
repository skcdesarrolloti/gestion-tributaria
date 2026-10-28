<details class="bg-surface-container-lowest border border-surface-container rounded-xl p-4" <?= !$documents||$errors?'open':'' ?>>
<summary class="text-headline-md font-semibold">Subir extracto bancario en PDF</summary>
<p><?= e($taxpayer['nombre']) ?> · <?= e($year) ?>. Sube el PDF del banco; el mes se obtiene del período impreso en el documento.</p>
<form method="post" enctype="multipart/form-data" class="space-y-space-md" data-extracto-form data-documento="<?= e($taxpayer['documento_numero']) ?>" data-worker="<?= e(asset($config,'js/extracto-worker.js')) ?>" action="<?= e(url($config,'extractos').$extractQuery) ?>">
<?php require __DIR__.'/../partials/csrf.php'; ?>
<div class="scenario-settings-grid">
<label>Mes del extracto <select name="mes" required class="w-full rounded-xl bg-surface-container-low p-3"><option value="auto" <?= $form['mes']==='auto'?'selected':'' ?>>Detectar mes desde el PDF</option><?php foreach (\App\Services\ExtractoData::MONTHS as $n=>$month): ?><option value="<?= $n ?>" <?= (int)$form['mes']===$n?'selected':'' ?>><?= e($month) ?></option><?php endforeach; ?></select></label>
<label>Banco <input name="banco" value="<?= e($form['banco']) ?>" required maxlength="80" class="w-full rounded-xl bg-surface-container-low p-3"></label>
<label>Últimos cuatro dígitos de la cuenta <input name="cuenta" value="<?= e($form['cuenta']) ?>" required pattern="[0-9]{4}" maxlength="4" inputmode="numeric" class="w-full rounded-xl bg-surface-container-low p-3"></label>
</div>
<label class="block">PDF completo del mes · máximo 10 MB <input type="file" name="extracto_pdf" accept="application/pdf,.pdf" required class="block w-full"></label>
<label class="block"><input type="checkbox" data-pdf-unlock checked> Abrir PDF protegido antes de detectar el mes</label>
<label class="block">Contraseña del PDF (opcional si corresponde a tu documento)
<input type="password" data-pdf-password autocomplete="off" maxlength="128" class="w-full rounded-xl bg-surface-container-low p-3">
</label>
<p class="text-secondary text-body-md">Si dejas la clave vacía, se intentará con el número de documento de esta ficha, sin puntos ni dígito de verificación. La clave se usa en tu navegador y no se envía ni guarda. Se conserva el original y una copia privada de lectura para consultar el PDF sin volver a abrirlo con clave.</p>
<input type="file" name="extracto_lectura" hidden accept="application/pdf">
<p role="status" aria-live="polite" data-pdf-status></p>
<label class="block"><input type="checkbox" name="titular_confirmado" value="1" required> Revisé que el titular del PDF es <?= e($taxpayer['nombre']) ?> y que corresponde al año y cuenta seleccionados; el mes será el impreso en el PDF o el elegido manualmente.</label>
<details><summary class="text-secondary underline">? · Qué se hará con este soporte</summary>
<p>El PDF queda privado y se consulta desde Ingresos y desde Costos y gastos. No necesitas subir una copia por cada uso. El mismo archivo, aunque cambies su nombre, no se vuelve a guardar para este contribuyente.</p>
<p>El formato mensual BBVA Colombia reconocido se lee completo y se comprueban los saldos de cada movimiento contra el resumen. Los escaneos y otros diseños quedan pendientes, sin inventar importes. El banco debe indicarse como BBVA para este formato.</p>
<p>Una consignación de pensión puede ser neta de descuentos: no reemplaza el comprobante del pagador para identificar pensión bruta, salud y retenciones. Las transferencias entre cuentas propias, préstamos y devoluciones requieren clasificación; no se suman automáticamente como ingresos.</p>
<p>El 4×1000 y sus reintegros se muestran separados. No todo gasto pagado desde el banco es deducible: cada partida necesita su clasificación y soporte. Los acumulados bancarios aún no se suman a casillas DIAN, para evitar repetir la nómina o registrar una pensión neta como bruta.</p>
</details>
<button type="submit" class="rounded-xl bg-inverse-surface text-on-secondary px-4 py-2">Guardar extracto del mes</button>
</form></details>
