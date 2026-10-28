<?php
$menuRoute=input($_GET,'route','inicio');
$menuActive=match (true) {
    $menuRoute==='' || $menuRoute==='inicio'=>'inicio',
    $menuRoute==='accesos' || str_starts_with($menuRoute,'usuarios/')=>'accesos',
    str_starts_with($menuRoute,'contribuyentes') || $menuRoute==='planeacion/datos'=>'contribuyentes',
    str_starts_with($menuRoute,'beneficios') || $menuRoute==='seguridad-social'=>'beneficios',
    in_array($menuRoute,['registro','registro/fuentes','nomina','retenciones','extractos'],true) || str_starts_with($menuRoute,'ingresos/') || $menuRoute==='ingresos'=>'registro',
    $menuRoute==='analisis' || $menuRoute==='simulador' || str_starts_with($menuRoute,'renta')=>'analisis',
    str_starts_with($menuRoute,'exogena')=>'exogena',
    $menuRoute==='perfil'=>'perfil',$menuRoute==='configuracion'=>'configuracion',default=>'',
};
$menuStyle=static fn(string $section)=>$menuActive===$section?'bg-inverse-surface text-on-secondary':'text-secondary';
$menuLink=static fn(string $section)=>'class="rounded-xl px-3 py-2.5 '.$menuStyle($section).'"'.($menuActive===$section?' aria-current="page"':'');
?>
<header id="menu-principal" class="bg-surface-container-lowest border-b border-surface-container">
    <div class="portal-header">
        <a href="<?= e(url($config)) ?>" class="flex items-center gap-space-sm">
            <span aria-hidden="true" class="material-symbols-outlined bg-primary-container p-2 rounded-xl">domain</span>
            <span><strong class="block">SuCasa Inmobiliaria</strong><span class="text-secondary text-label-sm"><?= e($config['name']) ?></span></span>
        </a>
        <nav aria-label="Navegación principal" class="flex items-center gap-space-md flex-wrap">
            <a href="<?= e(url($config)) ?>" <?= $menuLink('inicio') ?>>Inicio</a>
            <?php if (\App\Services\AccesoRoles::socio($user)): ?><a href="<?= e(url($config,'accesos')) ?>" <?= $menuLink('accesos') ?>>Usuarios y accesos</a><?php endif; ?>
            <a href="<?= e(url($config, 'contribuyentes')) ?>" <?= $menuLink('contribuyentes') ?>>Contribuyentes</a>
            <?php require __DIR__.'/trabajo-menu.php'; require __DIR__.'/exogena-menu.php'; ?>
            <a href="<?= e(url($config, 'perfil')) ?>" <?= $menuLink('perfil') ?>>Mi perfil</a>
            <?php if (\App\Services\AccesoRoles::socio($user)): ?><a href="<?= e(url($config,'configuracion')) ?>" <?= $menuLink('configuracion') ?>>Configuración</a><?php endif; ?>
            <form method="post" action="<?= e(url($config, 'logout')) ?>">
                <?php require __DIR__ . '/csrf.php'; ?>
                <button class="rounded-xl border border-surface-container text-secondary px-4 py-2" type="submit">Cerrar sesión</button>
            </form>
        </nav>
    </div>
</header>
<nav class="portal-location bg-surface-container-lowest border-b border-surface-container" aria-label="Orientación y regreso">
<div class="portal-container"><a class="text-secondary underline" href="#menu-principal">↑ Menú principal</a>
<?php if (isset($profile['contribuyente_id'],$profile['anio'])):
    $returnPayroll=($profile['tipo']??'')==='natural' && ($profile['formulario']??'')==='210'; ?>
<a class="text-secondary underline" href="<?= e(url($config,'registro').'&contribuyente='.$profile['contribuyente_id'].'&anio='.$profile['anio'].'&componente=ingresos'.($returnPayroll?'&cedula=general&renta=trabajo#nomina':'')) ?>">← Volver a ingresos · <?= e($profile['nombre'].' · '.$profile['anio']) ?></a><?php else: ?><a class="text-secondary underline" href="<?= e(url($config)) ?>">← Inicio</a><?php endif; ?>
</div></nav>
<script src="<?= e(asset($config,'js/portal-menu.js').'?v='.filemtime(dirname(__DIR__,3).'/public/assets/js/portal-menu.js')) ?>" defer></script>
