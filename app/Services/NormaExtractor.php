<?php
declare(strict_types=1);
namespace App\Services;

/** Extracción local para generar textos consultables. Nunca ejecuta HTML de la fuente. */
final class NormaExtractor
{
    public static function article(string $html,string $article): array
    {
        if (!preg_match('/^\d+(?:-\d+)?$/D',$article)) { throw new \InvalidArgumentException('Artículo inválido.'); }
        $previous=libxml_use_internal_errors(true);$document=new \DOMDocument();
        try { $document->loadHTML($html,LIBXML_NONET|LIBXML_NOERROR|LIBXML_NOWARNING); }
        finally { libxml_clear_errors();libxml_use_internal_errors($previous); }
        $xpath=new \DOMXPath($document);
        $anchor=$xpath->query('//a[@name="'.$article.'"]')->item(0);
        if (!$anchor) { throw new \RuntimeException('No se encontró el artículo '.$article.' en la fuente.'); }
        $node=$anchor;while ($node && strtolower($node->nodeName)!=='p') { $node=$node->parentNode; }
        if (!$node) { throw new \RuntimeException('Estructura de artículo no reconocida.'); }
        $paragraphs=[];$first=true;
        for (; $node; $node=$node->nextSibling) {
            if (!$node instanceof \DOMElement) { continue; }
            $tag=strtolower($node->tagName);$class=$node->getAttribute('class');
            if (str_contains($class,'caja_vja') || in_array($tag,['script','style','div'],true)) { continue; }
            if (!$first) {
                foreach ($xpath->query('.//a[@name]',$node) as $next) {
                    if (preg_match('/^\d+(?:-\d+)?$/D',$next->getAttribute('name')) && preg_match('/^ART[IÍ]CULO\s/iu',trim($next->textContent))) { break 2; }
                }
            }
            $first=false;
            if (!in_array($tag,['p','table','ul','ol'],true)) { continue; }
            if ($tag==='table') {
                foreach ($xpath->query('.//tr',$node) as $row) { $paragraphs[]=trim(preg_replace('/\s+/u',' ',self::plain($row))," \t\n\r\0\x0B|"); }
                continue;
            }
            $text=trim(preg_replace('/\s+/u',' ',self::plain($node)));
            if ($text!=='' && !preg_match('/^(?:CAP[IÍ]TULO|T[IÍ]TULO|LIBRO)\b/u',$text)) { $paragraphs[]=$text; }
        }
        if (!$paragraphs || !preg_match('/ART[IÍ]CULO\s+'.preg_quote($article,'/').'[.ºo\s]/iu',$paragraphs[0])) { throw new \RuntimeException('Encabezado del artículo no verificado: '.$article); }
        return $paragraphs;
    }
    private static function plain(\DOMNode $node): string
    {
        if ($node instanceof \DOMText) { return $node->textContent; }
        if (!$node instanceof \DOMElement) { return ''; }
        $tag=strtolower($node->tagName);
        if (in_array($tag,['script','style'],true)) { return ''; }
        if (in_array($tag,['s','del','strike'],true)) { return '[Texto tachado en la fuente: '.$node->textContent.']'; }
        $text='';foreach ($node->childNodes as $child) { $text.=self::plain($child); }
        return $text.(in_array($tag,['li','td','th','tr','br'],true)?' | ':'');
    }
}
