<?php
declare(strict_types=1);
namespace App\Services;

final class CaracterizacionSchema
{
    public const YESNO=['si'=>'Sí','no'=>'No'];
    public const SOURCE='https://www.dian.gov.co/impuestos/Documents/Secciones-RUT-DIAN.pdf';
    public static function field(string $label, string $kind='text', array $options=[], bool $required=false, string $help=''): array
    {
        return compact('label','kind','options','required','help');
    }
    public static function all(array $catalogs=[]): array
    {
        $f=[self::class,'field']; $yn=self::YESNO;
        $ciiu=$catalogs['ciiu']??[]; $responsabilidad=$catalogs['responsabilidad']??[]; $aduana=$catalogs['aduana']??[];
        $blocks=[];
        foreach (['rut','planeacion','detalles'] as $file) {
            $blocks=array_merge($blocks,require dirname(__DIR__,2).'/resources/caracterizacion/'.$file.'.php');
        }
        foreach ($blocks as $key=>&$block) { $block['key']=$key; }
        unset($block);
        return $blocks;
    }
}
