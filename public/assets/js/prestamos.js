(() => {
    const form=document.querySelector('[data-loan-upload]');if (!form || !window.PdfCarga) return;
    const button=form.querySelector('button[type="submit"]'),status=form.querySelector('[data-loan-status]'),pass=form.querySelector('[data-loan-password]');button.disabled=false;
    form.addEventListener('submit',async event=>{
        event.preventDefault();if (button.disabled) return;button.disabled=true;status.textContent='Abriendo y conciliando el desglose del crédito…';
        try {
            const original=form.elements.prestamo_pdf.files[0],decoded=await PdfCarga.decode(original,pass.value||form.dataset.documento,form.dataset.worker);
            const body=new FormData();body.set('_csrf_token',form.elements._csrf_token.value);body.set('prestamo_pdf',original);body.set('prestamo_lectura',decoded);
            const r=await PdfCarga.request(form.dataset.api,body);status.textContent=r.mensaje;
            await PdfCarga.refresh(['[data-loan-months]']);form.elements.prestamo_pdf.value='';
        } catch (e) { status.textContent=e.message; }finally { button.disabled=false;pass.value=''; }
    });
})();
