<?php
declare(strict_types=1);
namespace App\Services;

final class NormaCatalog
{
    public const DOCUMENTS=[
        'et'=>['Estatuto Tributario','estatuto_tributario.htm'],
        'ley-361-1997'=>['Ley 361 de 1997','ley_0361_1997.htm'],
        'ley-1715-2014'=>['Ley 1715 de 2014','ley_1715_2014.htm'],
        'ley-814-2003'=>['Ley 814 de 2003','ley_0814_2003.htm'],
        'ley-2380-2024'=>['Ley 2380 de 2024','ley_2380_2024.htm'],
    ];
    public static function references(array $item): array
    {
        $references=[];
        if (preg_match('/ET ([^;]+)/u',$item['norm'],$match)) {
            $text=preg_replace('/par\.\s*\d+|(?<=\d)\.\d+(?:\s+a\s+\d+(?!-))?/u','',$match[1]);
            $text=preg_replace_callback('/(\d+)-(\d+)\s+a\s+\1-(\d+)/',static function($m) {
                return (int)$m[3]>=(int)$m[2] && (int)$m[3]-(int)$m[2]<=10?implode(', ',array_map(static fn($i)=>$m[1].'-'.$i,range((int)$m[2],(int)$m[3]))):$m[0];
            },$text);
            preg_match_all('/\d+(?:-\d+)?/',$text,$articles);
            foreach (array_unique($articles[0]) as $article) { $references[]=self::reference('et',$article); }
        }
        $special=match($item['id']) {
            'empleo-discapacidad'=>[['ley-361-1997','31']],
            'energia'=>[['ley-1715-2014','11']],
            'cine'=>[['ley-814-2003','16'],['ley-814-2003','17']],
            'bancos-alimentos'=>[['ley-2380-2024','2']],
            default=>[],
        };
        foreach ($special as [$document,$article]) { $references[]=self::reference($document,$article); }
        return $references;
    }
    private static function reference(string $document,string $article): array
    {
        return ['id'=>$document.'-'.$article,'document'=>$document,'article'=>$article,
            'label'=>($document==='et'?'ET':self::DOCUMENTS[$document][0]).' · Artículo '.$article,
            'source'=>'https://normograma.dian.gov.co/dian/compilacion/docs/'.self::DOCUMENTS[$document][1].'#'.$article];
    }
    public static function all(): array
    {
        $all=[];foreach (BeneficioCatalog::all() as $item) { foreach (self::references($item) as $reference) { $all[$reference['id']]=$reference; } }
        return $all;
    }
    public static function read(string $id): ?array
    {
        $reference=self::all()[$id]??null;if (!$reference) { return null; }
        $file=dirname(__DIR__,2).'/resources/normas/'.$reference['document'].'.json';
        if (!is_file($file)) { return null; }
        $snapshot=json_decode(file_get_contents($file),true,64,JSON_THROW_ON_ERROR);
        $text=$snapshot['articles'][$reference['article']]??null;
        return $text?$reference+['reviewed'=>$snapshot['reviewed'],'paragraphs'=>$text]:null;
    }
}
