(() => {
    'use strict';
    const form = document.getElementById('caracterizacion-form');
    if (!form) return;
    let dirty = false;
    const mark = () => { dirty = true; document.getElementById('profile-save-status').textContent = 'Cambios sin guardar. Pulsa Guardar bloque antes de cambiar de sección.'; };
    form.addEventListener('input', mark);
    form.addEventListener('change', mark);
    form.addEventListener('change', event => {
        if (event.target.tagName !== 'SELECT') return;
        const description = document.getElementById(event.target.id + '-descripcion');
        if (description) description.textContent = event.target.selectedOptions[0]?.textContent || 'Por confirmar';
    });
    form.addEventListener('submit', () => { dirty = false; });
    window.addEventListener('beforeunload', event => { if (dirty) { event.preventDefault(); event.returnValue = ''; } });
    const cachedOptions = new WeakMap();
    const onlyEmpty = document.getElementById('profile-only-empty');
    const filterEmpty = () => {
        form.querySelectorAll('[data-profile-field]').forEach(wrapper => {
            const field = wrapper.querySelector('select[name], textarea[name], input[name]');
            const role = wrapper.closest('[data-profile-row]')?.querySelector('select[name$="[rol]"]')?.value;
            const filled = Boolean(field && field.value.trim() !== '');
            const irrelevant = wrapper.dataset.fieldRoles && role && role !== 'otro' && !wrapper.dataset.fieldRoles.split(',').includes(role);
            wrapper.hidden = Boolean((onlyEmpty?.checked && filled) || (irrelevant && !filled));
        });
        form.querySelectorAll('[data-profile-group]').forEach(group => {
            if (onlyEmpty?.checked) group.open = true;
        });
    };
    onlyEmpty?.addEventListener('change', filterEmpty);
    form.addEventListener('change', event => { if (event.target.name?.endsWith('[rol]')) filterEmpty(); });
    filterEmpty();
    form.addEventListener('input', event => {
        const id = event.target.dataset.filterSelect;
        if (!id) return;
        const select = document.getElementById(id);
        if (!select) return;
        if (!cachedOptions.has(select)) cachedOptions.set(select, [...select.options].map(o => ({value: o.value, text: o.text})));
        const normalize = s => s.normalize('NFD').replace(/[\u0300-\u036f]/g, '').toLowerCase();
        const query = normalize(event.target.value), current = select.value;
        select.replaceChildren(...cachedOptions.get(select).filter(o => !o.value || o.value === current || normalize(o.text).includes(query))
            .map(o => new Option(o.text, o.value, false, o.value === current)));
    });
    const add = document.getElementById('add-profile-row');
    if (add) add.addEventListener('click', () => {
        if (form.querySelectorAll('[data-profile-row]').length >= 100) return;
        const fragment = document.getElementById('profile-row-template').content.cloneNode(true);
        const index = add.dataset.next; add.dataset.next = String(Number(index) + 1);
        fragment.querySelectorAll('*').forEach(node => {
            ['id','name','for','aria-describedby','data-filter-select','data-selected-label'].forEach(attr => {
                if (node.hasAttribute(attr)) node.setAttribute(attr, node.getAttribute(attr).replaceAll('__INDEX__', index));
            });
        });
        document.getElementById('profile-rows').append(fragment); filterEmpty(); mark();
    });
    form.addEventListener('click', event => {
        const remove = event.target.closest('[data-remove-profile-row]');
        if (remove) { remove.closest('[data-profile-row]').remove(); mark(); }
    });
})();
