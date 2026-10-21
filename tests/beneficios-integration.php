<?php
declare(strict_types=1);

[$status,$benefitHtml]=request($client,$base,'beneficios');
verify($status===200 && str_contains($benefitHtml,'52 fichas organizadas en siete categorías')
    && !str_contains($benefitHtml,'id="nomina"'),'Catálogo abre por categorías sin desplegar todas las fichas');
$seen=[];
foreach ([1,2] as $benefitPage) {
    [$status,$pagedHtml]=request($client,$base,'beneficios&grupo=costos&anio=2025&pagina='.$benefitPage);
    preg_match_all('/<article id="([^"]+)"/',$pagedHtml,$matches);
    verify($status===200 && count($matches[1])<=9 && str_contains($pagedHtml,'anio=2025'),'Categoría conserva año y limita fichas por página');
    $seen=array_merge($seen,$matches[1]);
}
verify(count($seen)===17 && count(array_unique($seen))===17,'Todas las fichas de costos siguen accesibles sin duplicados');
[$status,$benefitHtml]=request($client,$base,'beneficios&anio=2025&grupo=deducciones&buscar=119');
verify($status===200 && str_contains($benefitHtml,'49.799')
    && str_contains($benefitHtml,'Intereses de crédito de vivienda')
    && !str_contains($benefitHtml,'id="nomina"'),'Filtros conservan el año y restringen categoría y artículo');
[$status,$benefitHtml]=request($client,$base,'beneficios&buscar='.rawurlencode('<script>alert(1)</script>'));
verify($status===200 && str_contains($benefitHtml,'&lt;script&gt;')
    && !str_contains($benefitHtml,'<script>alert(1)</script>'),'Escapa búsquedas del catálogo');
verify(request($client,$base,'beneficios&anio=2027')[0]===422,'No supone reglas de un año sin soporte');
verify(request($client,$base,'beneficios&grupo=../config/local')[0]===422,'No admite rutas como grupos');
verify(request($client,$base,'beneficios&pagina=-1')[0]===422,'Rechaza páginas inválidas');
verify(request($client,$base,'beneficios',[])[0]===405,'Catálogo de consulta no ofrece escritura');
[$status,$reviewHtml]=request($client,$base,'seguridad-social');
verify($status===200 && str_contains($reviewHtml,'La calculadora necesita ajustes antes de integrarse')
    && !str_contains($reviewHtml,'id="tilesContrib"'),'Revisión disponible sin publicar la calculadora no validada');
