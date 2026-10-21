<?php
declare(strict_types=1);

use App\Services\BeneficioCatalog;

$benefits=BeneficioCatalog::all();
$ids=array_column($benefits,'id');
check(count($ids)===count(array_unique($ids)), 'Fichas sin identificadores duplicados para comparar con el contador.');
check(BeneficioCatalog::year('2026')['uvt']===52374 && BeneficioCatalog::year('2025')['uvt']===49799,
    'UVT separada por año gravable.');
$invalidYear=false;
try { BeneficioCatalog::year('2027'); } catch (InvalidArgumentException) { $invalidYear=true; }
check($invalidYear,'No proyecta UVT desconocida como si fuera cero o la del año previo.');
check(BeneficioCatalog::filter('deducciones','ICETEX')[0]['id']==='icetex','Busca sin distinguir mayúsculas.');
check(BeneficioCatalog::filter('deducciones','medicina prepagada')!==[],'Encuentra cobertura de salud.');
check(BeneficioCatalog::filter('exentas','pension')!==[],'Busca términos con o sin tilde.');
check(BeneficioCatalog::filter('costos','254-1')===[],'No confunde descuento de dividendos con costo de actividad.');
check(BeneficioCatalog::filter('','<script>alert(1)</script>')===[],'Una búsqueda no crea contenido del catálogo.');
foreach ($benefits as $benefit) {
    foreach (['title','scope','limit','evidence','action','norm','source'] as $field) {
        check($benefit[$field]!=='','Cada ficha conserva alcance, fundamento, soportes y acción.');
    }
    check(str_starts_with($benefit['source'],'https://normograma.dian.gov.co/'),'Cada ficha enlaza una fuente oficial.');
}
$byId=array_column($benefits,null,'id');
check(str_contains($byId['trabajo-25']['limit'],'790') && str_contains($byId['trabajo-25']['limit'],'no ambos'),
    'No conserva el tope antiguo ni acumula costos con exención 25% del mismo ingreso.');
check(str_contains($byId['pensiones']['limit'],'1.000 UVT mensuales'),'No transforma tope mensual pensional en anual.');
check(str_contains($byId['dependientes-387']['limit'],'no por cada dependiente'),'Distingue tope total de dependientes.');
