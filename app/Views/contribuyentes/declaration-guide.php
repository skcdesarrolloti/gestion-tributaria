<details class="rounded-xl border border-surface-container p-4">
    <summary class="text-headline-sm font-semibold">¿Cómo se organizará la declaración ante la DIAN?</summary>
    <p class="text-body-md text-secondary mt-2">Referencia para preparar el siguiente paso. Todavía no se registran valores ni se asigna un formulario: primero verificaremos año, residencia y régimen. El SIMPLE requiere su propia estructura.</p>
    <div data-declaration-type="natural" <?= $record['tipo'] !== 'natural' ? 'hidden' : '' ?>>
        <h3 class="font-semibold mt-4">Persona natural</h3>
        <p class="text-body-md text-secondary mt-2">En renta ordinaria, el residente utiliza el formulario 210; el no residente, el 110, según su obligación.</p>
        <p class="text-body-md text-secondary mt-2">El 210 separa patrimonio, cédula general (trabajo, capital y no laborales), pensiones, dividendos, ganancias ocasionales y liquidación privada.</p>
    </div>
    <div data-declaration-type="juridica" <?= $record['tipo'] !== 'juridica' ? 'hidden' : '' ?>>
        <h3 class="font-semibold mt-4">Persona jurídica · Referencia: formulario 110</h3>
        <p class="text-body-md text-secondary mt-2">Patrimonio: efectivo, inversiones, cuentas por cobrar, inventarios, intangibles, activos biológicos, propiedades y otros activos; después pasivos y patrimonio líquido.</p>
        <p class="text-body-md text-secondary mt-2">Ingresos: actividades ordinarias, ingresos financieros, dividendos y otros ingresos; total bruto, devoluciones, rebajas y descuentos en ventas, ingresos no constitutivos y total neto.</p>
        <p class="text-body-md text-secondary mt-2">Costos y deducciones: costos, gastos de administración, distribución y ventas, financieros y otros. Luego renta, ganancias ocasionales, impuesto, retenciones, anticipos y saldo.</p>
        <p class="text-body-md text-secondary mt-2">La nómina y los aportes pagados por la entidad se revisan como datos informativos y costos o gastos; no son salarios o pensiones recibidos por la empresa.</p>
    </div>
    <div data-declaration-type="sucesion" <?= $record['tipo'] !== 'sucesion' ? 'hidden' : '' ?>>
        <h3 class="font-semibold mt-4">Sucesión ilíquida</h3>
        <p class="text-body-md text-secondary mt-2">Es el patrimonio de una persona fallecida mientras se completa su adjudicación. No es una sociedad en liquidación.</p>
        <p class="text-body-md text-secondary mt-2">Causante residente fiscal al fallecer: formulario 210. Causante no residente: formulario 110. Revisaremos el período y a quién corresponden los ingresos; no se trasladan automáticamente todos los del fallecido.</p>
    </div>
    <p class="text-body-md text-secondary mt-4">Cada casilla tendrá nombre oficial, explicación, norma aplicable, soportes y regla de cálculo. La referencia no sustituye el análisis del perfil fiscal.</p>
    <p class="text-body-md mt-2">Fuentes: <a class="underline" href="https://www.dian.gov.co/atencionciudadano/formulariosinstructivos/Formularios/2026/Formulario_110_2026.pdf" target="_blank" rel="noopener noreferrer">Formulario 110 e instructivo</a>, <a class="underline" href="https://www.dian.gov.co/atencionciudadano/formulariosinstructivos/Formularios/2026/Formulario_210_2026.pdf" target="_blank" rel="noopener noreferrer">formulario 210</a> y <a class="underline" href="https://micrositios.dian.gov.co/renta-personas-naturales-ag-2025/todo-lo-que-usted-debe-saber-sobre-renta/" target="_blank" rel="noopener noreferrer">guía DIAN sobre sucesiones</a>.</p>
</details>
