<?php $selectedHelp=$options[$record[$field]]??null; ?>
<div id="<?= e($field.'-help') ?>" class="text-body-md text-secondary mt-2" aria-live="polite" data-help-for="<?= e($field) ?>">
    <p data-help-empty <?= $selectedHelp?'hidden':'' ?>>Selecciona una opción para consultar su definición, alcance y fundamento.</p>
    <div data-help-content <?= !$selectedHelp?'hidden':'' ?>>
        <p><strong>Definición:</strong> <span data-help-definition><?= e($selectedHelp['definicion']??'') ?></span></p>
        <p class="mt-2"><strong>Alcance:</strong> <span data-help-scope><?= e($selectedHelp['alcance']??'') ?></span></p>
        <p class="mt-2"><strong>Fundamento:</strong> <a class="underline" data-help-source href="<?= e($selectedHelp['url']??'https://normograma.dian.gov.co/dian/compilacion/docs/estatuto_tributario.htm') ?>" target="_blank" rel="noopener noreferrer"><?= e($selectedHelp['norma']??'') ?></a></p>
    </div>
</div>
