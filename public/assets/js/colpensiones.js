(() => {
    const root=document.querySelector('[data-colpensiones]');if (!root || !window.PdfCarga) return;
    const {request,decode,refresh,node}=window.PdfCarga;
    const upload=root.querySelector('[data-pension-upload]'),status=root.querySelector('[data-pension-status]');
    const preview=root.querySelector('[data-pension-preview]');let busy=false;
    const money=value=>'$'+Number(value).toLocaleString('es-CO',{maximumFractionDigits:0});
    const input=(label,type,value) => {
        const wrap=node('label',label,'block'),field=node('input');field.type=type;field.required=true;
        field.className='w-full bg-surface-container-low rounded-xl p-2';field.value=value;wrap.append(field);return {wrap,field};
    };
    const resultPanel=result => {
        const panel=node('div',undefined,'space-y-space-sm');panel.dataset.pensionResult='';
        panel.append(node('p','Mesadas guardadas. Se actualizaron 99 y 100 sin duplicar el neto del banco.'));
        if (result.estado!=='calculado') { panel.append(node('p',(result.errores||[]).join(' ')));return panel; }
        panel.append(node('p','Impuesto provisional con salarios y pensiones registrados: '+money(result.impuesto_neto)+'. Retenciones de nómina: '+money(result.retenciones)+'. Por cubrir: '+money(result.por_cubrir)+'.','font-semibold'));
        const help=node('details');help.append(node('summary','? · De dónde sale y qué falta','text-secondary underline'));
        help.append(node('p','Salarios '+money(result.bruto_laboral)+' − aportes '+money(result.aportes)+' − exención laboral simulada '+money(result.deducciones.exencion25)+'. Pensión bruta '+money(result.pension_bruta)+' − aportes '+money(result.pension_salud)+' − exención pensional simulada '+money(result.pension_exenta)+'.'));
        help.append(node('p','Base '+money(result.base_gravable)+'. UVT del año '+money(result.uvt)+'. Tarifa anual ET 241: '+result.tarifa.tarifa+'%. Es un escenario con lo registrado, no el saldo final de declaración.'));
        for (const text of result.pendientes) help.append(node('p',text));panel.append(help);return panel;
    };
    const show=data => {
        preview.replaceChildren();preview.hidden=false;
        preview.append(node('h3',data.titulo,'font-semibold'),node('p',data.lectura.motivo));
        const pdf=node('object');pdf.type='application/pdf';pdf.data=data.pdf;pdf.className='bank-document';pdf.setAttribute('aria-label','Comprobante de Colpensiones guardado');
        const form=node('form',undefined,'space-y-space-md');form.dataset.pensionConfirm='';let versions=data.versiones;
        const rows=data.lectura.registros.length?data.lectura.registros:[data.lectura.campos_sin_periodo||{}];const fields=[];
        for (const row of rows) {
            const grid=node('div',undefined,'profile-grid');const monthLabel=node('label','Mes del comprobante','block');
            const month=node('select');month.className='w-full bg-surface-container-low rounded-xl p-2';month.required=true;
            const names=['Selecciona','Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];
            names.forEach((name,index)=>{const option=node('option',name);option.value=index?String(index):'';month.append(option);});
            month.value=row.periodo?String(Number(row.periodo.slice(5,7))):'';monthLabel.append(month);
            const gross=input('Pensión bruta · casilla 99 (COP)','text',row.bruto==null?'':(row.bruto/100).toFixed(2).replace('.',','));
            const health=input('Salud obligatoria · casilla 100 (COP)','text',row.salud==null?'':(row.salud/100).toFixed(2).replace('.',','));
            gross.field.inputMode='decimal';health.field.inputMode='decimal';grid.append(monthLabel,gross.wrap,health.wrap);form.append(grid);
            const extra=node('details'),extraGrid=node('div',undefined,'profile-grid');extra.append(node('summary','Libranza y neto del comprobante · para cruzar con el crédito y el banco','text-secondary underline'));
            const optional={};for (const [key,label] of [['libranza','Libranza descontada (COP)'],['neto','Neto a consignar (COP)']]) {
                const f=input(label,'text',row[key]==null?'':(row[key]/100).toFixed(2).replace('.',','));f.field.required=false;f.field.inputMode='decimal';optional[key]=f.field;extraGrid.append(f.wrap);
            }
            extra.append(extraGrid,node('p','Completa solo lo que informe Colpensiones. Vacío queda pendiente; la libranza no se suma a salud ni reduce otra vez la pensión bruta.'));form.append(extra);
            fields.push({month,gross:gross.field,health:health.field,...optional});
        }
        const review=node('label',undefined,'block'),check=node('input');check.type='checkbox';check.required=true;
        review.append(check,document.createTextNode(' Revisé el titular, los meses y los valores en este comprobante.'));
        const replace=node('label',undefined,'block'),replacement=node('input');replacement.type='checkbox';
        replace.append(replacement,document.createTextNode(' Corregir meses ya importados, conservando historial.'));
        const submit=node('button','Guardar meses y actualizar cálculo','rounded-xl bg-inverse-surface text-on-secondary px-4 py-2');submit.type='submit';
        if (data.lectura.anual_sin_desglose || data.lectura.revision_particular || rows.some(row=>row.especial)) { submit.disabled=true;form.append(node('p','Este documento necesita revisión particular. No se importa automáticamente ni se divide el total anual.')); }
        form.append(review,replace,submit);preview.append(form,pdf);
        form.addEventListener('submit',async event=>{
            event.preventDefault();if (busy) return;busy=true;submit.disabled=true;status.textContent='Guardando los meses y recalculando…';
            try {
                const body=new FormData();body.set('_csrf_token',upload.elements._csrf_token.value);body.set('accion','guardar');
                body.set('comprobante',String(data.id));body.set('confirmado',check.checked?'1':'0');body.set('reemplazar',replacement.checked?'1':'0');
                body.set('filas',JSON.stringify(fields.map(f=>({mes:f.month.value,bruto:f.gross.value,salud:f.health.value,libranza:f.libranza.value,neto:f.neto.value}))));body.set('versiones',JSON.stringify(versions));
                const saved=await request(root.dataset.api,body);
                const previous=preview.querySelector('[data-pension-result]');if (previous) previous.remove();preview.prepend(resultPanel(saved.impuesto));
                await refresh(['#meses','[data-colpensiones-list]','[data-casillas-section]','[data-registro-movimientos]']);
                const latest=await request(root.dataset.api+'&comprobante='+data.id);versions=latest.versiones;check.checked=false;
                status.textContent='Guardado. Los acumulados y el cálculo están actualizados aquí.';
            } catch (error) { status.textContent=error.message; }
            finally { busy=false;submit.disabled=false; }
        });
    };
    upload.querySelector('button[type="submit"]').disabled=false;
    upload.addEventListener('submit',async event=>{
        event.preventDefault();if (busy) return;busy=true;const button=upload.querySelector('button[type="submit"]');button.disabled=true;
        let password=upload.querySelector('[data-pension-password]').value||upload.dataset.documento;
        status.textContent='Abriendo y leyendo el comprobante. Todavía no se crean ingresos…';
        try {
            const original=upload.elements.comprobante_pdf.files[0];const decoded=await decode(original,password,upload.dataset.worker);
            const body=new FormData();body.set('_csrf_token',upload.elements._csrf_token.value);body.set('comprobante_pdf',original);body.set('comprobante_lectura',decoded);
            const data=await request(root.dataset.api,body);show(data);await refresh(['[data-colpensiones-list]']);
            status.textContent='PDF conservado. Revisa los datos y confirma abajo para completar el cálculo.';
        } catch (error) { status.textContent=error.message; }
        finally { password='';upload.querySelector('[data-pension-password]').value='';busy=false;button.disabled=false; }
    });
    document.addEventListener('click',async event=>{
        const button=event.target.closest('[data-pension-open]');if (!button || busy) return;
        busy=true;status.textContent='Abriendo el comprobante guardado…';
        try { show(await request(root.dataset.api+'&comprobante='+button.dataset.pensionOpen));status.textContent='Comprobante abierto en este mismo panel.';root.scrollIntoView({block:'start',behavior:'smooth'}); }
        catch (error) { status.textContent=error.message; }finally { busy=false; }
    });
})();
