<?php
declare(strict_types=1);
namespace App\Services;

final class RutPdfReader
{
    public const MAX_BYTES=5242880;
    public const VERSION='dian-001-carta-v2';

    public static function read(string $path): array
    {
        $size=filesize($path);
        if (!$size || $size>self::MAX_BYTES) { throw new \InvalidArgumentException('El PDF debe pesar como máximo 5 MB.'); }
        $handle=fopen($path,'rb'); $signature=fread($handle,5); fclose($handle);
        if ($signature!=='%PDF-' || (new \finfo(FILEINFO_MIME_TYPE))->file($path)!=='application/pdf') {
            throw new \InvalidArgumentException('El archivo no es un PDF válido.');
        }
        require_once dirname(__DIR__,2).'/bootstrap/pdfparser.php';
        $config=new \Smalot\PdfParser\Config();
        $config->setRetainImageContent(false); $config->setDecodeMemoryLimit(8388608); $config->setMaxNestingDepth(50);
        try {
            $document=(new \Smalot\PdfParser\Parser([],$config))->parseFile($path);
            $pages=$document->getPages();
            if (!$pages || count($pages)>10) { throw new \RuntimeException('Número de páginas fuera de alcance.'); }
            $text=$document->getText();
            if (strlen($text)>1000000 || mb_strlen(trim($text))<300) { throw new \RuntimeException('Sin texto utilizable.'); }
            $sheets=[];
            foreach ($pages as $page) { $sheets[]=['text'=>$page->getText(),'cells'=>self::cells($page)]; }
        } catch (\Throwable $e) {
            throw new \InvalidArgumentException('No se pudo leer este PDF. Usa el RUT descargado de la DIAN, sin contraseña; los escaneos requieren transcripción manual.');
        }
        return ['text'=>$text,'cells'=>$sheets[0]['cells'],'sheets'=>$sheets,'pages'=>count($pages),'reader'=>self::VERSION];
    }

    private static function cells(\Smalot\PdfParser\Page $page): array
    {
        $raw=$page->getDataTm(); $texts=$page->getTextArray();
        // En formularios con un XObject de etiquetas, PdfParser agrega ese texto al
        // comienzo del arreglo, pero no a sus coordenadas. Alinear sin tocar la biblioteca.
        $offset=isset($raw[0][1]) && mb_strlen($raw[0][1])>500?1:0;
        $cells=[];
        foreach ($raw as $i=>$entry) {
            $value=$offset?($texts[$i+1]??''):$entry[1];
            if (!is_string($value) || mb_strlen($value)>500) { continue; }
            $cells[]=['x'=>(float)$entry[0][4],'y'=>(float)$entry[0][5],'text'=>trim($value)];
        }
        return $cells;
    }

    public static function normalize(string $value): string
    {
        return strtr(mb_strtolower($value),['á'=>'a','é'=>'e','í'=>'i','ó'=>'o','ú'=>'u','ü'=>'u']);
    }
}
