(() => {
    if (!window.PdfCarga) return;
    const {request,decode,refresh,node}=window.PdfCarga,states=new WeakMap();
    document.addEventListener('change',event=>{
        const form=event.target.closest('[data-extracto-form]');if (!form) return;
        if (event.target.matches('[data-pdf-custom]')) { const pass=form.querySelector('[data-pdf-password]');pass.disabled=!event.target.checked;pass.value=''; }
        if (event.target.matches('[data-bank-files]')) { states.delete(form);form.querySelector('[data-bank-results]').replaceChildren(); }
    });
    const metadata=(form,row)=>{
        if (row.fields) return;
        const grid=node('div',undefined,'profile-grid');row.fields={};
        for (const key of ['banco','cuenta','mes']) {
            const label=node('label',key==='banco'?'Banco de este PDF':(key==='cuenta'?'Últimos cuatro dígitos':'Mes de este PDF'),'block');
            const field=node(key==='mes'?'select':'input');field.className='w-full bg-surface-container-low rounded-xl p-2';
            if (key==='mes') {
                const names=['Selecciona','Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];
                names.forEach((text,n)=>{const option=node('option',text);option.value=n?String(n):'auto';field.append(option);});
            } else { field.type='text';field.maxLength=key==='cuenta'?4:80; }
            field.value=form.elements[key].value;row.fields[key]=field;label.append(field);grid.append(label);
        }
        row.wrap.append(grid);
    };
    document.addEventListener('submit',async event=>{
        const form=event.target.closest('[data-extracto-form]');if (!form) return;event.preventDefault();
        const input=form.querySelector('[data-bank-files]'),status=form.querySelector('[data-pdf-status]');
        const files=Array.from(input.files);if (!files.length) return;
        if (files.length>50 || files.reduce((sum,f)=>sum+f.size,0)>104857600) { status.textContent='Selecciona hasta 50 PDF y 100 MB por lote; puedes continuar con otro lote después.';return; }
        let state=states.get(form);if (state?.busy) return;
        if (!state) {
            const list=form.querySelector('[data-bank-results]');list.replaceChildren();
            state={busy:false,rows:files.map(file=>{const wrap=node('li',undefined,'border border-surface-container rounded-xl p-3');const message=node('p',file.name+' · Pendiente');wrap.append(message);list.append(wrap);return {file,wrap,message,done:false,decoded:null};})};states.set(form,state);
        }
        const button=form.querySelector('button[type="submit"]'),pass=form.querySelector('[data-pdf-password]');
        let key=form.querySelector('[data-pdf-custom]').checked?pass.value:form.dataset.documento;
        const needsKey=form.querySelector('[data-pdf-unlock]').checked && state.rows.some(row=>!row.done && !row.decoded);
        if (needsKey && form.querySelector('[data-pdf-custom]').checked && !key) { status.textContent='Escribe la contraseña del PDF o usa el documento de la ficha.';return; }
        state.busy=true;button.disabled=true;input.disabled=true;let saved=0,duplicates=0;
        try {
            for (let i=0;i<state.rows.length;i++) {
                const row=state.rows[i];if (row.done) continue;
                status.textContent='Procesando '+(i+1)+' de '+state.rows.length+'…';row.message.textContent=row.file.name+' · Abriendo…';
                try {
                    if (form.querySelector('[data-pdf-unlock]').checked && !row.decoded) row.decoded=await decode(row.file,key,form.dataset.worker);
                    const body=new FormData();body.set('_csrf_token',form.elements._csrf_token.value);body.set('extracto_pdf',row.file);
                    if (row.decoded) body.set('extracto_lectura',row.decoded);
                    for (const name of ['banco','cuenta','mes']) body.set(name,row.fields?row.fields[name].value:form.elements[name].value);
                    body.set('titular_confirmado',form.elements.titular_confirmado.checked?'1':'0');
                    const result=await request(form.dataset.batchUrl,body);row.done=true;row.decoded=null;saved++;
                    const period=result.meses?.length>1?'meses '+result.meses.join(', '):'mes '+result.mes;
                    row.message.textContent=row.file.name+' · Guardado · '+result.banco+' '+result.cuenta+' · '+period+' · '+(result.tipo==='credito'?'desglose disponible en Control de préstamos':(result.leido?'leído':'pendiente de lectura'));
                    if (row.fields) row.wrap.querySelector('.profile-grid').remove();
                } catch (error) {
                    if (error.data?.duplicado) { row.done=true;row.decoded=null;duplicates++;row.message.textContent=row.file.name+' · Ya estaba guardado; no se duplicó.'; }
                    else { row.message.textContent=row.file.name+' · '+error.message;if (error.status===422 && /mes|banco|cuenta|período/i.test(error.message)) metadata(form,row);if ([401,403,419].includes(error.status)) break; }
                }
            }
            if (saved) await refresh(['#meses','[data-credit-documents]']);
            const pending=state.rows.filter(row=>!row.done).length;
            status.textContent='Este intento: '+saved+' guardados; '+duplicates+' ya existentes; '+pending+' pendientes. '+(pending?'Completa solo los datos que faltan debajo del PDF y pulsa otra vez «Subir seleccionados».':'Acumulados actualizados en esta misma página.');
        } catch (error) { status.textContent=error.message; }
        finally { key='';pass.value='';state.busy=false;button.disabled=false;input.disabled=false; }
    });
})();
