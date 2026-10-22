<?php $contextQuery=\App\Services\RegistroNavegacion::query($state,(int)$taxpayer['id']); ?>
<nav class="contribuyente-tabs" aria-label="Componente de información">
<?php foreach (['ingresos'=>'Ingresos','costos'=>'Costos y gastos'] as $key=>$label): ?>
<a class="rounded-xl px-3 py-2 <?= $state['componente']===$key?'bg-inverse-surface text-on-secondary':'bg-surface-container-low text-secondary' ?>" <?= $state['componente']===$key?'aria-current="page"':'' ?> href="<?= e(url($config,'registro').\App\Services\RegistroNavegacion::query(array_replace($state,['componente'=>$key]),(int)$taxpayer['id'])) ?>"><?= e($label) ?></a>
<?php endforeach; ?></nav>
<?php if ($profile['formulario']==='210'): ?>
<p class="contribuyente-tab-hint text-secondary text-body-md">Desliza las pestañas hacia los lados para ver todas las cédulas y rentas.</p>
<nav class="contribuyente-tabs" aria-label="Cédulas del formulario 210"><?php foreach (\App\Services\RegistroNavegacion::CEDULAS as $key=>$label): ?>
<a class="rounded-xl px-3 py-2 <?= $state['cedula']===$key?'bg-inverse-surface text-on-secondary':'bg-surface-container-low text-secondary' ?>" <?= $state['cedula']===$key?'aria-current="page"':'' ?> href="<?= e(url($config,'registro').\App\Services\RegistroNavegacion::query(array_replace($state,['cedula'=>$key]),(int)$taxpayer['id'])) ?>"><?= e($label) ?></a>
<?php endforeach; ?></nav>
<?php if ($state['cedula']==='general'): ?><nav class="contribuyente-tabs" aria-label="Rentas de la cédula general"><?php foreach (\App\Services\RegistroNavegacion::RENTAS as $key=>$label): ?>
<a class="rounded-xl px-3 py-2 <?= $state['renta']===$key?'bg-inverse-surface text-on-secondary':'bg-surface-container-low text-secondary' ?>" <?= $state['renta']===$key?'aria-current="page"':'' ?> href="<?= e(url($config,'registro').\App\Services\RegistroNavegacion::query(array_replace($state,['renta'=>$key]),(int)$taxpayer['id'])) ?>"><?= e($label) ?></a>
<?php endforeach; ?></nav><?php endif; ?>
<details class="text-body-md"><summary class="text-secondary underline">? · ¿Cómo se organizan las cédulas?</summary><p>La cédula general reúne Trabajo, Capital y No laborales. Servicios personales distingue el tratamiento del trabajo sin relación laboral. Pensiones y Dividendos son cédulas separadas. Esta organización no convierte un desembolso en deducible.</p><a class="text-secondary underline" href="https://micrositios.dian.gov.co/renta-personas-naturales-ag-2025/todo-lo-que-usted-debe-saber-sobre-renta/" target="_blank" rel="noopener noreferrer">Referencia DIAN · Personas naturales AG2025</a></details>
<?php else: ?><p>Formulario 110: conceptos de la entidad o del no residente. No se aplica la organización cedular del formulario 210.</p><?php endif; ?>
