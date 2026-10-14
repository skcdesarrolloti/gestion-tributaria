(() => {
    document.querySelectorAll('select[data-guidance]').forEach(select => {
        const options = JSON.parse(select.dataset.guidance);
        const help = document.querySelector(`[data-help-for="${select.id}"]`);
        if (!help) return;
        const update = () => {
            const selected = options[select.value];
            help.querySelector('[data-help-empty]').hidden = Boolean(selected);
            help.querySelector('[data-help-content]').hidden = !selected;
            if (selected) {
                help.querySelector('[data-help-definition]').textContent = selected.definicion;
                help.querySelector('[data-help-scope]').textContent = selected.alcance;
                const link = help.querySelector('[data-help-source]');
                link.textContent = selected.norma;
                link.href = selected.url;
            }
            if (select.id === 'regimen') {
                const blocked = Boolean(select.value && select.value !== 'ordinario');
                document.getElementById('create-profile').disabled = blocked;
                document.getElementById('regimen-availability').textContent = blocked
                    ? 'Esta opción es solo de consulta. Su cálculo está pendiente y no se puede crear un perfil con reglas del ordinario.'
                    : 'Ordinario: depuración inicial disponible. La elección no certifica la condición ni la procedencia de beneficios.';
            }
        };
        select.addEventListener('change', update);
        update();
    });
})();
