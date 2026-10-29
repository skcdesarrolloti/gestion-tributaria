<?php if ($profile && $profile['formulario']==='210' && $state['componente']==='ingresos' && $state['cedula']==='general'
    && in_array($state['renta'],['capital','no_laborales'],true)): ?>
<section class="bg-surface-container-lowest border border-surface-container rounded-xl p-4 space-y-space-sm" aria-label="Cómo distinguir Capital y No laborales">
<h2 class="text-headline-md font-semibold"><?= $state['renta']==='capital'?'Capital · alquileres e intereses':'No laborales · otros ingresos de actividad económica' ?></h2>
<p><strong>El alquiler de un inmueble propio se registra en Capital, casilla 58.</strong> Que un ingreso no sea salario no significa que deba ir en No laborales.</p>
<?php if ($state['renta']==='capital'): ?>
<p>Aquí van los arrendamientos, intereses de cuentas de ahorro o CDT, rendimientos financieros, regalías y explotación de propiedad intelectual. «Capital» se refiere al ingreso que producen tus bienes o inversiones; no al dinero que invertiste.</p>
<?php else: ?>
<p>Aquí van ingresos que no tienen clasificación expresa en las demás rentas, como ventas de mercancías de un negocio, según la actividad y el soporte. Su ingreso bruto corresponde a la casilla 74; los alquileres e intereses tienen su clasificación propia en Capital.</p>
<?php endif; ?>
<details><summary class="text-secondary underline">? · Ejemplos para saber dónde registrar</summary>
<p>Si recibes $2.000.000 por alquilar tu apartamento: Capital, casilla 58. Si un CDT de $20.000.000 produce $50.000 de intereses: el ingreso de Capital son los $50.000; la devolución de los $20.000.000 invertidos no es un nuevo ingreso por intereses.</p>
<p>Si vendes mercancías en tu negocio, se revisa No laborales, casilla 74. Salarios, pensiones y dividendos se registran en sus propias secciones. No dupliques un ingreso en Capital y No laborales.</p>
<p>Esta ayuda corresponde al formulario 210 de personas naturales residentes. Las sociedades y quienes usan el 110 tienen sus propias secciones. El tipo de operación y sus soportes determinan la clasificación, no solo la descripción de una consignación.</p>
<p class="text-secondary text-body-md">Fuente: instructivo oficial DIAN del formulario 210 publicado en 2026, secciones de rentas de capital y no laborales, casillas 58 y 74. Consultado el 29/10/2026.</p>
</details></section>
<?php endif; ?>
