<?php $title = 'Iniciar sesión'; require __DIR__ . '/../partials/head.php'; ?>
<body class="bg-background min-h-screen flex items-center justify-center p-4 font-body-md text-on-surface antialiased">
  <div class="w-full max-w-md bg-surface-container-lowest p-space-xl rounded-2xl shadow-xl border border-surface-container space-y-space-md">
    <div class="flex items-center gap-space-sm">
      <div class="w-10 h-10 rounded-xl bg-primary-container flex items-center justify-center shadow-sm">
        <span class="material-symbols-outlined text-on-surface text-[24px]">domain</span>
      </div>
      <div>
        <span class="font-headline-sm text-headline-sm text-on-surface tracking-tight block">SuCasa Inmobiliaria</span>
        <span class="font-label-sm text-label-sm text-secondary uppercase font-semibold"><?= e($config['name']) ?></span>
      </div>
    </div>

    <div>
      <h1 class="font-headline-lg text-headline-lg text-on-surface tracking-tight font-bold">Iniciar sesión</h1>
      <p class="font-body-md text-body-md text-on-surface-variant mt-1">Ingresa con tus credenciales institucionales.</p>
    </div>

    <?php if ($error !== ''): ?>
      <div role="alert" class="flex items-center gap-2 p-space-sm rounded-xl bg-error-container text-on-error-container font-label-md text-label-md font-semibold">
        <span class="material-symbols-outlined text-[20px]">warning</span>
        <span><?= e($error) ?></span>
      </div>
    <?php endif; ?>

    <form method="post" action="<?= e(url($config, 'login')) ?>" class="space-y-space-sm pt-space-xs">
      <?php require __DIR__ . '/../partials/csrf.php'; ?>
      <div class="space-y-1">
        <label for="username" class="block font-label-sm text-label-sm text-secondary font-medium">Usuario</label>
        <div class="relative">
          <span class="material-symbols-outlined absolute left-3 top-2.5 text-secondary text-[18px]">person</span>
          <input type="text" id="username" name="username" maxlength="100" value="<?= e($username) ?>" autocomplete="username" required class="w-full pl-9 pr-3 py-2.5 bg-surface-container-low focus:bg-surface-container-lowest rounded-xl font-body-sm text-body-sm text-on-surface outline-none border border-transparent focus:border-outline-variant transition-all shadow-inner">
        </div>
      </div>
      <div class="space-y-1">
        <label for="password" class="block font-label-sm text-label-sm text-secondary font-medium">Contraseña</label>
        <div class="relative">
          <span class="material-symbols-outlined absolute left-3 top-2.5 text-secondary text-[18px]">lock</span>
          <input type="password" id="password" name="password" maxlength="4096" autocomplete="current-password" required class="w-full pl-9 pr-3 py-2.5 bg-surface-container-low focus:bg-surface-container-lowest rounded-xl font-body-sm text-body-sm text-on-surface outline-none border border-transparent focus:border-outline-variant transition-all shadow-inner">
        </div>
      </div>
      <button type="submit" class="w-full mt-space-sm py-3 rounded-xl bg-inverse-surface hover:bg-secondary text-on-secondary font-label-md text-label-md font-semibold transition-all shadow-md cursor-pointer">
        Iniciar Sesión
      </button>
    </form>
  </div>
</body>

</html>
