<header class="bg-surface-container-lowest border-b border-surface-container">
    <div class="portal-header">
        <a href="<?= e(url($config)) ?>" class="flex items-center gap-space-sm">
            <span aria-hidden="true" class="material-symbols-outlined bg-primary-container p-2 rounded-xl">domain</span>
            <span><strong class="block">SuCasa Inmobiliaria</strong><span class="text-secondary text-label-sm"><?= e($config['name']) ?></span></span>
        </a>
        <nav aria-label="Navegación principal" class="flex items-center gap-space-md flex-wrap">
            <a href="<?= e(url($config)) ?>" class="text-secondary">Inicio</a>
            <?php if (\App\Services\AccesoRoles::socio($user)): ?><a href="<?= e(url($config,'accesos')) ?>" class="text-secondary">Usuarios y accesos</a><?php endif; ?>
            <a href="<?= e(url($config, 'contribuyentes')) ?>" class="text-secondary">Contribuyentes</a>
            <?php require __DIR__.'/trabajo-menu.php'; require __DIR__.'/exogena-menu.php'; ?>
            <a href="<?= e(url($config, 'perfil')) ?>" class="text-secondary">Mi perfil</a>
            <form method="post" action="<?= e(url($config, 'logout')) ?>">
                <?php require __DIR__ . '/csrf.php'; ?>
                <button class="rounded-xl bg-inverse-surface text-on-secondary px-4 py-2" type="submit">Cerrar sesión</button>
            </form>
        </nav>
    </div>
</header>
<script src="<?= e(asset($config,'js/portal-menu.js').'?v='.filemtime(dirname(__DIR__,3).'/public/assets/js/portal-menu.js')) ?>" defer></script>
