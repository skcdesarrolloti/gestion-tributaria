(() => {
    'use strict';
    const form = document.getElementById('renta-sheet');
    if (!form) return;
    const panel = document.getElementById('renta-results');
    const status = document.getElementById('renta-status');
    const pesos = value => '$' + new Intl.NumberFormat('es-CO', {maximumFractionDigits: 0}).format(value);
    let timer, controller, sequence = 0;
    const list = (id, messages) => {
        const target = document.getElementById(id);
        target.replaceChildren(...messages.map(message => {
            const li = document.createElement('li'); li.textContent = message; return li;
        }));
    };
    const clear = () => {
        document.querySelectorAll('[data-result-key]').forEach(node => { node.textContent = 'Pendiente'; });
        ['renta-tax-saving', 'renta-pay-saving'].forEach(id => {
            const node = document.getElementById(id); if (node) node.textContent = 'Pendiente';
        });
        document.getElementById('renta-tariff-detail').textContent = '';
        list('renta-warnings', []);
    };
    const calculate = async () => {
        const current = ++sequence;
        if (controller) controller.abort();
        controller = new AbortController();
        clear(); status.textContent = 'Calculando con los datos actuales…';
        try {
            const response = await fetch(form.dataset.calculate, {method: 'POST', body: new FormData(form),
                signal: controller.signal, headers: {'Accept': 'application/json'}});
            if (!response.headers.get('content-type')?.includes('application/json')) throw new Error('La sesión puede haber vencido. Recarga antes de continuar.');
            const result = await response.json();
            if (current !== sequence) return;
            list('renta-errors', result.errores || []);
            if (!response.ok || result.estado !== 'calculado') {
                status.textContent = 'Cálculo pendiente. No se muestra un saldo final incompleto.'; return;
            }
            document.querySelectorAll('[data-result-key]').forEach(node => {
                let value = result;
                node.dataset.resultKey.split('.').forEach(key => { value = value?.[key]; });
                node.textContent = typeof value === 'number' ? pesos(value) : 'Pendiente';
            });
            const tariff = result.tarifa;
            document.getElementById('renta-tariff-detail').textContent =
                `Tarifa aplicada: ${tariff.tarifa}%; umbral: ${tariff.umbral_uvt} UVT; componente fijo: ${tariff.fijo_uvt} UVT. No se redondean las UVT para elegir el tramo.`;
            for (const [id, field, base] of [['renta-tax-saving','impuesto_neto',panel.dataset.baseTax],
                ['renta-pay-saving','saldo_pagar',panel.dataset.basePay]]) {
                const node = document.getElementById(id);
                if (node && base !== '') node.textContent = pesos(Number(base) - result[field]);
            }
            list('renta-warnings', result.advertencias || []);
            status.textContent = 'Estimación actualizada. Aún no guardada: Guarda una nueva versión para conservarla.';
        } catch (error) {
            if (error.name === 'AbortError' || current !== sequence) return;
            clear(); status.textContent = 'No se pudo recalcular. Se retiró el resultado anterior para evitar confundirlo con el actual.';
            list('renta-errors', [error.message]);
        }
    };
    form.addEventListener('input', () => {
        ++sequence; if (controller) controller.abort();
        clearTimeout(timer); clear(); status.textContent = 'Datos modificados; pendiente de recalcular.';
        timer = setTimeout(calculate, 500);
    });
    form.addEventListener('change', () => { ++sequence; if (controller) controller.abort(); clear(); clearTimeout(timer); timer = setTimeout(calculate, 100); });
    document.getElementById('recalculate-renta').addEventListener('click', () => { clearTimeout(timer); calculate(); });
    let closedForPrint = [];
    window.addEventListener('beforeprint', () => {
        closedForPrint = [...document.querySelectorAll('details:not([open])')];
        closedForPrint.forEach(node => { node.open = true; });
    });
    window.addEventListener('afterprint', () => { closedForPrint.forEach(node => { node.open = false; }); });
    document.getElementById('print-renta').addEventListener('click', () => window.print());
    calculate();
})();
