<?php $reading=\App\Services\ExtractoData::reading($selected);$search=mb_substr(trim(input($_GET,'buscar')),0,100);
$labels=['pension'=>'Pensión recibida · neto bancario','rendimientos'=>'Rendimiento abonado','gmf'=>'4×1000 debitado',
    'gmf_reintegro'=>'Reintegro de 4×1000','retenciones'=>'Retención bancaria · revisar concepto','iva'=>'IVA bancario',
    'otros_abonos'=>'Otro abono · por clasificar','otros_debitos'=>'Otro débito · por clasificar']; ?>
<section id="detalle" class="bg-surface-container-lowest border border-surface-container rounded-xl p-4 space-y-space-md">
<a class="text-secondary underline" href="<?= e(($extractBrowseUrl??url($config,'extractos').$extractQuery).'#'.($extractReturnAnchor??'meses')) ?>">← Volver a los meses guardados</a>
<h2 class="text-headline-md font-semibold">Movimientos del extracto · <?= e(\App\Services\ExtractoData::MONTHS[(int)$selected['mes']].' '.$year) ?></h2>
<?php if (($reading['estado']??'')!=='leido'): ?><p><?= e($reading['motivo']??'Lectura pendiente.') ?></p>
<?php else: ?>
<p><?= (int)$reading['paginas'] ?> páginas leídas. Cada saldo y los totales concilian con el resumen del banco. Los otros abonos y débitos requieren confirmar su finalidad antes de tratarlos como ingresos, costos o deducciones.</p>
<form method="get" class="scenario-settings-grid">
<input type="hidden" name="route" value="<?= e($extractRoute??'extractos') ?>"><input type="hidden" name="contribuyente" value="<?= (int)$taxpayer['id'] ?>"><input type="hidden" name="anio" value="<?= e($year) ?>"><input type="hidden" name="documento" value="<?= (int)$selected['id'] ?>">
<?php if (($extractRoute??'')==='registro'): foreach (($extractState??['componente'=>'ingresos','cedula'=>'pensiones']) as $key=>$value): if (in_array($key,['componente','cedula','renta','seccion'],true)): ?><input type="hidden" name="<?= e($key) ?>" value="<?= e($value) ?>"><?php endif; endforeach; endif; ?>
<label>Buscar concepto o comercio en este PDF <input name="buscar" value="<?= e($search) ?>" maxlength="100" class="w-full rounded-xl bg-surface-container-low p-3"></label><button class="rounded-xl bg-inverse-surface text-on-secondary px-4 py-2">Consultar movimientos</button>
</form>
<details <?= $search!==''?'open':'' ?>><summary class="font-semibold">Ver detalle y categorías propuestas (<?= count($reading['movimientos']) ?> movimientos)</summary>
<div class="renta-sheet-scroll" role="region" aria-label="Movimientos bancarios" tabindex="0"><table class="renta-sheet-table">
<thead><tr><th scope="col">Fecha valor / operación</th><th scope="col">Concepto del banco</th><th scope="col">Cargo</th><th scope="col">Abono</th><th scope="col">Categoría propuesta</th><th scope="col">Página</th></tr></thead>
<tbody><?php $shown=0;foreach ($reading['movimientos'] as $m): if ($search!=='' && !str_contains(mb_strtolower($m['descripcion']),mb_strtolower($search))) { continue; }$shown++; ?>
<tr><td><?= e($m['fecha']) ?><br><?= e($m['operacion']) ?></td><th scope="row"><?= e($m['descripcion']) ?></th><td><?= e(\App\Services\ExtractoData::money($m['cargo'])) ?></td><td><?= e(\App\Services\ExtractoData::money($m['abono'])) ?></td><td><?= e($labels[$m['categoria']]??'Por clasificar') ?></td><td><?= (int)$m['pagina'] ?></td></tr>
<?php endforeach; ?></tbody></table></div><p><?= $shown ?> movimientos encontrados.</p></details>
<p>Retenciones reportadas por el banco en este extracto: <?= e(\App\Services\ExtractoData::money($reading['resumen']['retenciones'])) ?>. Verifica su concepto en el certificado; no reemplaza las retenciones del pagador de la pensión.</p>
<?php endif; ?></section>
