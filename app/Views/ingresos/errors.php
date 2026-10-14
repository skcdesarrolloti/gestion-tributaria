<?php if ($errors): ?>
<div role="alert" class="p-3 rounded-xl bg-error-container text-on-error-container">
    <p class="font-semibold">Revisa los siguientes datos:</p>
    <ul class="list-disc pl-4"><?php foreach ($errors as $error): ?><li><?= e($error) ?></li><?php endforeach; ?></ul>
</div>
<?php endif; ?>
