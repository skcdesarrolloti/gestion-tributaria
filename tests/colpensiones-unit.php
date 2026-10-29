<?php
declare(strict_types=1);
use App\Services\{ColpensionesReader,PensionImportData,PensionResumen};
$colText="COLPENSIONES\nCERTIFICADO DE DEVENGADOS Y DEDUCIDOS\nCEDULA: 12345678\nPERIODO: ENERO DE 2026\nTOTAL DEVENGADO: $ 4.500.000,00\nDEDUCCIONES\nAPORTE SALUD: $ 540.000,00\nTOTAL DEDUCCIONES: 540.000,00\nNETO: 3.960.000,00";
$colRead=ColpensionesReader::parse([$colText],'12345678');
check($colRead['registros'][0]['periodo']==='2026-01' && $colRead['registros'][0]['bruto']===450000000 && $colRead['registros'][0]['salud']===54000000,
    'Colpensiones identifica período y separa bruto de salud, sin usar neto bancario.');
$colRows=PensionImportData::rows([['mes'=>'1','bruto'=>'4500000','salud'=>'540000']],'2026',$colRead);
check($colRows[0]['bruto']===450000000 && $colRows[0]['salud']===54000000,'Confirmación conserva centavos enteros.');
check(PensionImportData::cents('4,500,000.50')===450000050 && PensionImportData::cents('4.500.000,50')===450000050 && PensionImportData::cents('0')===0,
    'Importes admiten formatos bancarios claros y salud cero explícita.');
$colAnnual=ColpensionesReader::parse([str_replace('ENERO DE 2026','2026-01 a 2026-12',$colText)],'12345678');
check(!empty($colAnnual['anual_sin_desglose']) && !$colAnnual['registros'],'Un total anual no se reparte en doce mesadas inventadas.');
$colFailed=false;try { PensionImportData::rows([['mes'=>'1','bruto'=>'4500000','salud'=>'540000']],'2026',$colAnnual); }catch (DomainException) { $colFailed=true; }
check($colFailed,'POST no elude el bloqueo de un certificado anual sin detalle.');
$colFailed=false;try { ColpensionesReader::parse([$colText],'99999999'); }catch (DomainException) { $colFailed=true; }
check($colFailed,'Identificación distinta impide vincular el comprobante.');
$colFailed=false;try { PensionImportData::rows([['mes'=>'2','bruto'=>'4500000','salud'=>'540000']],'2026',$colRead); }catch (DomainException) { $colFailed=true; }
check($colFailed,'No permite cambiar el mes reconocido por otro.');
$colSpecial=ColpensionesReader::parse([$colText."\nAJUSTE SALUD: 10000"],'12345678');
check($colSpecial['registros'][0]['especial'],'Ajustes quedan sujetos a revisión y no se mezclan con salud obligatoria.');
$colPartial=ColpensionesReader::parse([str_replace('PERIODO: ENERO DE 2026','',$colText)],'12345678');
check(!$colPartial['registros'] && $colPartial['campos_sin_periodo']['bruto']===450000000,'Si falta el mes se conserva el importe leído, sin exigir digitarlo otra vez.');
$colFailed=false;try { PensionImportData::rows([['mes'=>'1','bruto'=>'4500000','salud'=>'540000']],'2026',ColpensionesReader::parse([str_replace('PERIODO: ENERO DE 2026','AJUSTE RETROACTIVO',$colText)],'12345678')); }catch (DomainException) { $colFailed=true; }
check($colFailed,'No elude revisión de retroactivos cuando tampoco se pudo leer el período.');
check(!empty(ColpensionesReader::parse([str_replace('PERIODO: ENERO DE 2026','CERTIFICADO ANUAL AÑO GRAVABLE 2026',$colText)],'12345678')['anual_sin_desglose']),'Certificado anual sin período explícito tampoco se importa como un mes.');
$colZero=ColpensionesReader::parse([str_replace('540.000,00','0,00',$colText)],'12345678');
check($colZero['registros'][0]['salud']===0,'Salud cero se distingue de salud no leída.');
$colSummary=PensionResumen::months([['casilla'=>99,'estado'=>'real','fecha'=>'2026-01-01','valor_cop'=>4500000],['casilla'=>100,'estado'=>'real','fecha'=>'2026-01-01','valor_cop'=>540000]],[],[]);
check($colSummary[1]['bruto']===4500000 && $colSummary[2]['bruto']===null,'Resumen mensual separa conocido de pendiente.');
