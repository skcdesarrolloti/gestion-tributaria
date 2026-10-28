<?php $contextQuery=\App\Services\RegistroNavegacion::query($state,(int)$taxpayer['id']);$incomeMenu=$incomeMenu??false;
$incomeTabClass=$incomeMenu?'portal-container registro-section-tabs':'contribuyente-tabs'; ?>
<?php if (!$incomeMenu): ?>
<nav class="contribuyente-tabs" aria-label="Años gravables">
<?php foreach (['2025','2026'] as $tabYear): ?>
<a class="rounded-xl px-3 py-2 <?= $state['anio']===$tabYear?'bg-inverse-surface text-on-secondary':'bg-surface-container-low text-secondary' ?>" <?= $state['anio']===$tabYear?'aria-current="page"':'' ?> href="<?= e(url($config,'registro').\App\Services\RegistroNavegacion::query(array_replace($state,['anio'=>$tabYear]),(int)$taxpayer['id'])) ?>">Año <?= e($tabYear) ?></a>
<?php endforeach; ?></nav>
<?php endif; ?>
<?php if ($profile['formulario']==='210'): ?>
<nav class="<?= $incomeTabClass ?>" aria-label="Cédulas del formulario 210">
<?php foreach (\App\Services\RegistroNavegacion::CEDULAS+['ganancias'=>'Ganancias ocasionales · sección independiente'] as $key=>$label): ?>
<a class="rounded-xl px-3 py-2 <?= $state['cedula']===$key?'bg-inverse-surface text-on-secondary':'bg-surface-container-low text-secondary' ?>" <?= $state['cedula']===$key?'aria-current="page"':'' ?> href="<?= e(url($config,'registro').\App\Services\RegistroNavegacion::query(array_replace($state,['cedula'=>$key]),(int)$taxpayer['id'])) ?>"><?= e($label) ?></a>
<?php endforeach; ?></nav>
<?php if ($state['cedula']==='general'): ?><nav class="<?= $incomeTabClass ?>" aria-label="Rentas de la cédula general">
<?php foreach (\App\Services\RegistroNavegacion::RENTAS as $key=>$label): ?>
<?php if ($key==='servicios' && $profile['servicios']!=='costos') { continue; } ?>
<a class="rounded-xl px-3 py-2 <?= $state['renta']===$key?'bg-inverse-surface text-on-secondary':'bg-surface-container-low text-secondary' ?>" <?= $state['renta']===$key?'aria-current="page"':'' ?> href="<?= e(url($config,'registro').\App\Services\RegistroNavegacion::query(array_replace($state,['renta'=>$key]),(int)$taxpayer['id'])) ?>"><?= e($incomeMenu?$label:($key==='servicios'?'Trabajo sin relación laboral · opción costos':$label)) ?></a>
<?php endforeach; ?></nav><?php endif; ?>
<?php elseif ($state['componente']==='ingresos'): ?>
<nav class="<?= $incomeTabClass ?>" aria-label="Secciones de ingresos del formulario 110">
<?php foreach (\App\Services\RegistroNavegacion::SECCIONES_110 as $key=>$label): ?>
<a class="rounded-xl px-3 py-2 <?= $state['seccion']===$key?'bg-inverse-surface text-on-secondary':'bg-surface-container-low text-secondary' ?>" <?= $state['seccion']===$key?'aria-current="page"':'' ?> href="<?= e(url($config,'registro').\App\Services\RegistroNavegacion::query(array_replace($state,['seccion'=>$key]),(int)$taxpayer['id'])) ?>"><?= e($label) ?></a>
<?php endforeach; ?></nav>
<?php endif; ?>
