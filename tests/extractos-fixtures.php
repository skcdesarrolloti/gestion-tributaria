<?php
declare(strict_types=1);
require_once __DIR__.'/rut-fixtures.php';

function bankFixtureCells(): array
{
    $pages=[[],[]];
    $put=static function(int $p,float $x,float $y,string $t) use (&$pages): void { $pages[$p][]=['x'=>$x,'y'=>$y,'text'=>$t]; };
    $put(0,42,747,'TITULAR BANCO FICTICIO');$put(0,455,592,'000000000000001234');
    $labels=['SALDO CIERRE MES ANTERIOR','+ ABONOS','+ INTERESES RECIBIDOS','- CARGOS','- IVA','- 4 POR MIL','- RETENCIONES','SALDO FINAL'];
    $values=['1,000.00','1,100.00','10.00','500.00','0.00','1.00','0.00','1,609.00'];
    foreach ($labels as $i=>$label) { $x=$i<4?38:346;$y=514-8*($i%4);$put(0,$x,$y,$label);$put(0,$i<4?300:535,$y+.5,$values[$i]); }
    $rows=[['ABONO MESADA PENSIONAL PAGADOR FICTICIO',0,100000,200000],['PAGO COMERCIO FICTICIO',50000,0,150000],
        ['CARGO POR IMPUESTO 4X1.000',200,0,149800],['CORRECCION IMPTO DECRETO',0,100,149900],
        ['ABONO POR INTERESES DE CUENTA',0,1000,150900],['TRANSFERENCIA POR ACLARAR',0,10000,160900]];
    foreach ($rows as $i=>[$name,$debit,$credit,$balance]) {
        $p=$i<3?0:1;$y=430-10*($i%3);
        $put($p,40,$y,(string)($i+1));$put($p,80,$y,'15-06-2025');$put($p,134,$y,'15-06-2025');$put($p,181,$y,$name);
        if ($debit) { $put($p,410,$y,number_format($debit/100,2,'.',',')); }
        if ($credit) { $put($p,475,$y,number_format($credit/100,2,'.',',')); }
        $put($p,540,$y,number_format($balance/100,2,'.',','));
    }
    return $pages;
}

function bankFixturePdf(): string
{
    $streams=[];
    foreach (bankFixtureCells() as $page) {
        $cmd=['BT /F1 8 Tf 1 0 0 1 40 560 Tm (BBVA PERIODO DESDE: 01-06-2025 HASTA: 30-06-2025) Tj ET'];
        foreach ($page as $c) { $t=str_replace(['\\','(',')'],['\\\\','\\(','\\)'],$c['text']);$cmd[]='BT /F1 8 Tf 1 0 0 1 '.$c['x'].' '.$c['y'].' Tm ('.$t.') Tj ET'; }
        $streams[]=implode("\n",$cmd);
    }
    return rutFixturePages($streams);
}
