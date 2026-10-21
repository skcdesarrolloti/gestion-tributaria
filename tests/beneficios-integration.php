<?php
declare(strict_types=1);

[$status,$benefitHtml]=request($client,$base,'beneficios');
verify($status===200 && str_contains($benefitHtml,'52 fichas encontradas')
    && str_contains($benefitHtml,'52.374'),'Catálogo protegido abre con UVT de 2026 y todos los conceptos');
[$status,$benefitHtml]=request($client,$base,'beneficios&anio=2025&grupo=deducciones&buscar=119');
verify($status===200 && str_contains($benefitHtml,'49.799')
    && str_contains($benefitHtml,'Intereses de crédito de vivienda')
    && !str_contains($benefitHtml,'id="nomina"'),'Filtros conservan el año y restringen categoría y artículo');
[$status,$benefitHtml]=request($client,$base,'beneficios&buscar='.rawurlencode('<script>alert(1)</script>'));
verify($status===200 && str_contains($benefitHtml,'&lt;script&gt;')
    && !str_contains($benefitHtml,'<script>alert(1)</script>'),'Escapa búsquedas del catálogo');
verify(request($client,$base,'beneficios&anio=2027')[0]===422,'No supone reglas de un año sin soporte');
verify(request($client,$base,'beneficios&grupo=../config/local')[0]===422,'No admite rutas como grupos');
verify(request($client,$base,'beneficios',[])[0]===405,'Catálogo de consulta no ofrece escritura');
