<?php if ($pages>1): ?>
<nav aria-label="Páginas de fichas" class="flex items-center gap-space-md flex-wrap">
    <?php for ($number=1;$number<=$pages;$number++): ?>
        <a class="rounded-xl px-3 py-2.5 <?= $number===$page?'bg-inverse-surface text-on-secondary':'bg-surface-container-low text-secondary' ?>" <?= $number===$page?'aria-current="page"':'' ?> href="<?= e(url($config,'beneficios').'&'.http_build_query(['anio'=>$year,'grupo'=>$group,'buscar'=>$query,'pagina'=>$number])) ?>">Página <?= e($number) ?></a>
    <?php endfor; ?>
</nav>
<?php endif; ?>
