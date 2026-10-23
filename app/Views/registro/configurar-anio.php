<section class="bg-surface-container-lowest border border-surface-container rounded-xl p-4 space-y-space-md">
<h2 class="text-headline-md font-semibold">Preparar <?= e($state['anio']) ?> · <?= e($taxpayer['nombre']) ?></h2>
<p>Confirma estas condiciones una sola vez para este año. Determinan el formulario y las casillas disponibles; no se copian valores entre años.</p>
<?php
$record=\App\Services\IngresoData::read([],\App\Models\Ingreso::PROFILE_FIELDS);
$record['anio']=$state['anio'];
$profileAction=url($config,'ingresos').'&origen=registro'.\App\Services\RegistroNavegacion::query($state,(int)$taxpayer['id']);
$profileButton='Guardar condiciones y abrir registro de '.$state['anio'];
require __DIR__.'/../ingresos/profile-form.php';
?>
</section>
<script src="<?= e(asset($config,'js/ingresos-guidance.js')) ?>" defer></script>
