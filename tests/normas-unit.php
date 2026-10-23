<?php
declare(strict_types=1);
use App\Services\{NormaCatalog,NormaExtractor};
$all=NormaCatalog::all();
check(isset($all['et-125-2']) && !isset($all['et-3']) && !isset($all['et-4']),'Expande rangos de artículos sin confundir numerales o parágrafos con artículos.');
foreach (App\Services\BeneficioCatalog::all() as $item) {
    $references=NormaCatalog::references($item);check($references!==[],'Cada ficha tiene artículos consultables.');
    foreach ($references as $reference) { check(NormaCatalog::read($reference['id'])!==null,'Cada referencia tiene texto y fuente disponibles.'); }
}
check(NormaCatalog::read('../config/local')===null && NormaCatalog::read('https://example.test')===null,'Solo admite artículos del catálogo, nunca rutas o URL del navegador.');
$html='<html><meta charset="UTF-8"><body><p><a name="107">ARTÍCULO 107. PRUEBA.</a> Inicio.</p><div>Concordancias</div><table class="caja_vja_c"><tr><td>Texto histórico oculto</td></tr></table><p>Continúa <strike>anterior</strike>.</p><script>alert(1)</script><p><a name="107-1">ARTÍCULO 107-1. SIGUIENTE.</a> Otra norma.</p></body></html>';
$paragraphs=NormaExtractor::article($html,'107');$text=implode(' ',$paragraphs);
check(count($paragraphs)===2 && !str_contains($text,'SIGUIENTE') && !str_contains($text,'histórico') && !str_contains($text,'alert'),'Extrae solo el artículo, sin scripts, notas ocultas ni el siguiente artículo.');
check(str_contains($text,'[Texto tachado en la fuente: anterior]'),'Conserva la advertencia cuando la fuente tacha texto.');
$article107=NormaCatalog::read('et-107');
check(count($article107['paragraphs'])===3 && str_contains(implode(' ',$article107['paragraphs']),'proporcionalidad'),'Texto 107 conserva sus párrafos y termina antes de 107-1.');
check(str_contains(implode(' ',NormaCatalog::read('et-137')['paragraphs']),'EQUIPO DE COMPUTACIÓN | 20,00%'),'La extracción conserva la tabla de depreciación.');
