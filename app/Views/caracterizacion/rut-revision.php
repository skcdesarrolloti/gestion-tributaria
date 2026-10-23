<?php
$last=$documents[0];$parsed=json_decode($last['metadatos'],true,64,JSON_THROW_ON_ERROR);
$audit=\App\Services\RutRevision::build($parsed,$documentBlocks,$schemas,$taxpayer);
?>
<details class="border border-surface-container rounded-xl p-3 space-y-space-md">
<summary class="font-semibold">Revisar RUT frente al perfil · <?= e((string)$audit['counts']['pendiente']) ?> datos sin completar · <?= e((string)$audit['counts']['diferente']) ?> diferencias</summary>
<p>Se compara el último PDF guardado en este año con los datos actuales, sin sustituirlos. «Coincide» significa que ambos valores son iguales, no que la DIAN haya certificado su vigencia.</p>
<p>Páginas del archivo: <?= e((string)$audit['pages_count']) ?> · <?= e((string)$audit['counts']['coincide']) ?> datos coincidentes · Comparación con el perfil <?= e($documentYear) ?>.</p>
<details><summary class="text-secondary underline">Cobertura de páginas y límites de lectura</summary>
<?php foreach ($audit['pages'] as $page): ?><p>Página <?= e((string)$page['pagina']) ?> · Hoja <?= e((string)$page['hoja']) ?>: <?= e($page['resultado']) ?></p><?php endforeach; ?>
<p>El contraste incluye los campos que el lector reconoció. Un campo vacío del PDF no es automáticamente un dato faltante obligatorio. Las páginas no reconocidas requieren transcripción manual. Firmas, códigos QR y elementos de autenticidad se conservan en el soporte; este cuadro no los certifica ni declara una lectura automática al 100 %.</p>
</details>
<?php foreach ($audit['groups'] as $auditGroup): ?><details name="revision-datos-rut" class="border border-surface-container rounded-xl p-3">
<summary class="font-semibold"><?= e($auditGroup['title']) ?> · <?= count($auditGroup['rows']) ?> datos</summary>
<div class="renta-sheet-scroll" tabindex="0" role="region" aria-label="Comparación del RUT"><table class="renta-sheet-table"><thead><tr><th>Campo</th><th>Leído del PDF</th><th>Guardado en perfil</th><th>Contraste</th></tr></thead><tbody>
<?php foreach ($auditGroup['rows'] as $auditRow): ?><tr><th scope="row"><?= e($auditRow['label']) ?></th><td><?= e($auditRow['pdf']) ?><?php if ($auditRow['meaning']!==''): ?><p class="text-label-sm text-secondary">Catálogo: <?= e($auditRow['meaning']) ?></p><?php endif; ?></td><td><?= e($auditRow['current']!==''?$auditRow['current']:'Sin completar') ?></td><td><?= e(['coincide'=>'Coincide','pendiente'=>'Completar o revisar','diferente'=>'Dato diferente: revisar'][$auditRow['state']]) ?></td></tr><?php endforeach; ?>
</tbody></table></div></details><?php endforeach; ?>
<p>Si existe una diferencia, revisa la pestaña correspondiente del perfil. Se conserva tu edición manual; este contraste no modifica datos ni actualiza el RUT ante la DIAN.</p>
</details>
