<?php
declare(strict_types=1);
require_once __DIR__.'/caja-social-fixtures.php';
$cajaRoute='extractos&contribuyente='.$readBankTax.'&anio=2025';[, $cajaPage]=request($client,$base,$cajaRoute);
$cajaPath=$temp.'/caja-trimestre-ficticio.pdf';file_put_contents($cajaPath,cajaFixturePdf());
$cajaPost=['_csrf_token'=>token($cajaPage),'extracto_pdf'=>new CURLFile($cajaPath,'application/pdf','trimestre-ficticio.pdf'),'mes'=>'auto','banco'=>'','cuenta'=>''];
$cajaBefore=(int)$db->query('SELECT COUNT(*) FROM movimientos_ingresos')->fetchColumn();
[$s,$cajaSaved]=rutRequest($client,$base,'extractos/lote&contribuyente='.$readBankTax.'&anio=2025',$cajaPost);$cajaSaved=json_decode($cajaSaved,true);
verify($s===200 && $cajaSaved['banco']==='BANCO CAJA SOCIAL' && $cajaSaved['leido'],'Caja Social detecta banco, titular, cuenta y trimestre por carga HTTP');$cajaId=$cajaSaved['id'];
verify((int)$db->query('SELECT COUNT(*) FROM extractos_bancarios WHERE id='.$cajaId)->fetchColumn()===1,'Trimestre guarda un único documento y archivo, sin tres inserciones');
[, $cajaVisible]=request($client,$base,$cajaRoute);
verify(str_contains($cajaVisible,'3/12 meses con soporte') && str_contains($cajaVisible,'$1,00') && str_contains($cajaVisible,'$2,00') && str_contains($cajaVisible,'$3,00'),'Trimestre aparece como enero, febrero y marzo con rendimientos individuales');
verify(str_contains($cajaVisible,'mes_movimientos=2') && str_contains($cajaVisible,'Sube') && str_contains($cajaVisible,'carga única'),'Carga trimestral se mantiene disponible dentro de Ingresos');
[, $cajaMonth]=request($client,$base,$cajaRoute.'&documento='.$cajaId.'&mes_movimientos=2');
verify(str_contains($cajaMonth,'2 movimientos encontrados.') && str_contains($cajaMonth,'DEBITO CUOTA CREDITO') && !str_contains($cajaMonth,'ABONO NOMINA</th>'),'Detalle por mes conserva índices originales sin mostrar operaciones de otros meses');
verify((int)$db->query('SELECT COUNT(*) FROM movimientos_ingresos')->fetchColumn()===$cajaBefore,'Abonos de Caja Social no duplican salarios ni se convierten en arriendos o pensión fiscal');
verify(rutRequest($client,$base,'extractos/lote&contribuyente='.$readBankTax.'&anio=2025',$cajaPost)[0]===409,'PDF trimestral repetido no se vuelve a sumar');
file_put_contents($cajaPath,rutFixturePdf('99887766'));$cajaOverlap=array_replace($cajaPost,['mes'=>'2','banco'=>'Caja Social','cuenta'=>'4321']);
verify(rutRequest($client,$base,'extractos/lote&contribuyente='.$readBankTax.'&anio=2025',$cajaOverlap)[0]===409,'No permite agregar un mes que ya está cubierto por el trimestre');
