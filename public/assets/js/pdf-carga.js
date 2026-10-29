(() => {
    const request=async (url,body) => {
        const response=await fetch(url,{method:body?'POST':'GET',body,credentials:'same-origin'});
        let result;
        try { result=await response.json(); } catch (_) { throw new Error('No se recibió una respuesta válida. Revisa la sesión y el tamaño del PDF; tus archivos seleccionados se conservan aquí.'); }
        if (!response.ok) { const error=new Error(result.error||'No se pudo completar la carga.');error.data=result;error.status=response.status;throw error; }
        return result;
    };
    const decode=async (file,password,workerUrl) => {
        if (!file || file.size>10485760) throw new Error('Selecciona un PDF de hasta 10 MB.');
        const bytes=await file.arrayBuffer();let worker;
        try {
            const result=await new Promise((resolve,reject)=>{
                worker=new Worker(workerUrl);const timer=setTimeout(()=>reject(new Error('La apertura superó el tiempo disponible.')),45000);
                worker.onmessage=({data})=>{clearTimeout(timer);resolve(data);};
                worker.onerror=()=>{clearTimeout(timer);reject(new Error('No se pudo preparar el lector del PDF.'));};
                worker.postMessage({bytes,password},[bytes]);
            });
            if (!result.ok) throw new Error('No se pudo abrir el PDF. Revisa la contraseña y vuelve a intentarlo.');
            return new File([result.bytes],'lectura.pdf',{type:'application/pdf'});
        } finally { if (worker) worker.terminate();password=''; }
    };
    const refresh=async selectors => {
        const url=new URL(location.href);url.hash='';
        const response=await fetch(url,{credentials:'same-origin'});
        if (!response.ok || response.redirected) throw new Error('El guardado terminó, pero falta actualizar los acumulados. Reabre esta misma sección.');
        const doc=new DOMParser().parseFromString(await response.text(),'text/html');
        for (const selector of selectors) {
            const current=document.querySelector(selector),fresh=doc.querySelector(selector);
            if (current && fresh) current.replaceWith(fresh);
        }
    };
    const node=(tag,text,className) => {
        const el=document.createElement(tag);if (text!==undefined) el.textContent=text;if (className) el.className=className;return el;
    };
    window.PdfCarga={request,decode,refresh,node};
})();
