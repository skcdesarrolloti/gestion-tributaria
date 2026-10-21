document.querySelectorAll('[data-exogena-menu], [data-planeacion-menu]').forEach((menu) => {
    document.addEventListener('pointerdown', (event) => {
        if (!menu.contains(event.target)) menu.open = false;
    });
    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && menu.open) {
            menu.open = false;
            menu.querySelector('summary').focus();
        }
    });
    menu.addEventListener('focusout', () => {
        setTimeout(() => {
            if (!menu.contains(document.activeElement)) menu.open = false;
        }, 0);
    });
});
