<?php $title='Control de préstamos personales';require __DIR__.'/../partials/head.php'; ?>
<body class="bg-background min-h-screen font-body-md text-on-surface antialiased">
<?php require __DIR__.'/../partials/header.php';$contextRoute='prestamos';$money=static fn($v)=>$v===null?'—':\App\Services\ExtractoData::money($v); ?>
<main class="portal-container py-8 space-y-space-lg"><h1 class="text-headline-lg font-semibold">Control de préstamos personales</h1>
<?php require __DIR__.'/../registro/context.php'; ?>
<?php if ($taxpayer): ?>
<section class="bg-surface-container-lowest border border-surface-container rounded-xl p-4 space-y-space-md">
<?php $year=$state['anio'];require __DIR__.'/../extractos/access.php'; ?>
<p>El documento del crédito separa capital, intereses y seguros. El cargo de la cuenta es soporte del mismo pago: no se vuelve a sumar ni se convierte automáticamente en gasto deducible.</p>
</section>
<section data-loan-months class="space-y-space-md">
<?php require __DIR__.'/months.php'; ?>
<details><summary class="text-secondary underline">? · Cómo cruza con la pensión y qué falta</summary><p>Colpensiones informa bruto, salud, libranza y neto. El neto debe contrastarse con lo recibido en el banco; la libranza, con el pago del crédito. Hasta tener ambos soportes queda pendiente. Si el banco descuenta después de consignar, el débito se vincula al mismo pago y no se vuelve a sumar.</p><p>La cuota no se resta de la pensión bruta como salud. El capital reduce la deuda; intereses y seguros se controlan por separado y no se convierten automáticamente en deducciones tributarias. La coincidencia de importes es orientativa: no identifica por sí sola qué crédito descontó Colpensiones.</p></details>
<?php if ($documents): ?><details><summary class="text-secondary underline">Documentos del préstamo y próxima cuota</summary><?php foreach ($documents as $d): ?><p><?= e($d['datos']['banco'].' · crédito terminado en '.substr($d['credito'],-4).' · corte '.$d['fecha_factura']) ?> · saldo <?= e($money($d['datos']['saldo'])) ?> · cuota facturada <?= e($money($d['datos']['proxima']['total'])) ?> para <?= e($d['datos']['vence']) ?>. <a class="text-secondary underline" href="<?= e(url($config,'prestamos').'&contribuyente='.$taxpayer['id'].'&anio='.$state['anio'].'&documento='.$d['id'].'#prestamo-pdf') ?>">Ver PDF aquí</a></p><?php endforeach; ?></details><?php endif; ?>
</section>
<?php if ($selected): ?><section id="prestamo-pdf"><object class="bank-document" type="application/pdf" data="<?= e(url($config,'prestamos/documento').'&documento='.$selected['id']) ?>" aria-label="Extracto del crédito personal"></object></section><?php endif; ?>
<?php endif; ?></main><?php require __DIR__.'/../partials/footer.php'; ?>
