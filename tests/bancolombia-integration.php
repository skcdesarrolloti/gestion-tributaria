<?php
require_once __DIR__.'/bancolombia-fixtures.php';
$bancoRoute='extractos&contribuyente='.$readBankTax.'&anio=2025';[, $bancoPage]=request($client,$base,$bancoRoute);
$bancoPath=$temp.'/banco-trimestre-ficticio.pdf';file_put_contents($bancoPath,bancolombiaFixturePdf());
$bancoPost=['_csrf_token'=>token($bancoPage),'extracto_pdf'=>new CURLFile($bancoPath,'application/pdf','trimestre-ficticio.pdf'),'mes'=>'auto','banco'=>'','cuenta'=>''];
$bancoBefore=(int)$db->query('SELECT COUNT(*) FROM movimientos_ingresos')->fetchColumn();
[$s,$bancoSaved]=rutRequest($client,$base,'extractos/lote&contribuyente='.$readBankTax.'&anio=2025',$bancoPost);$bancoSaved=json_decode($bancoSaved,true);
verify($s===200 && $bancoSaved['banco']==='BANCOLOMBIA' && $bancoSaved['meses']===[4,5,6] && $bancoSaved['leido'],'Bancolombia: carga HTTP detecta cuenta y los tres meses efectivos');
$bancoId=$bancoSaved['id'];[, $bancoVisible]=request($client,$base,$bancoRoute);
verify(str_contains($bancoVisible,'BANCOLOMBIA') && str_contains($bancoVisible,'documento='.$bancoId.'&amp;mes_movimientos=5'),'Bancolombia: cada mes abre movimientos del mismo PDF con su filtro');
[, $bancoDetail]=request($client,$base,$bancoRoute.'&documento='.$bancoId.'&mes_movimientos=5');
verify(str_contains($bancoDetail,'3 movimientos encontrados.') && str_contains($bancoDetail,'TRANSFERENCIA CTA SUC VIRTUAL'),'Bancolombia: detalle mensual incluye filas de ambas páginas sin sumar el trimestre');
verify((int)$db->query('SELECT COUNT(*) FROM movimientos_ingresos')->fetchColumn()===$bancoBefore,'Bancolombia: abono neto no se duplica como pensión bruta ni rendimiento fiscal');
verify(rutRequest($client,$base,'extractos/lote&contribuyente='.$readBankTax.'&anio=2025',$bancoPost)[0]===409,'Bancolombia: PDF repetido no se suma de nuevo');
file_put_contents($bancoPath,rutFixturePdf('99881122'));$bancoOverlap=array_replace($bancoPost,['mes'=>'5','banco'=>'BANCOLOMBIA','cuenta'=>'8765']);
verify(rutRequest($client,$base,'extractos/lote&contribuyente='.$readBankTax.'&anio=2025',$bancoOverlap)[0]===409,'Bancolombia: mes ya cubierto por trimestre se rechaza sin duplicados');
