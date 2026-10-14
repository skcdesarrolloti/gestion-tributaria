(() => {
    const type = document.getElementById('tipo');
    if (!type) return;
    const containers = [...document.querySelectorAll('[data-source-labels]')];
    const descriptions = {
        natural: 'Marca las fuentes de la persona. Salarios, pensión, servicios y arriendos pueden coexistir. La residencia y el régimen determinarán el formulario.',
        juridica: 'Marca las actividades de la entidad. Los salarios y pensiones personales no son ingresos de la empresa. La referencia de renta es el formulario 110; se verificará el régimen.',
        sucesion: 'Identifica las fuentes a revisar para el período del causante y la sucesión. Su residencia al fallecer determina el uso del 210 o del 110; el tratamiento de cada ingreso debe verificarse.',
    };
    const refresh = () => {
        let excludedSelection = false;
        containers.forEach(container => {
            const labels = JSON.parse(container.dataset.sourceLabels);
            const checkbox = container.querySelector('input');
            const applicable = Boolean(labels[type.value]);
            excludedSelection ||= checkbox.checked && !applicable;
            container.hidden = !applicable;
            checkbox.disabled = !applicable;
            if (applicable) container.querySelector('[data-source-label]').textContent = labels[type.value];
        });
        document.getElementById('sources-help').textContent = descriptions[type.value]
            || 'Selecciona el tipo de contribuyente para ver sus fuentes de ingresos.';
        document.getElementById('sources-change').hidden = !excludedSelection;
        document.querySelectorAll('[data-declaration-type]').forEach(section => {
            section.hidden = section.dataset.declarationType !== type.value;
        });
    };
    type.addEventListener('change', refresh);
    refresh();
})();
