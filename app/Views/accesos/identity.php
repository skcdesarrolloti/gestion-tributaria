<label class="block">Nombre completo *<input name="nombre" value="<?= e(input($_POST,'nombre')) ?>" maxlength="160" required class="w-full bg-surface-container-low rounded-xl p-2"></label>
<label class="block">Usuario *<input name="username" value="<?= e(input($_POST,'username')) ?>" minlength="3" maxlength="100" autocomplete="username" required class="w-full bg-surface-container-low rounded-xl p-2"></label>
<label class="block">Correo (opcional)<input name="correo" type="email" value="<?= e(input($_POST,'correo')) ?>" maxlength="190" class="w-full bg-surface-container-low rounded-xl p-2"></label>
<p class="text-secondary">La cuenta pertenece únicamente a Gestión Tributaria. Usa un usuario con letras sin tildes, números, punto o guion.</p>
