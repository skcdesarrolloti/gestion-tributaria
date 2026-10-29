<?php
declare(strict_types=1);
namespace App\Services;

final class ExtractoPdfReader
{
    public static function read(string $path): array
    {
        try {
            require_once dirname(__DIR__,2).'/bootstrap/pdfparser.php';
            $config=new \Smalot\PdfParser\Config();$config->setRetainImageContent(false);
            $config->setDecodeMemoryLimit(16777216);$config->setMaxNestingDepth(50);
            $pdf=(new \Smalot\PdfParser\Parser([],$config))->parseFile($path);
            $pages=$pdf->getPages();
            if (!$pages || count($pages)>30) { throw new \RuntimeException('Páginas fuera de alcance.'); }
            $sheets=[];$text='';
            foreach ($pages as $page) {
                $t=$page->getText();$text.=$t."\n";
                if (strlen($text)>1500000) { throw new \RuntimeException('Lectura fuera de alcance.'); }
                $cells=[];
                foreach ($page->getDataTm() as $c) {
                    if (!isset($c[0][4],$c[0][5],$c[1]) || !is_string($c[1]) || mb_strlen($c[1])>500) { continue; }
                    $cells[]=['x'=>(float)$c[0][4],'y'=>(float)$c[0][5],'text'=>trim($c[1])];
                }
                $sheets[]=$cells;
            }
            $normal=PrestamoCajaSocial::normal($text);
            if ((str_contains($normal,'BANCO CAJA SOCIAL') || str_contains($normal,'BANCOCAJASOCIAL'))
                && (str_contains($normal,'CREDITO DE CONSUMO') || str_contains($normal,'CREDITO COMERCIAL'))) {
                return ['tipo_documento'=>'credito','estado'=>'pendiente','motivo'=>'Extracto de crédito: se revisará el titular y el pago anterior antes de guardarlo en Control de préstamos.'];
            }
            if (str_contains($normal,'CUENTA DE AHORROS') && (str_contains($normal,'BANCO CAJA SOCIAL') || str_contains($normal,'BANCOCAJASOCIAL'))) {
                $metadata=ExtractoCajaSocial::metadata($text,$sheets);
                try { return ExtractoCajaSocial::parse($text,$sheets); }
                catch (\InvalidArgumentException) { return $metadata+['estado'=>'pendiente','motivo'=>'Se reconocieron la cuenta y el período, pero el detalle no concilia. No se sumaron importes parciales.']; }
            }
            $metadata=ExtractoBbva::metadata($text,$sheets);
            try { return ExtractoBbva::parse($text,$sheets); }
            catch (\InvalidArgumentException) {
                return $metadata+['estado'=>'pendiente','motivo'=>'Se reconocieron el titular, la cuenta y el mes, pero no se pudo conciliar todo el detalle. El PDF se conserva para revisión; no se han extraído importes.'];
            }
        } catch (\Throwable) {
            return ['estado'=>'pendiente','motivo'=>'PDF cifrado, escaneado o formato no reconocido. Se conserva íntegro para consulta; no se han extraído importes.'];
        }
    }
}
