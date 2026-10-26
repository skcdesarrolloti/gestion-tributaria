(() => {
  'use strict';
  const form=document.querySelector('[data-nomina-upload]');if (!form) return;
  const config=JSON.parse(document.querySelector('#nomina-config').textContent), files=form.querySelector('[type=file]');
  const results=document.querySelector('#nomina-hojas'), status=document.querySelector('#nomina-estado'), button=form.querySelector('[data-preview]');
  const entries=[], money=n=>(n/100).toLocaleString('es-CO',{minimumFractionDigits:2,maximumFractionDigits:2});
  const td=(tr,text)=>{const e=document.createElement('td');e.textContent=text;tr.append(e);return e;};
  const selection=document.querySelector('#nomina-seleccion'), people=document.querySelector('#nomina-personas');
  const periodsSelect=form.querySelector('[data-nomina-periodos]'), selectedStatus=document.querySelector('#nomina-seleccion-estado');
  function selectionCount() {
    selectedStatus.textContent=`${results.querySelectorAll('input:checked').length} quincenas seleccionadas. Los meses y quincenas que falten quedarán pendientes.`;
  }
  function selectPeriods() {
    const selected=new Set([...people.querySelectorAll('input:checked')].map(c=>Number(c.value)));
    for (const c of results.querySelectorAll('input')) {
      const r=entries[Number(c.dataset.index)];
      c.checked=!c.disabled && !r.conflict && selected.has(r.contribuyente_id) && (periodsSelect.value==='todos' || r.month===1);
    }
    selectionCount();
  }
  people.addEventListener('change',selectPeriods);periodsSelect.addEventListener('change',selectPeriods);results.addEventListener('change',selectionCount);
  let reading=false;
  files.addEventListener('change',async()=>{
    results.replaceChildren();people.replaceChildren();selection.hidden=true;entries.length=0;button.disabled=true;reading=true;const seenFiles=new Set(),seenRows=new Set(),periods=new Set();
    let repeats=0, omitted=0;
    try {
      if (!files.files.length || files.files.length>6) throw Error('Selecciona entre uno y seis archivos XLS o XLSX.');
      status.textContent='Leyendo hojas y períodos…';
      for (const file of files.files) {
        if (file.size>4*1024*1024 || !/\.xlsx?$/i.test(file.name)) throw Error('Cada archivo debe ser XLS/XLSX y pesar hasta 4 MB.');
        const buffer=await file.arrayBuffer(), sha=Array.from(new Uint8Array(await crypto.subtle.digest('SHA-256',buffer))).map(v=>v.toString(16).padStart(2,'0')).join('');
        if (seenFiles.has(sha)) {repeats++;continue;}seenFiles.add(sha);
        const book=XLSX.read(buffer,{type:'array',cellFormula:false,cellHTML:false,cellText:false,sheetRows:2001});
        if (book.SheetNames.length>80) throw Error('El libro contiene demasiadas hojas.');
        for (const name of book.SheetNames) {
          const sheet=book.Sheets[name];if (!sheet['!ref']) continue;
          const dims=XLSX.utils.decode_range(sheet['!ref']);if (dims.e.r>2000 || dims.e.c>79) throw Error('La hoja supera el tamaño permitido.');
          const rows=XLSX.utils.sheet_to_json(sheet,{header:1,raw:true,defval:null,blankrows:true});
          const parsed=NominaParser.parse(rows,config.targets,config.year);if (!parsed.entries.length) {omitted++;continue;}
          const employer=rows.slice(0,5).flat().find(v=>typeof v==='string' && v.trim().length>5 && !/NOMINA|^DEL\b|^LIQUIDACION/i.test(v.trim()));
          if (!employer) throw Error('No se identificó el empleador en la hoja '+name+'.');
          const matching=config.employers.filter(e=>NominaParser.norm(e.nombre)===NominaParser.norm(employer));
          if (matching.length===1 && !form.elements.empleador_documento.value) form.elements.empleador_documento.value=matching[0].nit;
          for (const r of parsed.entries) {
            const signature=JSON.stringify([NominaParser.norm(employer),r.contribuyente_id,r.inicio,r.fin,r.valores]);
            if (seenRows.has(signature)) {repeats++;continue;}seenRows.add(signature);
            const period=`${NominaParser.norm(employer)}|${r.contribuyente_id}|${r.inicio}|${r.fin}`, conflict=periods.has(period);periods.add(period);
            const record={...r,conflict,empleador:String(employer).trim(),archivo:file.name,hoja:name,archivo_sha:sha};entries.push(record);
            const tr=document.createElement('tr'), check=document.createElement('input');check.type='checkbox';check.dataset.index=String(entries.length-1);
            check.checked=!conflict && r.month===1 && r.perfil_id>0;check.disabled=r.perfil_id===0;
            td(tr,'').append(check);check.setAttribute('aria-label',`${r.nombre}, ${r.inicio} a ${r.fin}`);
            td(tr,r.nombre);td(tr,`${r.inicio} a ${r.fin}`);
            const source=td(tr,''), detail=document.createElement('details'), summary=document.createElement('summary'), filename=document.createElement('p');
            summary.textContent=`Hoja: ${name}`;filename.textContent=file.name;detail.append(summary,filename);source.append(detail);
            td(tr,money(r.valores.devengado));td(tr,money(r.valores.neto));
            td(tr,!r.perfil_id?'Prepara el año en Ingresos':conflict?'Otra versión: elige solo una':'Disponible');results.append(tr);
          }
        }
      }
      if (!entries.length) throw Error('No hay filas reconocidas para contribuyentes autorizados en este año. Verifica identificación y encabezado.');
      const ordered=[...results.children].sort((a,b)=>{
        const ra=entries[Number(a.querySelector('input').dataset.index)], rb=entries[Number(b.querySelector('input').dataset.index)];
        return ra.nombre.localeCompare(rb.nombre,'es') || ra.inicio.localeCompare(rb.inicio) || ra.fin.localeCompare(rb.fin);
      });results.replaceChildren(...ordered);
      for (const t of config.targets.filter(t=>entries.some(r=>r.contribuyente_id===t.id))) {
        const label=document.createElement('label'), c=document.createElement('input');c.type='checkbox';c.value=String(t.id);c.checked=t.perfil>0;c.disabled=t.perfil===0;
        label.className='block';label.append(c,document.createTextNode(` ${t.nombre}${t.perfil?'':' · Prepara su año en Ingresos'}`));people.append(label);
      }
      selection.hidden=false;selectPeriods();
      status.textContent=`${entries.length} filas disponibles. ${repeats} archivos/filas repetidos omitidos. ${omitted} hojas sin coincidencia o de otro formato/año. Puedes seleccionar enero o todo lo disponible.`;
      button.disabled=false;
    } catch(e) {status.textContent=e.message;results.replaceChildren();selection.hidden=true;entries.length=0;}
    finally {reading=false;}
  });
  form.addEventListener('submit',e=>{
    if (reading || button.disabled) {e.preventDefault();return;}
    const selected=[...results.querySelectorAll('input:checked')].map(c=>entries[Number(c.dataset.index)]);
    if (new Set(selected.map(r=>NominaParser.norm(r.empleador))).size>1) {e.preventDefault();status.textContent='Importa un solo empleador por carga. Selecciona sus hojas y confirma su NIT.';return;}
    if (!selected.length || selected.length>150 || !/^\d{5,20}$/.test(form.elements.empleador_documento.value.trim())) {e.preventDefault();status.textContent='Selecciona entre una y 150 filas e indica el NIT del empleador sin dígito de verificación.';return;}
    form.elements.filas.value=JSON.stringify(selected.map(r=>({...r,empleador_documento:form.elements.empleador_documento.value.trim()})));
  });
})();
