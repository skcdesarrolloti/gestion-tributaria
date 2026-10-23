<section class="bg-surface-container-lowest border border-surface-container rounded-xl p-4 space-y-space-md">
<h2 class="text-headline-md font-semibold">Preparar <?= e($state['anio']) ?> · <?= e($taxpayer['nombre']) ?></h2>
<p>Revisa los datos recuperados del RUT y del perfil. Completa las condiciones del año que todavía faltan; después podrás registrar los ingresos.</p>
<?php if ($preparation['summary']): ?><p class="text-secondary"><?= e(implode(' · ',$preparation['summary'])) ?></p><?php else: ?><p class="text-secondary">No encontramos referencias del RUT guardadas para proponer el régimen. Puedes confirmar estas condiciones manualmente.</p><?php endif; ?>
<?php foreach ($preparation['notes'] as $note): ?><p class="bg-primary-container rounded-xl p-3"><?= e($note) ?></p><?php endforeach; ?>
<?php
$record=$preparation['record'];$profileOrigins=$preparation['origins'];
$profileAction=url($config,'ingresos').'&origen=registro'.\App\Services\RegistroNavegacion::query($state,(int)$taxpayer['id']);
$profileButton='Guardar condiciones y abrir registro de '.$state['anio'];
require __DIR__.'/../ingresos/profile-form.php';
?>
</section>
<script src="<?= e(asset($config,'js/ingresos-guidance.js').'?v='.filemtime(dirname(__DIR__,3).'/public/assets/js/ingresos-guidance.js')) ?>" defer></script>
