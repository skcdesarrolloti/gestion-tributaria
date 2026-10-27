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
            return ExtractoBbva::parse($text,$sheets);
        } catch (\Throwable) {
            return ['estado'=>'pendiente','motivo'=>'PDF cifrado, escaneado o formato no reconocido. Se conserva íntegro para consulta; no se han extraído importes.'];
        }
    }
}
