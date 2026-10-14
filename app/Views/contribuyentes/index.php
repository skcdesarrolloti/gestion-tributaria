<?php
use App\Services\ContribuyenteData;
$title = 'Contribuyentes'; require __DIR__ . '/../partials/head.php';
?>
<body class="bg-background min-h-screen font-body-md text-on-surface antialiased">
<?php require __DIR__ . '/../partials/header.php'; ?>
<main class="portal-container space-y-space-lg py-8">
    <section class="bg-inverse-surface text-on-secondary rounded-2xl p-space-xl">
        <p class="text-label-md uppercase tracking-wide">Paso 1 · Identificación</p>
        <h1 class="text-headline-lg font-semibold mt-2">Contribuyentes</h1>
        <p class="text-body-md mt-2">Identifica a quién vamos a analizar y cuáles son sus fuentes de ingresos.</p>
    </section>
    <?php require __DIR__ . '/guide.php'; ?>
    <?php if ($notice !== ''): ?>
        <p role="status" class="bg-primary-container rounded-xl p-3"><?= e($notice) ?></p>
    <?php endif; ?>
    <section class="bg-surface-container-lowest rounded-2xl border border-surface-container p-space-xl space-y-space-md">
        <h2 class="text-headline-md font-semibold">Mis contribuyentes (<?= count($rows) ?>)</h2>
        <p class="text-body-md text-secondary">Puedes consultar y editar los registros que creaste con tu cuenta.</p>
        <a class="inline-block rounded-xl bg-inverse-surface text-on-secondary px-4 py-2" href="<?= e(url($config, 'contribuyentes/formulario')) ?>">Registrar contribuyente</a>
        <?php if (!$rows): ?>
            <p class="text-body-md text-secondary">Todavía no tienes contribuyentes registrados. Comienza con sus datos de identificación; las fuentes de ingresos pueden completarse después.</p>
        <?php endif; ?>
        <div class="space-y-space-md">
            <?php foreach ($rows as $row): ?>
                <article class="rounded-xl border border-surface-container p-4 space-y-space-sm">
                    <h3 class="text-headline-sm font-semibold taxpayer-name"><?= e($row['nombre']) ?></h3>
                    <p class="text-body-md text-secondary"><?= e(ContribuyenteData::TIPOS[$row['tipo']]) ?> · <?= e($row['documento_tipo']) ?> <?= e($row['documento_numero']) ?><?= $row['dv'] !== '' ? '-' . e($row['dv']) : '' ?></p>
                    <p class="text-body-md text-secondary">Fuentes: <?= e($row['fuentes'] ? implode(', ', array_map(static fn ($key) => ContribuyenteData::sourcesFor($row['tipo'])[$key] ?? ContribuyenteData::FUENTES[$key], $row['fuentes'])) : 'Pendientes de identificar') ?></p>
                    <a class="inline-block rounded-xl bg-inverse-surface text-on-secondary px-4 py-2" href="<?= e(url($config, 'contribuyentes/formulario') . '&id=' . $row['id']) ?>" aria-label="<?= e('Editar ' . $row['nombre']) ?>">Editar datos</a>
                </article>
            <?php endforeach; ?>
        </div>
    </section>
</main>
<?php require __DIR__ . '/../partials/footer.php'; ?>
