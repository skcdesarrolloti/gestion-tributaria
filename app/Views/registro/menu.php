<nav class="portal-container registro-section-tabs" aria-label="Secciones de Registro de información">
<?php foreach (\App\Services\RegistroNavegacion::menu($menuRoute,$_GET,$menuTaxpayer,$menuYear) as $tab): ?>
<a class="rounded-xl px-3 py-2 <?= $tab['active']?'bg-inverse-surface text-on-secondary':'bg-surface-container-low text-secondary' ?>" <?= $tab['active']?'aria-current="page"':'' ?> href="<?= e(url($config,$tab['route']).$tab['query']) ?>"><?= e($tab['label']) ?></a>
<?php endforeach; ?>
</nav>
<?php if ($menuRoute!=='registro' || input($_GET,'componente','ingresos')==='ingresos'):
    if (!empty($profile['formulario']) && $menuTaxpayer):
        // Reutilizar las cédulas existentes, sin alterar el contexto de las vistas que siguen.
        (static function(array $navigationState) use ($config,$profile,$menuRoute,$menuTaxpayer,$menuYear): void {
            $taxpayer=['id'=>$menuTaxpayer];$incomeMenu=true;
            $state=\App\Services\RegistroNavegacion::state(array_replace($navigationState,['anio'=>$menuYear,'componente'=>'ingresos']));
            if (in_array($menuRoute,['nomina','retenciones'],true)) { $state['cedula']='general';$state['renta']='trabajo'; }
            if ($menuRoute==='extractos') { $state['cedula']='pensiones'; }
            require __DIR__.'/tabs.php';
        })($state??$registration??[]);
    endif;
endif; ?>
