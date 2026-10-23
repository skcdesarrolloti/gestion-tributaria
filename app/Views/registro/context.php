<section class="bg-surface-container-lowest border border-surface-container rounded-xl p-4 space-y-space-sm">
<form method="get" action="<?= e($config['base_path'].'/index.php') ?>" class="profile-grid">
<input type="hidden" name="route" value="<?= e($contextRoute) ?>">
<?php foreach (['componente','cedula','renta'] as $key): ?><input type="hidden" name="<?= e($key) ?>" value="<?= e($state[$key]) ?>"><?php endforeach; ?>
<label>Contribuyente<select name="contribuyente" required class="w-full bg-surface-container-low rounded-xl p-2"><option value="">Selecciona su ficha</option><?php foreach ($rows as $row): ?><option value="<?= e($row['id']) ?>" <?= $taxpayer && (int)$taxpayer['id']===(int)$row['id']?'selected':'' ?>><?= e($row['nombre']) ?></option><?php endforeach; ?></select></label>
<label>Año gravable<select name="anio" class="w-full bg-surface-container-low rounded-xl p-2"><?php foreach (['2025','2026'] as $year): ?><option <?= $state['anio']===$year?'selected':'' ?>><?= e($year) ?></option><?php endforeach; ?></select></label>
<div><button class="rounded-xl bg-inverse-surface text-on-secondary px-4 py-2">Abrir año</button></div>
</form>
<?php if (!$rows): ?><p>No tienes un contribuyente asignado. Un Socio debe crear o asignar tu ficha.</p><?php endif; ?>
</section>
<?php if ($taxpayer && !$profile): ?><section class="bg-primary-container rounded-xl p-4 space-y-space-sm"><p>No hay un perfil fiscal guardado para <?= e($state['anio']) ?>. Confírmalo antes de registrar valores de ese año; no se copiarán importes del otro año.</p><a class="text-secondary underline" href="<?= e(url($config,'ingresos').'&origen=registro'.\App\Services\RegistroNavegacion::query($state,(int)$taxpayer['id'])) ?>">Configurar perfil de <?= e($state['anio']) ?></a></section><?php endif; ?>
