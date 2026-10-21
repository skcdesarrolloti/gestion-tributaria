<details class="text-body-md text-secondary mt-2">
    <summary class="cursor-pointer underline" aria-label="<?= e('Ayuda sobre '.$helpTitle) ?>">? · Ver explicación</summary>
    <p class="mt-2"><?= e($helpText) ?></p>
    <?php if (!empty($helpArticle)): ?>
        <p class="mt-2"><a class="underline" href="<?= e('https://normograma.dian.gov.co/dian/compilacion/docs/estatuto_tributario.htm#'.$helpArticle) ?>" target="_blank" rel="noopener noreferrer">Consultar ET <?= e($helpArticle) ?> en la DIAN</a></p>
    <?php endif; ?>
</details>
