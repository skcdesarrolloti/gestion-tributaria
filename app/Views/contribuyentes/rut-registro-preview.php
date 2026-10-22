<?php $parsed=$rutDraft['file']['parsed']; ?>
<p>NIT <?= e($parsed['nit'].'-'.$parsed['dv']) ?> · Formulario <?= e($parsed['formulario']) ?> · Generado <?= e($parsed['generado']?:'sin fecha legible') ?></p>
<?php foreach ($rutDraft['summary'] as $group=>$fields): ?>
    <details class="border border-surface-container rounded-xl p-3 mt-2"><summary class="font-semibold"><?= e($group) ?> · <?= count($fields) ?> campos y renglones</summary>
        <dl><?php foreach ($fields as $field): ?><dt class="font-semibold mt-2"><?= e($field['label']) ?></dt><dd class="taxpayer-name"><?= e($field['value']) ?></dd><?php endforeach; ?></dl>
    </details>
<?php endforeach; ?>
