<?php
declare(strict_types=1);
require_once __DIR__.'/rut-fixtures.php';
function bancolombiaFixtureCells(): array
{
    $pages=[[],[]];$put=static function($p,$x,$y,$text) use(&$pages){$pages[$p][]=['x'=>$x,'y'=>$y,'text'=>$text];};
    $put(0,30,740,'BANCOLOMBIA ESTADO DE CUENTA');$put(0,332,681.8,'CUENTA DE AHORROS');$put(0,17,675.6,'TITULAR BANCO FICTICIO');$put(0,400,664.8,'00000008765');
    foreach ([[332,'DESDE:'],[370,'2025/03/31'],[450,'HASTA:'],[490,'2025/06/30']] as [$x,$v]) { $put(0,$x,698.8,$v); }
    foreach ([[222.4,472.6,'1,000.00'],[217.6,457.6,'303.90'],[217.6,442.6,'54.00'],[222.4,427.6,'1,249.90'],[548,441.8,'.90'],[558.8,426.8,'.00']] as [$x,$y,$v]) { $put(0,$x,$y,$v); }
    $rows=[['1/04','PAGO PENSION ADMINISTRADORA',10000,110000],['1/04','ABONO INTERESES AHORROS',20,110020],['2/04','IMPTO GOBIERNO 4X1000',-100,109920],
        ['1/05','PAGO PENSION ADMINISTRADORA',10000,119920],['1/05','ABONO INTERESES AHORROS',30,119950],['2/05','TRANSFERENCIA CTA SUC VIRTUAL',-5000,114950],
        ['1/06','PAGO PENSION ADMINISTRADORA',10000,124950],['1/06','ABONO INTERESES AHORROS',40,124990],['2/06','PAGO CXC DESDE CTA 00000000001',300,125290],['2/06','CXC IMPTO GOBIERNO 4X1000 MON',-300,124990]];
    foreach ($rows as $i=>[$date,$desc,$value,$balance]) {
        $p=$i<3?0:1;$y=$p===0?395.6-$i*9:584.6-($i-3)*9;
        $put($p,37.4,$y,$date);$put($p,79,$y,$desc);$put($p,450,$y,abs($value)<100?($value<0?'-':'').'.'.str_pad((string)abs($value),2,'0',STR_PAD_LEFT):number_format($value/100,2,'.',','));
        $put($p,540,$y,number_format($balance/100,2,'.',','));
    }return $pages;
}
function bancolombiaFixturePdf(): string
{
    $streams=[];foreach (bancolombiaFixtureCells() as $page) { $cmd=[];foreach ($page as $c) {
        $t=str_replace(['\\','(',')'],['\\\\','\\(','\\)'],$c['text']);$cmd[]='BT /F1 8 Tf 1 0 0 1 '.$c['x'].' '.$c['y'].' Tm ('.$t.') Tj ET';
    }$streams[]=implode("\n",$cmd); }return rutFixturePages($streams);
}
