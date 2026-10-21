<?php
declare(strict_types=1);
namespace App\Services;

final class RutPdfGrid
{
    public function __construct(private array $cells) {}

    public function value(float $left, float $right, float $bottom, float $top, bool $digits=false): string
    {
        $found=array_values(array_filter($this->cells,static fn($c)=>$c['x']>=$left && $c['x']<$right
            && $c['y']>=$bottom && $c['y']<=$top && $c['text']!==''));
        usort($found,static fn($a,$b)=>abs($a['y']-$b['y'])>3?($b['y']<=>$a['y']):($a['x']<=>$b['x']));
        if ($digits) {
            return implode('',array_column(array_filter($found,static fn($c)=>preg_match('/^[0-9 ]+$/D',$c['text'])),'text'));
        }
        return trim(preg_replace('/\s+/u',' ',implode(' ',array_column($found,'text'))));
    }

    public function codes(float $y, float $start, float $width, int $count): array
    {
        $codes=[];
        for ($i=0;$i<$count;$i++) {
            $digits=preg_replace('/\s/','',$this->value($start+$width*$i,$start+$width*($i+1),$y-2,$y+2,true));
            if ($digits!=='') { $codes[]=str_pad($digits,2,'0',STR_PAD_LEFT); }
        }
        return array_values(array_unique($codes));
    }
}
