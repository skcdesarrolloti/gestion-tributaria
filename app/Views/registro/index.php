<?php $title=$state['componente']==='ingresos'?'Ingresos':'Costos y gastos';require __DIR__.'/../partials/head.php'; ?>
<body class="bg-background min-h-screen font-body-md text-on-surface antialiased">
<?php require __DIR__.'/../partials/header.php'; ?>
<main class="portal-container py-8 space-y-space-lg">
<section class="bg-inverse-surface text-on-secondary rounded-2xl p-4"><p>3 · Registro de información</p><h1 class="text-headline-lg font-semibold"><?= e($title) ?></h1><p>Contribuyente → año gravable → sección → registrar valores.</p></section>
<?php $contextRoute='registro';require __DIR__.'/context.php'; ?>
<?php if ($profile): ?>
<section class="bg-surface-container-lowest border border-surface-container rounded-xl p-4 space-y-space-md">
<h2 class="text-headline-md font-semibold"><?= e($profile['nombre'].' · Año gravable '.$profile['anio']) ?></h2>
<p><?= e($profile['tipo']==='juridica'?'Persona jurídica':($profile['tipo']==='sucesion'?'Sucesión ilíquida':'Persona natural')) ?> · Formulario <?= e($profile['formulario']) ?> · <?= e($profile['residencia']==='residente'?'Condición fiscal nacional / residente':'No residente') ?></p>
<?php require __DIR__.'/tabs.php'; ?>
<details><summary class="text-secondary underline">? · Formulario, año y clasificación</summary>
<p><?= $profile['formulario']==='210'?'El 210 corresponde a naturales y sucesiones residentes. General reúne trabajo, capital y no laborales; el trabajo sin relación laboral con opción de costos tiene su bloque específico. Pensiones y Dividendos son cédulas separadas. Ganancias ocasionales no es una cuarta cédula.':'El 110 corresponde a personas jurídicas y a naturales o sucesiones no residentes. Sus ingresos se organizan por las secciones del formulario, sin aplicar cédulas de personas naturales residentes.' ?></p>
<p>La estructura se contrastó con los instructivos DIAN publicados en 2026. El año gravable identifica las operaciones; no es el año de publicación del formulario. Para proyectar 2026 se usa esta estructura de referencia, sujeta a revisión al publicarse el formulario aplicable.</p>
<p>La captura acumula valores con soporte; las exenciones, tarifas y el impuesto final se revisan en Análisis y acciones. No se presenta una declaración a la DIAN desde esta pantalla.</p>
</details>
<?php
$primary=array_values(array_filter($concepts,static fn($c)=>$state['componente']==='costos'||$c['clase']==='ingreso'));
$adjustments=array_values(array_filter($concepts,static fn($c)=>$state['componente']==='ingresos'&&$c['clase']==='reduccion'));
$tableConcepts=$primary;$tableTitle=$title.' de esta sección';require __DIR__.'/casillas.php';
?>
<?php if ($adjustments): ?><details><summary class="text-secondary underline">Ajustes de estos ingresos · devoluciones e ingresos no constitutivos</summary><p>Se registran por separado con fundamento y soporte; no son ingresos adicionales ni costos y gastos.</p><?php $tableConcepts=$adjustments;$tableTitle='Ajustes de ingresos';require __DIR__.'/casillas.php'; ?></details><?php endif; ?>
<?php if (!$concepts): ?><p role="status">No hay casillas de captura para esta combinación. Pensiones y dividendos tienen tratamiento propio; no se habilitan costos de la cédula general en estas cédulas. Si elegiste trabajo sin relación laboral, comprueba la opción de costos del año.</p><?php endif; ?>
<details><summary class="text-secondary underline">Condiciones fiscales guardadas para <?= e($state['anio']) ?></summary><p>Régimen: <?= e($profile['regimen']) ?>. Tratamiento de servicios: <?= e($profile['servicios']) ?>.</p><a class="text-secondary underline" href="<?= e(url($config,'ingresos/perfil').'&id='.$profile['id']) ?>">Consultar condiciones del año</a></details>
</section>
<details class="border border-surface-container rounded-xl p-4"><summary class="font-semibold">Consultar o corregir movimientos de esta sección (<?= count($movements) ?>)</summary><?php $registration=$state;require __DIR__.'/../ingresos/movements.php'; ?></details>
<?php endif; ?>
</main><?php require __DIR__.'/../partials/footer.php'; ?>
