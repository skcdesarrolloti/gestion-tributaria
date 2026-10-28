(() => {
    const form=document.querySelector('[data-extracto-form]');
    if (!form) return;
    const input=form.querySelector('[name="extracto_pdf"]');
    const decoded=form.querySelector('[name="extracto_lectura"]');
    const password=form.querySelector('[data-pdf-password]');
    const custom=form.querySelector('[data-pdf-custom]');
    const unlock=form.querySelector('[data-pdf-unlock]');
    const status=form.querySelector('[data-pdf-status]');
    const submit=form.querySelector('button[type="submit"]');
    let ready=false,busy=false;
    custom.addEventListener('change',()=>{password.disabled=!custom.checked;password.value='';});
    input.addEventListener('change',()=>{ready=false;decoded.value='';status.textContent='';});
    form.addEventListener('submit',async event => {
        if (ready || !unlock.checked) return;
        event.preventDefault();
        if (busy) return;
        const original=input.files[0];
        if (!original || original.size>10485760) { status.textContent='Selecciona un PDF de hasta 10 MB.';return; }
        if (custom.checked && !password.value) { status.textContent='Escribe la otra contraseña del PDF o desmarca esa opción para usar el documento de la ficha.';return; }
        busy=true;submit.disabled=true;status.textContent='Abriendo el PDF protegido antes de leer el mes…';
        let worker;
        try {
            const bytes=await original.arrayBuffer();
            const key=custom.checked?password.value:form.dataset.documento;
            password.value='';
            const result=await new Promise((resolve,reject)=>{
                worker=new Worker(form.dataset.worker);
                const timer=setTimeout(()=>{worker.terminate();reject(new Error('Tiempo agotado.'));},45000);
                worker.onmessage=({data})=>{clearTimeout(timer);resolve(data);};
                worker.onerror=()=>{clearTimeout(timer);reject(new Error('Lector no disponible.'));};
                worker.postMessage({bytes,password:key},[bytes]);
            });
            if (!result.ok) { status.textContent='No se pudo abrir con esa clave. Escribe la contraseña del PDF y vuelve a guardar. No se ha enviado ni guardado el documento.';return; }
            if (input.files[0]!==original) { status.textContent='El archivo cambió durante la apertura. Vuelve a guardar el PDF seleccionado.';return; }
            const transfer=new DataTransfer();
            transfer.items.add(new File([result.bytes],'lectura.pdf',{type:'application/pdf'}));
            decoded.files=transfer.files;ready=true;
            status.textContent='PDF abierto. Guardando y detectando el mes…';
        } catch (_) { status.textContent='No fue posible preparar la lectura. Vuelve a intentar; si el PDF no tiene clave, desmarca la apertura protegida.'; }
        finally { if (worker) worker.terminate();busy=false;submit.disabled=false; }
        if (ready) form.requestSubmit(submit);
    });
})();
