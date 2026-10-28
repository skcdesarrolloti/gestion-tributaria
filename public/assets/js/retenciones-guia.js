(() => {
    'use strict';
    document.querySelectorAll('[data-retencion-guide]').forEach(panel => {
        const form=panel.closest('form');
        const control=form?.elements.namedItem(panel.dataset.retencionGuide);
        if (!control) return;
        let options;
        try { options=JSON.parse(panel.dataset.retencionOptions); } catch { return; }
        const update=()=> { panel.textContent=options[control.value]||'Selecciona una opción: aquí aparecerá su explicación automáticamente.'; };
        control.addEventListener('change',update);control.addEventListener('input',update);update();
    });
    document.querySelectorAll('[data-retencion-form]').forEach(form => {
        const unsaved=form.querySelector('[data-retencion-unsaved]');
        const changed=()=> { if (unsaved) unsaved.hidden=false; };
        form.addEventListener('input',changed);form.addEventListener('change',changed);
        const procedure=form.elements.namedItem('procedimiento');
        const panel=form.querySelector('[data-retencion-percentage]');
        const percentage=form.elements.namedItem('porcentaje');
        if (!procedure || !panel || !percentage) return;
        const update=()=> {
            const fixed=procedure.value==='2';panel.hidden=!fixed;
            percentage.disabled=!fixed;percentage.required=fixed;
        };
        procedure.addEventListener('change',update);update();
    });
})();
