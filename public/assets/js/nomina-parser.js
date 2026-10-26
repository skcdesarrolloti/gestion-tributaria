/* Adaptador del listado SKC. No ejecuta macros ni fórmulas de los libros. */
(function (root) {
  'use strict';
  const norm = s => String(s ?? '').normalize('NFD').replace(/[\u0300-\u036f]/g, '').toUpperCase().replace(/[^A-Z0-9]/g, '');
  const months = ['ENERO','FEBRERO','MARZO','ABRIL','MAYO','JUNIO','JULIO','AGOSTO','SEPTIEMBRE','OCTUBRE','NOVIEMBRE','DICIEMBRE'];
  const fields = {SALARIO:'salario',RETROACTIVOS:'retroactivos',SOBRESUELDO:'sobresueldo',COMISIONES:'comisiones',
    INCAPACIDAD:'incapacidad',SUBTRANS:'auxilio',DEVENGADO:'devengado',PENSION:'pension',SALUD:'salud',
    FONDOSOLIDARIDAD:'solidaridad',SOLIDARIDAD:'solidaridad',RETEFUENTE:'retencion',DESCUENTOS:'otros_descuentos',DEDUCIDO:'deducido',NETO:'neto'};
  function period(rows) {
    const texts = rows.slice(0,8).flat().map(v => String(v ?? '').toUpperCase().replace(/\s+/g, ' '));
    for (const text of texts) {
      const m = text.match(/DEL\s+(\d{1,2})\s+AL\s+(\d{1,2})\s+DE\s+([A-Z]+)\s+DE\s+(20\d{2})/);
      if (!m) continue;
      const month = months.indexOf(m[3]) + 1;
      if (!month) throw Error('Mes no reconocido en el encabezado.');
      const date = d => `${m[4]}-${String(month).padStart(2,'0')}-${String(d).padStart(2,'0')}`;
      return {inicio:date(m[1]),fin:date(m[2]),year:m[4],month};
    }
    return null;
  }
  function parse(rows, targets, year) {
    if (rows.length>2000 || rows.some(r=>r.length>80)) throw Error('Hoja demasiado grande para este formato.');
    const dates=period(rows);if (!dates || dates.year!==year) return {entries:[],reason:'Sin encabezado quincenal del año seleccionado'};
    const h=rows.findIndex(r=>r.some(c=>norm(c)==='IDENTIFICACION') && r.some(c=>norm(c)==='DEVENGADO'));
    if (h<0 || h>15) return {entries:[],reason:'Formato diferente: requiere otro adaptador'};
    const header=rows[h].map(norm), doc=header.indexOf('IDENTIFICACION'), name=header.indexOf('NOMBREDELTRABAJADOR');
    for (const required of ['SALARIO','DEVENGADO','PENSION','SALUD','RETEFUENTE','DESCUENTOS','DEDUCIDO','NETO']) {
      if (!header.includes(required)) throw Error(`Falta la columna ${required}.`);
    }
    const entries=[];let unmatched=0;
    for (let i=h+1;i<rows.length;i++) {
      const row=rows[i], rawDoc=row[doc];if (rawDoc==='' || rawDoc===null || rawDoc===undefined) continue;
      const document=typeof rawDoc==='number' && Number.isSafeInteger(rawDoc)?String(rawDoc):String(rawDoc).trim();
      if (!/^\d{5,20}$/.test(document)) continue;
      const matches=targets.filter(t=>t.documento===document);
      if (!matches.length) {unmatched++;continue;}
      if (matches.length!==1) throw Error('Hay más de un contribuyente con la misma identificación. Revisa las fichas.');
      const t=matches[0], values=Object.fromEntries([...new Set(Object.values(fields))].map(k=>[k,0]));
      for(let j=0;j<header.length;j++) {
        const key=fields[header[j]];if (!key) continue;
        const v=row[j];if (v==='' || v===null || v===undefined) continue;
        if (typeof v!=='number' || !Number.isFinite(v) || v<0 || v>99999999999) throw Error(`Importe inválido en fila ${i+1}, columna ${j+1}.`);
        values[key]+=Math.round((v+Number.EPSILON)*100);
      }
      entries.push({...dates,documento:document,nombre:t.nombre,nombre_origen:String(row[name]??''),contribuyente_id:t.id,perfil_id:t.perfil,valores:values,fila:i+1});
    }
    return {entries,unmatched,reason:entries.length?'':'Sin contribuyentes autorizados coincidentes'};
  }
  root.NominaParser={parse,norm,period};
  if (typeof module!=='undefined') module.exports=root.NominaParser;
})(typeof window==='undefined'?globalThis:window);
