<?php $selectedHelp=$options[$record[$field]]??null; ?>
<details class="text-body-md text-secondary mt-2">
<summary class="underline">? · Cómo elegir y consultar el fundamento</summary>
<div id="<?= e($field.'-help') ?>" aria-live="polite" data-help-for="<?= e($field) ?>">
    <p data-help-empty <?= $selectedHelp?'hidden':'' ?>>Selecciona una opción para consultar su definición, alcance y fundamento aquí mismo.</p>
    <div data-help-content <?= !$selectedHelp?'hidden':'' ?>>
        <p><strong>Definición:</strong> <span data-help-definition><?= e($selectedHelp['definicion']??'') ?></span></p>
        <p class="mt-2"><strong>Alcance:</strong> <span data-help-scope><?= e($selectedHelp['alcance']??'') ?></span></p>
        <p class="mt-2"><strong>Fundamento:</strong> <span data-help-source><?= e($selectedHelp['norma']??'') ?></span></p>
    </div>
    <?php foreach ($options as $helpKey=>$helpOption): ?>
    <div data-help-articles="<?= e($helpKey) ?>" <?= (string)$record[$field]!== (string)$helpKey?'hidden':'' ?>>
    <?php $item=['id'=>'','norm'=>$helpOption['norma']];require __DIR__.'/../beneficios/articles.php'; ?>
    </div>
    <?php endforeach; ?>
</div>
</details>
<?php if (empty($incomeNormsLoaded)): $incomeNormsLoaded=true; ?>
<script src="<?= e(asset($config,'js/normas.js')) ?>" defer></script>
<?php endif; ?>
