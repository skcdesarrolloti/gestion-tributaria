<?php
$incomeView=$state['componente']==='ingresos';
$bankMode=!$incomeView?'gmf':(!$pensionView && (($profile['formulario']??'210')==='210'?$state['cedula']==='general' && $state['renta']==='capital':$state['seccion']==='financieros')?'rendimientos':'');
$laborView=$incomeView && $profile && $profile['formulario']==='210' && $profile['tipo']==='natural' && $state['cedula']==='general' && $state['renta']==='trabajo';
$title=$incomeView?($profile && $profile['formulario']==='110'
    ?\App\Services\RegistroNavegacion::SECCIONES_110[$state['seccion']]
    :($state['cedula']==='general'?($state['renta']==='trabajo'?'Ingresos laborales':\App\Services\RegistroNavegacion::RENTAS[$state['renta']])
        :(\App\Services\RegistroNavegacion::CEDULAS[$state['cedula']]??'Ganancias ocasionales'))):'Costos y gastos';
require __DIR__.'/../partials/head.php'; ?>
<body class="bg-background min-h-screen font-body-md text-on-surface antialiased">
<?php require __DIR__.'/../partials/header.php'; ?>
<main class="portal-container py-8 space-y-space-lg">
<section class="bg-inverse-surface text-on-secondary rounded-2xl p-4"><h1 class="text-headline-lg font-semibold"><?= e($title) ?></h1></section>
<?php $contextRoute='registro';require __DIR__.'/context.php'; ?>
<?php if ($incomeView && $taxpayer): ?><p><a class="inline-block rounded-xl bg-inverse-surface text-on-secondary px-4 py-2" href="<?= e(url($config,'extractos').'&contribuyente='.$taxpayer['id'].'&anio='.$state['anio']) ?>">Subir extractos bancarios · carga única</a></p><?php endif; ?>
<?php require __DIR__.'/ayuda-rentas.php'; ?>
<?php if ($pensionView && $taxpayer): require __DIR__.'/colpensiones.php';require __DIR__.'/pension-meses.php'; endif; ?>
<?php if ($taxpayer): if ($pensionView) { require __DIR__.'/pensiones.php'; } elseif ($bankMode) { require __DIR__.'/bancos.php'; } endif; ?>
<?php if ($pensionView && $taxpayer): ?><details><summary class="text-secondary underline">Casillas DIAN de pensiones · bruto, ajustes y condiciones del año</summary><p>Los extractos guardados se muestran arriba. El abono bancario es neto; la casilla 99 requiere la pensión bruta y sus descuentos verificados. Un valor $0 en esa casilla no borra ni representa la cantidad de PDF guardados.</p><?php endif; ?>
<?php if ($taxpayer && !$profile): require __DIR__.'/configurar-anio.php'; endif; ?>
<?php if ($profile): $contextQuery=\App\Services\RegistroNavegacion::query($state,(int)$taxpayer['id']); ?>
<section data-casillas-section class="bg-surface-container-lowest border border-surface-container rounded-xl p-4 space-y-space-md">
<?php if ($state['componente']==='costos'): require __DIR__.'/tabs.php'; endif; ?>
<?php if ($laborView): require __DIR__.'/../nomina/monthly.php'; endif; ?>
<?php if ($laborView && $payroll): ?><details><summary class="text-secondary underline">Registrar otro pago laboral o una proyección</summary><p>La nómina ya está sumada arriba. Usa estas casillas solo para pagos adicionales; no vuelvas a registrar lo importado.</p><?php endif; ?>
<?php
$primary=array_values(array_filter($concepts,static fn($c)=>$state['componente']==='costos'||$c['clase']==='ingreso'));
$adjustments=array_values(array_filter($concepts,static fn($c)=>$state['componente']==='ingresos'&&$c['clase']==='reduccion'));
$tableConcepts=$primary;$tableTitle=$title;require __DIR__.'/casillas.php';
?>
<?php if ($adjustments): ?><details><summary class="text-secondary underline"><?= $pensionView?'Salud y aportes obligatorios de la pensión · casilla 100':'Ajustes de estos ingresos · devoluciones e ingresos no constitutivos' ?></summary><p>Se registran por separado con fundamento y soporte; no son ingresos adicionales ni costos y gastos.</p><?php $tableConcepts=$adjustments;$tableTitle='Ajustes de ingresos';require __DIR__.'/casillas.php'; ?></details><?php endif; ?>
<?php if ($laborView && $payroll): ?></details><?php endif; ?>
<?php if (!$concepts): ?><p role="status">No hay casillas de captura disponibles para esta sección y las condiciones del año.</p><?php endif; ?>
<details><summary class="text-secondary underline">? · Formulario y condiciones de <?= e($state['anio']) ?></summary>
<p><?= e($profile['nombre']) ?> · Formulario <?= e($profile['formulario']) ?> · <?= e($profile['residencia']==='residente'?'Condición fiscal nacional / residente':'No residente') ?></p>
<p><?= $profile['formulario']==='210'?'El 210 corresponde a naturales y sucesiones residentes. General reúne trabajo, capital y no laborales; el trabajo sin relación laboral con opción de costos tiene su bloque específico. Pensiones y Dividendos son cédulas separadas. Ganancias ocasionales no es una cuarta cédula.':'El 110 corresponde a personas jurídicas y a naturales o sucesiones no residentes. Sus ingresos se organizan por las secciones del formulario, sin aplicar cédulas de personas naturales residentes.' ?></p>
<p>La estructura se contrastó con los instructivos DIAN publicados en 2026. El año gravable identifica las operaciones; no es el año de publicación del formulario. Para proyectar 2026 se usa esta estructura de referencia, sujeta a revisión al publicarse el formulario aplicable.</p>
<p>La captura acumula valores con soporte; las exenciones, tarifas y el impuesto final se revisan en Análisis y acciones. No se presenta una declaración a la DIAN desde esta pantalla.</p>
<p>Régimen: <?= e($profile['regimen']) ?>. Tratamiento de servicios: <?= e($profile['servicios']) ?>.</p><a class="text-secondary underline" href="<?= e(url($config,'ingresos/perfil').'&id='.$profile['id']) ?>">Consultar condiciones del año</a></details>
</section>
<details data-registro-movimientos class="border border-surface-container rounded-xl p-4"><summary class="font-semibold">Consultar o corregir movimientos de esta sección (<?= count($movements) ?>)</summary><?php $registration=$state;require __DIR__.'/../ingresos/movements.php'; ?></details>
<?php endif; ?>
<?php if ($pensionView && $taxpayer): ?></details><?php endif; ?>
</main><?php if ($profile): ?><script src="<?= e(asset($config,'js/normas.js')) ?>" defer></script><?php endif; ?>
<?php if ($pensionView && $profile): ?><script src="<?= e(asset($config,'js/pdf-carga.js').'?v='.filemtime(dirname(__DIR__,3).'/public/assets/js/pdf-carga.js')) ?>" defer></script><?php endif; ?>
<?php if ($pensionView && $profile): ?><script src="<?= e(asset($config,'js/colpensiones.js').'?v='.filemtime(dirname(__DIR__,3).'/public/assets/js/colpensiones.js')) ?>" defer></script><?php endif; ?><?php require __DIR__.'/../partials/footer.php'; ?>
