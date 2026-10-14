<section class="bg-surface-container-lowest rounded-2xl border border-surface-container p-space-xl space-y-space-md">
    <h2 class="text-headline-md font-semibold">Estructura para llegar al impuesto proyectado</h2>
    <p class="text-body-md text-secondary">Estos son los siguientes bloques del instrumento. Las opciones de simulación y el plan de acciones todavía están pendientes de desarrollo.</p>
    <?php foreach ([
        ['1. Ingresos del año','Registrar por fuente y mes lo ocurrido y lo esperado hasta el 31 de diciembre. Primera captura disponible; completar las fuentes pendientes.'],
        ['2. Beneficios y soportes','Revisar costos, aportes, rentas exentas, deducciones y descuentos. Mostrar requisitos, límites, incompatibilidades, artículo y ahorro posible. Pendiente.'],
        ['3. Impuesto estimado','Depurar la base y aplicar las reglas del año, residencia y régimen. Separar rentas ordinarias y ganancias ocasionales. Pendiente; no calculado.'],
        ['4. Cuánto dinero reservar','Restar retenciones, anticipos anteriores y saldos a favor utilizables; agregar el anticipo del siguiente año. Pendiente; no calculado.'],
        ['5. Comparar decisiones antes del cierre','Comparar mantener la situación actual con acciones documentadas. Mostrar ahorro tributario, desembolso necesario y dinero que queda disponible. Pendiente.'],
        ['6. Plan de acciones y UGPP','Registrar responsable, plazo, soporte y estado de cada acción. Revisar seguridad social por mes en un bloque separado. Pendiente.'],
    ] as [$heading,$description]): ?>
        <article class="rounded-xl border border-surface-container p-4 space-y-space-sm">
            <h3 class="text-headline-sm font-semibold"><?= e($heading) ?></h3>
            <p class="text-body-md"><?= e($description) ?></p>
        </article>
    <?php endforeach; ?>
    <details>
        <summary class="text-body-md underline">Fundamentos para desarrollar las reglas</summary>
        <p class="text-body-md mt-2">Referencias iniciales del Estatuto Tributario: ingresos (26–28), costos y deducciones (107), pensiones y exenciones (206), aportes voluntarios y AFC (126-1 y 126-4), depuración de personas naturales (330–341), dependientes y salud (387), tarifas (240–241) y anticipos (807–810). Su aplicación depende del perfil y del período.</p>
        <a class="text-secondary underline" href="https://normograma.dian.gov.co/dian/compilacion/docs/estatuto_tributario.htm" target="_blank" rel="noopener noreferrer">Consultar Estatuto Tributario en la DIAN</a>
    </details>
</section>
