<?php
// Todos estos valores se publican únicamente en la base aleatoria de integración.
[$status,$parameterPage]=request($client,$base,'configuracion&anio=2090');
verify($status===200 && str_contains($parameterPage,'año por preparar'),'Socio puede preparar parámetros de un año futuro');
$parameterHtml=new DOMDocument();@$parameterHtml->loadHTML($parameterPage);$parameterXpath=new DOMXPath($parameterHtml);
verify($parameterXpath->query('//input[@name="uvt" and @value=""]')->length===1,'Un año nuevo no inventa una UVT oficial');
$annualPost=['_csrf_token'=>token($parameterPage),'anio'=>'2090','revision'=>'0','uvt'=>'60000',
    'source'=>'https://www.dian.gov.co/prueba-ficticia','norma'=>'Ensayo aislado <script>no ejecutar</script>','confirmado'=>'1'];
foreach (App\Services\ParametrosAnuales::LIMITS as $field=>[$label,$default]) { $annualPost[$field]=(string)$default; }
$annualPost['tramos']=array_map(static fn($r)=>['hasta'=>(string)($r[1]??''),'tarifa'=>(string)$r[2],'fijo'=>(string)$r[3]],App\Services\RetencionTarifa::BRACKETS);
verify(request($client,$base,'configuracion',array_replace($annualPost,['_csrf_token'=>'']))[0]===419,'Publicar parámetros exige CSRF');
verify(request($client,$base,'configuracion',array_replace($annualPost,['uvt'=>'']))[0]===422,'UVT vacía no se publica');
verify(request($client,$base,'configuracion',array_replace($annualPost,['anio'=>'2025']))[0]===403,'Los años anteriores se conservan en consulta');
$unchangedAnnual=$db->query('SELECT * FROM caracterizaciones_tributarias ORDER BY id')->fetchAll();
verify(request($client,$base,'configuracion',$annualPost)[0]===303,'Publica primera versión anual en la base aislada');
[$status,$publishedAnnual]=request($client,$base,'configuracion&anio=2090');
verify($status===200 && str_contains($publishedAnnual,'value="60000"') && str_contains($publishedAnnual,'&lt;script&gt;')
    && !str_contains($publishedAnnual,'<script>no ejecutar</script>'),'Recupera parámetros del año y escapa sus fuentes');
verify(request($client,$base,'configuracion',$annualPost)[0]===409,'Una pestaña antigua no sobrescribe parámetros anuales');
$annualPost['revision']='1';$annualPost['uvt']='61000';
verify(request($client,$base,'configuracion',$annualPost)[0]===303,'Actualizar conserva una segunda versión sin borrar la primera');
$savedAnnual=$db->query('SELECT revision,datos FROM parametros_anuales WHERE anio=2090 ORDER BY revision')->fetchAll();
verify(count($savedAnnual)===2 && json_decode($savedAnnual[0]['datos'],true)['uvt']===60000
    && json_decode($savedAnnual[1]['datos'],true)['uvt']===61000,'Historial conserva exactamente valores y versiones');
verify($db->query('SELECT * FROM caracterizaciones_tributarias ORDER BY id')->fetchAll()===$unchangedAnnual,
    'Publicar otro año no altera revisiones ni fichas de años existentes');
$parameterUser=curl_init();curl_setopt($parameterUser,CURLOPT_COOKIEFILE,'');
try {
    [, $parameterLogin]=request($parameterUser,$base,'login');
    verify(request($parameterUser,$base,'login',['_csrf_token'=>token($parameterLogin),'username'=>'hash-user','password'=>'Otra-clave-segura'])[0]===303,'Usuario de prueba entra para comprobar permisos de configuración');
    verify(request($parameterUser,$base,'configuracion&anio=2090')[0]===403
        && request($parameterUser,$base,'configuracion',$annualPost)[0]===403,'Tercero no consulta ni publica parámetros aunque conozca la ruta');
    [, $parameterProfile]=request($parameterUser,$base,'perfil');
    verify(!str_contains($parameterProfile,'>Configuración</a>'),'Menú de Configuración solo visible al Socio');
} finally { curl_close($parameterUser); }
