(() => {
    'use strict';
    const role = document.querySelector('select[name="perfil"]');
    const group = document.querySelector('[data-user-taxpayer]');
    const active = document.querySelector('select[name="activo"]');
    if (!role || !group || !active) return;
    const taxpayer = group.querySelector('select');
    const refresh = () => {
        const partner = role.value === 'socio';
        group.hidden = partner;
        taxpayer.disabled = partner;
        taxpayer.required = !partner && active.value === '1';
    };
    role.addEventListener('change', refresh);
    active.addEventListener('change', refresh);
    refresh();
})();
