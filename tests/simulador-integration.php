<?php
declare(strict_types=1);
require_once __DIR__.'/simulador-fixtures.php';
$simRoute='simulador&id='.$profileId;
[$status,$simHtml]=request($client,$base,$simRoute);
verify($status===200 && str_contains($simHtml,'name="pension_1" value=""')
    && str_contains($simHtml,'Las 52 fichas y sus celdas'),'Hoja privada abre sin copiar importes reales');
$simPost=array_replace(simulatorFixture(),['salario_1'=>'100000000','nombre'=>'Escenario <b>ficticio</b>',
    '_csrf_token'=>token($simHtml),'impuesto_neto'=>'1']);
$calcRoute='simulador/calcular&id='.$profileId;
verify(request($client,$base,$calcRoute)[0]===405,'Cálculo rechaza GET');
verify(request($client,$base,$calcRoute,array_replace($simPost,['_csrf_token'=>'']))[0]===419,'Cálculo exige CSRF');
[$status,$simJson]=request($client,$base,$calcRoute,$simPost);
$canonical=json_decode($simJson,true,512,JSON_THROW_ON_ERROR);
verify($status===200 && $canonical['estado']==='calculado' && $canonical['impuesto_neto']>1,'Servidor calcula sin confiar en resultado enviado por cliente');
verify(request($client,$base,$simRoute,array_replace($simPost,['_csrf_token'=>'']))[0]===419,'Guardar versión exige CSRF');
verify(request($client,$base,$simRoute,$simPost)[0]===303,'Guarda escenario privado');
$simId=(int)$db->query('SELECT id FROM escenarios_renta')->fetchColumn();
$stored=json_decode($db->query('SELECT resultado FROM escenarios_renta WHERE id='.$simId)->fetchColumn(),true);
verify($stored['impuesto_neto']===$canonical['impuesto_neto'],'Persiste resultado recalculado en servidor');
$simPost['retenciones']='1000000';
verify(request($client,$base,$simRoute,$simPost)[0]===303 && (int)$db->query('SELECT COUNT(*) FROM escenarios_renta')->fetchColumn()===2,
    'Cambiar dato guarda nueva versión sin sobreescribir');
verify(json_decode($db->query('SELECT resultado FROM escenarios_renta WHERE id='.$simId)->fetchColumn(),true)===$stored,
    'Resultado histórico permanece intacto');
[$status,$simHtml]=request($client,$base,'simulador&escenario='.$simId.'&base='.$simId);
verify($status===200 && str_contains($simHtml,'Escenario &lt;b&gt;ficticio&lt;/b&gt;')
    && str_contains($simHtml,'data-base-tax="'.$stored['impuesto_neto'].'"'),'Carga y compara versión propia escapando nombres');
$simDraft=array_replace($simPost,['elegibilidad'=>'']);
verify(request($client,$base,$simRoute,$simDraft)[0]===303,'Permite conservar borradores pendientes');
verify(request($client,$base,'simulador&id='.$otherProfileId)[0]===200,'Socio consulta el perfil ajeno en el simulador');
verify(request($client,$base,'simulador/calcular&id='.$otherProfileId,$simPost)[0]===200,'Socio explora cálculos sin persistir cambios ajenos');
verify(request($client,$base,'simulador&id='.$otherProfileId,$simPost)[0]===403,'No guarda en perfil ajeno');
$foreignSim=$db->prepare('INSERT INTO escenarios_renta (perfil_id,nombre,version,entradas,resultado) VALUES (?,?,?,?,?)');
$foreignSim->execute([$otherProfileId,'Ajeno',\App\Services\SimuladorFields::VERSION,json_encode($simPost),json_encode($stored)]);
$foreignSimId=(int)$db->lastInsertId();
verify(request($client,$base,'simulador&escenario='.$foreignSimId)[0]===200,'Socio consulta las versiones de otros titulares');
verify(request($client,$base,$simRoute.'&base='.$foreignSimId)[0]===404,'No compara contra versiones ajenas');
verify(request($client,$base,'simulador&escenario=999999')[0]===404,'Versión inexistente no crea datos');
