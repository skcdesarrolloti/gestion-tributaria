<details id="subir-extracto" class="bg-surface-container-lowest border border-surface-container rounded-xl p-4" <?= !$documents||$errors||input($_GET,'subir')==='1'?'open':'' ?>>
<summary class="text-headline-md font-semibold">Subir extracto bancario en PDF</summary>
<p><?= e($taxpayer['nombre']) ?> · <?= e($year) ?>. Cuentas y créditos personales en el mismo selector. No necesitas volver a subirlos en ingresos, costos ni préstamos.</p>
<form method="post" enctype="multipart/form-data" class="space-y-space-md" data-extracto-form data-documento="<?= e($taxpayer['documento_numero']) ?>" data-worker="<?= e(asset($config,'js/extracto-worker.js')) ?>" data-batch-url="<?= e(url($config,'extractos/lote').$extractQuery) ?>" action="<?= e(url($config,'extractos').$extractQuery) ?>">
<?php require __DIR__.'/../partials/csrf.php'; ?>
<div class="scenario-settings-grid">
<label>Mes del extracto <select name="mes" required class="w-full rounded-xl bg-surface-container-low p-3"><option value="auto" <?= $form['mes']==='auto'?'selected':'' ?>>Detectar mes desde el PDF</option><?php foreach (\App\Services\ExtractoData::MONTHS as $n=>$month): ?><option value="<?= $n ?>" <?= (int)$form['mes']===$n?'selected':'' ?>><?= e($month) ?></option><?php endforeach; ?></select></label>
<label>Banco (si no se detecta) <input name="banco" value="<?= e($form['banco']) ?>" maxlength="80" class="w-full rounded-xl bg-surface-container-low p-3"></label>
<label>Últimos cuatro dígitos (si no se detectan) <input name="cuenta" value="<?= e($form['cuenta']) ?>" pattern="[0-9]{4}" maxlength="4" inputmode="numeric" autocomplete="off" class="w-full rounded-xl bg-surface-container-low p-3"></label>
</div>
<label class="block">PDF bancarios mensuales o trimestrales · puedes seleccionar varios (hasta 50; 10 MB cada uno)<input type="file" name="extracto_pdf[]" data-bank-files multiple accept="application/pdf,.pdf" required class="block w-full"></label>
<label class="block"><input type="checkbox" data-pdf-unlock checked> Preparar lectura automática del PDF, con o sin contraseña</label>
<label class="block"><input type="checkbox" data-pdf-custom> Usar otra contraseña del PDF (distinta al documento de esta ficha)</label>
<label class="block">Contraseña del PDF (opcional si corresponde a tu documento)
<input type="password" data-pdf-password autocomplete="new-password" disabled maxlength="128" data-lpignore="true" class="w-full rounded-xl bg-surface-container-low p-3">
</label>
<p class="text-secondary text-body-md">No se necesita firma digital. Si el PDF abre sin contraseña, no tienes que escribir ninguna. Si está protegido, se intentará con el documento de esta ficha, sin puntos ni dígito de verificación; marca «Usar otra contraseña» solo si el banco usa una diferente. La clave se usa en tu navegador y no se envía ni guarda. Se conservan el original y una copia privada de lectura.</p>
<input type="file" name="extracto_lectura" hidden accept="application/pdf">
<p role="status" aria-live="polite" data-pdf-status></p>
<label class="block"><input type="checkbox" name="titular_confirmado" value="1" <?= input($_POST,'titular_confirmado')==='1'?'checked':'' ?>> Ya revisé el titular, el año y la cuenta del PDF (opcional).</label>
<p class="text-secondary text-body-md">Puedes guardar sin marcarla: quedará pendiente tu revisión visual. El sistema seguirá comprobando los datos que pueda leer contra <?= e($taxpayer['nombre']) ?>, el año y la cuenta elegidos. Esta casilla no abre ni firma el documento.</p>
<details><summary class="text-secondary underline">? · Qué se hará con este soporte</summary>
<p>El PDF queda privado y se consulta desde Ingresos y desde Costos y gastos. No necesitas subir una copia por cada uso. El mismo archivo, aunque cambies su nombre, no se vuelve a guardar para este contribuyente.</p>
<p>Se reconocen los formatos disponibles de BBVA, Caja Social Cuentamiga y Bancolombia Ahorros. Se concilian los saldos de cada movimiento y el resumen. Un PDF trimestral se distribuye por fechas reales en sus meses, sin dividir totales entre tres. Los escaneos y otros diseños quedan pendientes, sin inventar importes.</p>
<p>Una consignación de pensión puede ser neta de descuentos: no reemplaza el comprobante del pagador para identificar pensión bruta, salud y retenciones. Las transferencias entre cuentas propias, préstamos y devoluciones requieren clasificación; no se suman automáticamente como ingresos.</p>
<p>El 4×1000 y sus reintegros se muestran separados. No todo gasto pagado desde el banco es deducible: cada partida necesita su clasificación y soporte. Los acumulados bancarios aún no se suman a casillas DIAN, para evitar repetir la nómina o registrar una pensión neta como bruta.</p>
</details>
<button type="submit" class="rounded-xl bg-inverse-surface text-on-secondary px-4 py-2">Subir seleccionados</button>
<ul data-bank-results class="space-y-space-sm" aria-live="polite"></ul>
</form></details>
