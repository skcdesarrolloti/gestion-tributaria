<?php
$groups=$block==='entidad'?require dirname(__DIR__,3).'/resources/caracterizacion/grupos.php':[];
if (!$groups) { $groups=[''=>array_keys($schema['fields'])]; }
foreach ($groups as $heading=>$keys):
    $remaining=count(array_filter($keys,static fn($key)=>($record['campos'][$key]??'')===''));
?>
    <?php if ($heading!==''): ?><details class="border border-surface-container rounded-xl p-4" data-profile-group><summary class="font-semibold"><?= e($heading) ?> · <?= $remaining ?> campos vacíos</summary><?php endif; ?>
    <div class="scenario-settings-grid mt-2">
        <?php foreach ($keys as $key): if (!isset($schema['fields'][$key])) { continue; } $definition=$schema['fields'][$key];
            $name='campos['.$key.']'; $inputId='campo-'.$key; $value=$record['campos'][$key]; require __DIR__.'/field.php';
        endforeach; ?>
    </div>
    <?php if ($heading!==''): ?></details><?php endif; ?>
<?php endforeach; ?>
