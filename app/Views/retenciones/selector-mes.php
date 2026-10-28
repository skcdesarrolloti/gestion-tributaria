<form method="get" class="space-y-space-sm">
<input type="hidden" name="route" value="retenciones"><input type="hidden" name="id" value="<?= e($profile['id']) ?>">
<label>Mes y empleador que quieres revisar<select name="periodo" class="w-full rounded-xl bg-surface-container-low p-3">
<?php foreach ($groups as $periodKey=>$periodGroup): ?><option value="<?= e($periodKey) ?>" <?= $group['mes']===$periodGroup['mes'] && $group['documento']===$periodGroup['documento']?'selected':'' ?>><?= e(\App\Services\NominaPreview::MONTHS[$periodGroup['mes']-1].' · '.$periodGroup['empleador'].($periodGroup['completo']?'':' · faltan períodos')) ?></option><?php endforeach; ?>
</select></label><p class="text-label-sm text-secondary">Revisamos cada empleador por separado. Al entrar sin escoger un mes abrimos el último mes completo disponible.</p>
<button class="rounded-xl bg-inverse-surface text-on-secondary px-4 py-2">Abrir mes</button>
</form>
