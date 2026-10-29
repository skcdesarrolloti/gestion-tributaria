<?php
declare(strict_types=1);
require_once __DIR__.'/rut-fixtures.php';
function loanFixtureText(string $name='Pensionado ficticio',string $type='CREDITO DE CONSUMO'): string
{
    return "BANCO CAJA SOCIAL\nESTADO DE CUENTA\n$type\n$name\nNUMERO DE CREDITO\n5555550001\nSALDO A CAPITAL EN PESOS\n30,000.00\nPAGUE HASTA\n2025/05/10\nFECHA DE FACTURACION\n2025/04/20\nDISTRIBUCION DEL PAGO ANTERIOR\nFECHA PAGO ANTERIOR\n2025/04/09\nABONO A CAPITAL\n1,000.00\nINTERESES CORRIENTES\n200.00\nINTERESES MORA\n0.00\nSEGURO DE VIDA\n10.00\nSEGURO DE INCENDIO Y TERREMOTO\nCOMISION (MICROF. FNG, ONG)\n0.00\nCARGOS ADTIVOS COBRANZA\n0.00\nVALOR PENDIENTE POR APLICAR\n0.00\nTOTAL\n1,210.00\nDISTRIBUCION DE LA CUOTA A PAGAR\nABONO A CAPITAL\n1,000.00\nINTERESES CORRIENTES\n190.00\nSEGURO DE VIDA\n10.00\nVALOR PENDIENTE POR APLICAR\n5.00\nVALOR A PAGAR\n1,195.00";
}
function loanFixturePdf(string $name='Pensionado ficticio',string $type='CREDITO DE CONSUMO'): string
{
    $cmd=[];foreach (explode("\n",loanFixtureText($name,$type)) as $i=>$line) {
        $line=str_replace(['\\','(',')'],['\\\\','\\(','\\)'],$line);$cmd[]='BT /F1 8 Tf 1 0 0 1 40 '.(780-$i*10).' Tm ('.$line.') Tj ET';
    }return rutFixturePages([implode("\n",$cmd)]);
}
