/* PDF y clave solo viven en la memoria de este worker hasta su terminación. */
console.log=()=>{};console.warn=()=>{};console.error=()=>{};
importScripts('../vendor/qpdf-0.0.2/qpdf.js');
self.onmessage = async ({data}) => {
    try {
        const engine = await Module({noInitialRun:true,
            locateFile:file => new URL('../vendor/qpdf-0.0.2/'+file,self.location.href).href,
            print:()=>{},printErr:()=>{}});
        engine.FS.writeFile('/entrada.pdf',new Uint8Array(data.bytes));
        let code;
        try { code=engine.callMain(['--password='+data.password,'--decrypt','/entrada.pdf','/lectura.pdf']); }
        catch (error) { code=typeof error.status==='number'?error.status:2; }
        data.password='';
        if (code!==0 && code!==3) { throw new Error('No se pudo abrir.'); }
        const output=engine.FS.readFile('/lectura.pdf');
        if (!output.length || output.length>10485760) { throw new Error('Tamaño fuera de alcance.'); }
        self.postMessage({ok:true,bytes:output.buffer},[output.buffer]);
    } catch (_) { self.postMessage({ok:false}); }
};
