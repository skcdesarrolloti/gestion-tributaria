<?php $title='Seguridad social · Revisión'; require __DIR__.'/../partials/head.php'; ?>
<body class="bg-background min-h-screen font-body-md text-on-surface antialiased">
<?php require __DIR__.'/../partials/header.php'; ?>
<main class="portal-container space-y-space-lg py-8">
    <section class="bg-inverse-surface text-on-secondary rounded-2xl p-space-xl space-y-space-sm">
        <h1 class="text-headline-lg font-semibold">Seguridad social</h1>
        <p>Revisión del aplicativo recibido para rentas mixtas · 21 de octubre de 2026.</p>
    </section>
    <section class="bg-primary-container rounded-xl p-4 space-y-space-sm">
        <h2 class="text-headline-sm font-semibold">La calculadora necesita ajustes antes de integrarse</h2>
        <p>Se aprovechará su separación de salarios, servicios y capital. Las pruebas detectaron errores en el mínimo y en los límites del Fondo de Solidaridad Pensional. Aquí puedes consultar la revisión; todavía no se calculan aportes ni se trasladan valores a renta.</p>
    </section>
    <section class="space-y-space-md" aria-label="Hallazgos de revisión">
        <h2 class="text-headline-md font-semibold">Abre solo el tema que necesitas</h2>
        <details class="bg-surface-container-lowest rounded-xl border border-surface-container p-4">
            <summary class="font-semibold">1 · Ingresos en cero y obligación de aportar</summary>
            <p class="mt-2">El archivo recibido muestra $499.008 de aportes con todos los ingresos en cero. Aplica el piso de un salario mínimo antes de determinar si existe obligación de cotizar. Debe separar la evaluación del ingreso neto de la aplicación del mínimo a quien corresponda.</p>
            <a class="text-secondary underline" href="https://www.ugpp.gov.co/abc_rentistas_capital/" target="_blank" rel="noopener noreferrer">UGPP · Obligación y base mínima de rentistas</a>
        </details>
        <details class="bg-surface-container-lowest rounded-xl border border-surface-container p-4">
            <summary class="font-semibold">2 · Fondo de Solidaridad Pensional</summary>
            <p class="mt-2">Con una base de exactamente cuatro salarios mínimos, la calculadora muestra que no aplica el aporte adicional. La Ley 100 incluye bases iguales a ese límite. Deben probarse también los extremos de los tramos desde dieciséis salarios mínimos.</p>
            <a class="text-secondary underline" href="https://cancilleria.gov.co/normograma/compilacion/docs/ley_0100_1993.htm#20" target="_blank" rel="noopener noreferrer">Ley 100 · Artículos 20 y 27</a>
        </details>
        <details class="bg-surface-container-lowest rounded-xl border border-surface-container p-4">
            <summary class="font-semibold">3 · Período y presunción de costos</summary>
            <p class="mt-2">No existe selector de mes y año. La UGPP informa la entrada en vigor del Decreto 379 de 2026 desde el 9 de mayo de 2026. No debe aplicarse una única tabla a todos los períodos. También falta validar la clasificación de cada ingreso: la fila Arriendos inicia en actividades inmobiliarias y la de intangibles en No clasificados; esos valores iniciales no acreditan que corresponda ese coeficiente.</p>
            <a class="text-secondary underline" href="https://www.ugpp.gov.co/enterate-con-la-ugpp/" target="_blank" rel="noopener noreferrer">UGPP · Cambio del esquema de costos en 2026</a>
        </details>
        <details class="bg-surface-container-lowest rounded-xl border border-surface-container p-4">
            <summary class="font-semibold">4 · Salarios, aportes ya realizados y pensionados</summary>
            <p class="mt-2">El archivo presenta el total del sistema, incluida la parte del empleador. Para planear cuánto debe pagar la persona, hay que distinguir lo ya aportado por nómina del aporte independiente pendiente, coordinar el tope y precisar el tipo de pensión. También deben delimitarse novedades, ARL y redondeos PILA antes de presentarlo como una liquidación completa.</p>
            <a class="text-secondary underline" href="https://cancilleria.gov.co/normograma/compilacion/docs/ley_0100_1993.htm#18" target="_blank" rel="noopener noreferrer">Ley 100 · Bases y aportes concurrentes</a>
        </details>
    </section>
    <p class="text-body-md text-secondary">Los costos presuntos para seguridad social no se convierten automáticamente en deducciones del impuesto de renta. Cada cálculo conservará sus reglas y soportes.</p>
    <p><a class="text-secondary underline" href="https://www.ugpp.gov.co/calculadora-IBC/" target="_blank" rel="noopener noreferrer">Consultar la calculadora orientadora oficial de la UGPP</a></p>
    <a class="text-secondary underline" href="<?= e(url($config,'beneficios')) ?>">Volver a las categorías de planeación</a>
</main>
<?php require __DIR__.'/../partials/footer.php'; ?>
