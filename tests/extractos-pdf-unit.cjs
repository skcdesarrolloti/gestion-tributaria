// PDFs y claves exclusivamente ficticios. No necesita conexión de base de datos.
const fs=require('fs'),path=require('path'),assert=require('assert'),cp=require('child_process');
const root=path.resolve(__dirname,'..');
const create=require('../public/assets/vendor/qpdf-0.0.2/qpdf.js');
const compiled=new WebAssembly.Module(fs.readFileSync(path.join(root,'public/assets/vendor/qpdf-0.0.2/qpdf.wasm')));
async function engine() {
    const log=console.log,warn=console.warn;console.log=()=>{};console.warn=()=>{};
    try { return await create({instantiateWasm:(imports,receive)=>{
        const instance=new WebAssembly.Instance(compiled,imports);receive(instance,compiled);return instance.exports;
    }}); } finally { console.log=log;console.warn=warn; }
}
(async()=>{
    const fixture=path.join(__dirname,'extractos-fixtures.php').replaceAll('\\','/');
    const bytes=cp.execFileSync('php',['-r','require '+JSON.stringify(fixture)+';echo bankFixturePdf();']);
    const e=await engine();e.FS.writeFile('/base.pdf',bytes);
    assert.equal(e.callMain(['--encrypt','Clave-solo-prueba','Propietario-prueba','256','--','/base.pdf','/seguro.pdf']),0);
    const encrypted=e.FS.readFile('/seguro.pdf');
    const wrong=await engine();wrong.FS.writeFile('/in.pdf',encrypted);
    assert.equal(wrong.callMain(['--password=incorrecta','--decrypt','/in.pdf','/out.pdf']),2);
    const reader=await engine();reader.FS.writeFile('/in.pdf',encrypted);
    assert.equal(reader.callMain(['--password=Clave-solo-prueba','--decrypt','/in.pdf','/out.pdf']),0);
    const clear=reader.FS.readFile('/out.pdf');
    const tmp=fs.mkdtempSync(path.join(require('os').tmpdir(),'extracto-wasm-'));
    try {
        const file=path.join(tmp,'leido.pdf');fs.writeFileSync(file,clear);
        const autoload=path.join(root,'bootstrap/autoload.php').replaceAll('\\','/');
        const php='require '+JSON.stringify(autoload)+';echo json_encode(App\\Services\\ExtractoPdfReader::read('+JSON.stringify(file.replaceAll('\\','/'))+'));';
        const result=JSON.parse(cp.execFileSync('php',['-r',php],{encoding:'utf8'}));
        assert.equal(result.estado,'leido');assert.equal(result.inicio,'2025-06-01');assert.equal(result.paginas,2);
        assert.equal(result.totales.pension,100000);
    } finally {fs.rmSync(tmp,{recursive:true,force:true});}
    console.log('Apertura WASM: clave incorrecta rechazada; PDF AES-256 abierto y dos páginas conciliadas sin guardar claves.');
})().catch(()=>{console.error('Falló la prueba de apertura PDF.');process.exitCode=1;});
