<section id="creditos-guardados" data-credit-documents class="space-y-space-md">
<?php if ($credits): ?><h2 class="text-headline-md font-semibold">Extractos de créditos personales guardados (<?= count($credits) ?>)</h2>
<?php foreach ($credits as $credit): ?><details class="bg-surface-container-lowest border border-surface-container rounded-xl p-4"><summary class="font-semibold"><?= e($credit['datos']['banco'].' · Crédito '.substr($credit['credito'],-4).' · Pago '.$credit['fecha_pago']) ?></summary>
<p><?= e($credit['titulo']) ?> · <?= e(\App\Services\ExtractoData::money($credit['datos']['pagado']['total'])) ?> pagados. El desglose ya está disponible en Control de préstamos.</p>
<object class="bank-document" type="application/pdf" data="<?= e(url($config,'prestamos/documento').'&documento='.$credit['id']) ?>" aria-label="Documento del crédito guardado"></object></details><?php endforeach; ?>
<?php endif; ?></section>
