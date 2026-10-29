<?php if ($tableConcepts): ?>
<p class="text-secondary text-body-md">En pantallas pequeñas, desliza la tabla hacia los lados para ver valores y «Registrar valor».</p>
<div class="renta-sheet-scroll" role="region" aria-label="<?= e($tableTitle) ?>" tabindex="0">
<table class="renta-sheet-table">
<caption class="font-semibold"><?= e($tableTitle) ?></caption>
<thead><tr><th scope="col">Casilla y concepto DIAN</th><th scope="col">Real registrado (COP)</th><th scope="col">Proyectado adicional (COP)</th><th scope="col">Registro</th></tr></thead>
<tbody><?php foreach ($tableConcepts as $concept): $values=$totals[(int)$concept['casilla']]; ?>
<tr><th scope="row"><?= e($concept['casilla'].' · '.$concept['nombre']) ?>
<details><summary class="text-secondary underline">? · Qué registrar y soporte</summary><p><?= e($concept['explicacion']) ?></p><p>Fundamento: <?= e($concept['fundamento']) ?></p><p>Soportes: <?= e($concept['soportes']) ?></p><?php $item=['id'=>'','norm'=>$concept['fundamento']];require __DIR__.'/../beneficios/articles.php'; ?></details></th>
<?php $grossPending=$profile['formulario']==='210' && (int)$concept['casilla']===99
    && !array_filter($movements,static fn($m)=>(int)$m['casilla']===99 && $m['estado']==='real'); ?>
<td><?php if ($grossPending): ?>Bruto pendiente de registrar<p class="text-secondary text-body-md">La pensión neta del banco se acumula arriba. Para la casilla 99 falta el bruto del comprobante del pagador; no se copia la consignación neta.</p><?php else: ?>$<?= e(number_format($values['real'],0,',','.')) ?><?php endif; ?></td><td>$<?= e(number_format($values['proyectado'],0,',','.')) ?></td>
<td><a class="inline-block rounded-xl bg-inverse-surface text-on-secondary px-4 py-2" href="<?= e(url($config,'ingresos/movimiento').'&id='.$profile['id'].'&concepto='.$concept['id'].'&origen=registro'.$contextQuery) ?>">Registrar valor · <?= e($concept['casilla']) ?></a></td></tr>
<?php endforeach; ?></tbody></table></div>
<p class="text-secondary text-body-md">Real: operaciones ocurridas. Proyectado adicional: lo que falta por ocurrir, sin repetir el real. Los valores de otros años y secciones se conservan separados.</p>
<?php endif; ?>
