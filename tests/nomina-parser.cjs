const assert=require('node:assert/strict');
const parser=require('../public/assets/js/nomina-parser.js');
const targets=[{id:1,documento:'999111222',nombre:'Persona ficticia',perfil:1}];
const headers=['No.','IDENTIFICACION','NOMBRE DEL TRABAJADOR','BASICO','RETRO\nACTIVOS','SOBRESUELDO','RETROACTIVOS','DIAS','SALARIO','INCAPACIDAD','SUB TRANS','DEVENGADO','PENSION','SALUD','RETEFUENTE','DESCUENTOS','DEDUCIDO','NETO'];
const rows=[['EMPRESA FICTICIA SAS'],['DEL 1 AL 15 DE ENERO DE 2026'],[],headers,
 [1,999111222,'NOMBRE DE ORIGEN',20000000,100,200,300,15,10000000,0,0,10000600,400000,400000,500000,0,1300000,8700600],
 [2,999111333,'OTRO EMPLEADO',5000000,0,0,0,15,2500000,0,0,2500000,100000,100000,0,0,200000,2300000]];
const p=parser.parse(rows,targets,'2026');assert.equal(p.entries.length,1);assert.equal(p.unmatched,1);
assert.equal(p.entries[0].valores.salario,1000000000);assert.equal(p.entries[0].valores.retroactivos,40000);
assert.equal(p.entries[0].valores.sobresueldo,20000);assert.equal(p.entries[0].valores.comisiones,0);
assert.equal(p.entries[0].inicio,'2026-01-01');assert.equal(p.entries[0].fin,'2026-01-15');
assert.equal(parser.parse(rows,targets,'2025').entries.length,0);
assert.equal(parser.parse([['LIQUIDACION DE PRIMAS 2025']],targets,'2026').entries.length,0);
assert.throws(()=>parser.parse(rows,[...targets,...targets],'2026'));
const bad=structuredClone(rows);bad[4][13]='400.000';assert.throws(()=>parser.parse(bad,targets,'2026'));
console.log('Adaptador nómina: período, columnas móviles, retroactivos dobles, identidad, datos ajenos, básico y sobresueldo correctos.');
