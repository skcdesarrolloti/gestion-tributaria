<?php
declare(strict_types=1);
require_once __DIR__.'/rut-fixtures.php';
function cajaFixtureCells(): array
{
    $pages=[[],[]];$put=static function($p,$x,$y,$text) use(&$pages){$pages[$p][]=['x'=>$x,'y'=>$y,'text'=>$text];};
    $put(0,316,738,'Cuenta de Ahorros BANCO CAJA SOCIAL');$put(0,34,696,'TITULAR BANCO FICTICIO');$put(0,34,375,'00000004321');
    $put(0,459,403,'Periodo del Informe');$put(0,443,392,'1 de Enero a 31 de Marzo de 2025');
    foreach ([[65,'1,000.00'],[173,'111.00'],[284,'-25.00'],[420,'6.00'],[509,'1,086.00']] as [$x,$v]) { $put(0,$x,352.99,$v); }
    $rows=[['ENE 05','ABONO NOMINA',0,10000,110000],['ENE 31','ABONO DE INTERESES',0,100,110100],
        ['FEB 10','DEBITO CUOTA CREDITO',2000,0,108100],['FEB 28','ABONO DE INTERESES',0,200,108300],
        ['MAR 06','RECIBIDO DE CUENTA',0,500,108800],['MAR 06','TRASLADO HACIA BOLSILLO',500,0,108300],['MAR 31','ABONO DE INTERESES',0,300,108600]];
    foreach ($rows as $i=>[$date,$desc,$debit,$credit,$balance]) {
        $p=$i<2?0:1;$y=$p===0?270-$i*20:650-($i-2)*20;$put($p,39,$y,$date);$put($p,75,$y,$desc);$put($p,201,$y,(string)(12345678+$i));
        if ($debit) { $put($p,384,$y,number_format($debit/100,2,'.',',')); }if ($credit) { $put($p,470,$y,number_format($credit/100,2,'.',',')); }$put($p,533,$y,number_format($balance/100,2,'.',','));
    }return $pages;
}
function cajaFixturePdf(): string
{
    $streams=[];foreach (cajaFixtureCells() as $page) { $cmd=[];foreach ($page as $c) {
        $t=str_replace(['\\','(',')'],['\\\\','\\(','\\)'],$c['text']);$cmd[]='BT /F1 8 Tf 1 0 0 1 '.$c['x'].' '.$c['y'].' Tm ('.$t.') Tj ET';
    }$streams[]=implode("\n",$cmd); }return rutFixturePages($streams);
}
