(() => {
    'use strict';
    const paragraph = (text, className = '') => {
        const p = document.createElement('p'); p.textContent = text;
        if (className) p.className = className;
        return p;
    };
    const load = async (panel) => {
        if (panel.dataset.loaded || panel.dataset.loading) return;
        panel.dataset.loading = '1';
        const body = panel.querySelector('[data-norma-body]');
        body.replaceChildren(paragraph('Cargando artículo…'));
        try {
            const response = await fetch(panel.dataset.normaUrl, {credentials: 'same-origin', redirect: 'error', headers: {Accept: 'application/json'}});
            if (!response.ok) throw new Error('Artículo no disponible.');
            const article = await response.json();
            if (!Array.isArray(article.paragraphs) || !article.paragraphs.length) throw new Error('Texto no disponible.');
            const text = document.createElement('div'); text.className = 'norma-texto space-y-space-sm border border-surface-container rounded-xl';
            text.tabIndex = 0; text.setAttribute('role', 'region'); text.setAttribute('aria-label', article.label);
            article.paragraphs.forEach(line => text.append(paragraph(line)));
            const note = paragraph(`Compilación DIAN consultada: ${article.reviewed}. Verifica su vigencia para el año seleccionado. Los textos entre <…> son anotaciones de la compilación. No incluye ventanas de jurisprudencia ni versiones anteriores.`, 'text-label-sm text-secondary');
            const source = document.createElement('details');
            const sourceTitle = document.createElement('summary'); sourceTitle.textContent = 'Fuente de este texto';
            source.append(sourceTitle, paragraph(article.source, 'text-label-sm text-secondary'));
            const close = document.createElement('button'); close.type = 'button'; close.className = 'text-secondary underline'; close.textContent = 'Cerrar artículo';
            close.addEventListener('click', () => {panel.open = false; panel.querySelector('summary').focus();});
            body.replaceChildren(note, text, source, close); panel.dataset.loaded = '1';
        } catch {
            const retry = document.createElement('button'); retry.type = 'button'; retry.className = 'text-secondary underline'; retry.textContent = 'Reintentar';
            retry.addEventListener('click', () => load(panel));
            body.replaceChildren(paragraph('No se pudo mostrar el artículo. Comprueba tu sesión y vuelve a intentarlo.'), retry);
        } finally {delete panel.dataset.loading;}
    };
    document.querySelectorAll('[data-norma-url]').forEach(panel => panel.addEventListener('toggle', () => {
        if (!panel.open) return;
        [...panel.parentElement.children].filter(sibling => sibling !== panel && sibling.matches('[data-norma-url]')).forEach(sibling => {sibling.open = false;});
        load(panel);
    }));
})();
