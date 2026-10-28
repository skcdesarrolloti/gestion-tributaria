<?php
use App\Services\{ParametrosAnuales,ParametroAnualDatos,RetencionDesglose};

$annualDefault=ParametrosAnuales::defaults(2026);
$annualPost=array_map(static fn($value)=>(string)$value,array_intersect_key($annualDefault,ParametrosAnuales::LIMITS));
$annualPost+=['uvt'=>'52374','source'=>$annualDefault['source'],'norma'=>'Norma ficticia para ensayo aislado','confirmado'=>'1'];
$annualPost['tramos']=array_map(static fn($r)=>['hasta'=>(string)($r[1]??''),'tarifa'=>(string)$r[2],'fijo'=>(string)$r[3]],$annualDefault['tramos']);
$annualRead=ParametroAnualDatos::read($annualPost);
check($annualRead['tramos']===$annualDefault['tramos'] && $annualRead['uvt']===52374,'Parámetros validan unidades y reconstruyen rangos continuos.');
foreach (['uvt'=>'0','exencion_pct'=>'101','source'=>'javascript:alert(1)','confirmado'=>'','norma'=>''] as $field=>$bad) {
    $failed=false;try { ParametroAnualDatos::read(array_replace($annualPost,[$field=>$bad])); } catch (InvalidArgumentException) { $failed=true; }
    check($failed,'Parámetros rechazan dato inválido: '.$field);
}
$badRanges=$annualPost;$badRanges['tramos'][1]['hasta']='90';$failed=false;
try { ParametroAnualDatos::read($badRanges); } catch (InvalidArgumentException) { $failed=true; }
check($failed,'No permite rangos anuales superpuestos o descendentes.');
$explainSample=RetencionDesglose::build(['devengado'=>1000000000,'aportes'=>80000000],
    ['base'=>690000000,'beneficios'=>230000000,'esperada'=>36564900,'real'=>30000000],2026);
check($explainSample['neto']===920000000 && $explainSample['excedente']===192447000
    && $explainSample['sin_redondear']===36564930 && $explainSample['tarifa']===19,
    'Explicación con cifras ficticias muestra origen, base, excedente, tarifa y resultado exactos.');
$base2025=ParametrosAnuales::fingerprint(2025);
try {
    $custom=$annualRead;$custom['uvt']=60000;$custom['exencion_pct']=20;$custom['tramos'][1][2]=20;
    ParametrosAnuales::load([2026=>$custom+['revision'=>1],2027=>$annualRead+['revision'=>1]]);
    $freshGroup=$retGroups['1-999888777'];
    $newExample=App\Services\RetencionEjemplo::build($freshGroup,2026);
    check($newExample['esperada']===33200000,'Cambiar UVT, porcentaje y tabla cambia realmente el motor mensual.');
    check(App\Services\BeneficioCatalog::year('2026')['uvt']===60000 && ParametrosAnuales::fingerprint(2025)===$base2025,
        'UVT compartida por año y años anteriores conservados.');
    check(App\Services\RetencionLaboral::check($freshGroup,$retReview,2026)['estado']==='Pendiente de soporte',
        'Versión anual nueva invalida una validación anterior aunque la nómina siga igual.');
    $freshReview=array_replace($retReview,['parametros'=>ParametrosAnuales::fingerprint(2026)]);
    check(App\Services\RetencionLaboral::check($freshGroup,$freshReview,2026)['esperada']===33200000,
        'Revisión confirmada con versión actual usa nuevos parámetros.');
    check(ParametrosAnuales::year(2027)['uvt']===52374,'Un año configurado tiene sus propios datos explícitos.');
} finally { ParametrosAnuales::load([]); }
