<?php
declare(strict_types=1);
namespace App\Services;

final class ExtractoData
{
    public const MONTHS=[1=>'Enero',2=>'Febrero',3=>'Marzo',4=>'Abril',5=>'Mayo',6=>'Junio',
        7=>'Julio',8=>'Agosto',9=>'Septiembre',10=>'Octubre',11=>'Noviembre',12=>'Diciembre'];

    public static function read(array $source, string $year): array
    {
        $month=filter_var(\input($source,'mes'),FILTER_VALIDATE_INT);
        $bank=preg_replace('/\s+/u',' ',trim(\input($source,'banco')));
        if (in_array(PrestamoCajaSocial::normal($bank),['CAJA SOCIAL','BANCO CAJA SOCIAL','BANCOCAJASOCIAL'],true)) { $bank='BANCO CAJA SOCIAL'; }
        $account=trim(\input($source,'cuenta'));
        if (!in_array($year,['2025','2026'],true) || !$month || !isset(self::MONTHS[$month])) {
            throw new \InvalidArgumentException('Selecciona el año y el mes que cubre el extracto, no la fecha de descarga.');
        }
        if (!$bank || mb_strlen($bank)>80 || preg_match('/[\x00-\x1f<>]/u',$bank) || !preg_match('/^[0-9]{4}$/D',$account)) {
            throw new \InvalidArgumentException('Indica el banco y únicamente los últimos cuatro dígitos de la cuenta.');
        }
        return ['anio'=>(int)$year,'mes'=>$month,'banco'=>mb_strtoupper($bank),'cuenta'=>$account,
            'titular_confirmado'=>\input($source,'titular_confirmado')==='1'];
    }

    public static function calendar(array $documents, string $bank, string $account): array
    {
        $months=array_fill(1,12,[]);
        foreach (self::monthlyDocuments($documents) as $d) {
            if ($d['banco']===$bank && $d['cuenta']===$account) { $months[(int)$d['mes']][]=$d; }
        }
        return $months;
    }

    public static function reading(array $document): array
    {
        $value=json_decode($document['lectura']??'null',true);
        if (!is_array($value)) { return ['estado'=>'pendiente','motivo'=>'Documento pendiente de lectura.']; }
        if (isset($document['_mes_consulta'],$value['meses'][$document['_mes_consulta']])) {
            $month=(int)$document['_mes_consulta'];$value=array_replace($value,$value['meses'][$month]);
            $value['movimientos']=array_values(array_filter($value['movimientos'],static fn($r)=>(int)substr($r['fecha'],5,2)===$month));
        }
        return $value;
    }

    /** Vistas mensuales del mismo ID/PDF; no se insertan copias en la base. */
    public static function monthlyDocuments(array $documents): array
    {
        $rows=[];foreach ($documents as $doc) {
            if (isset($doc['_mes_consulta'])) { $rows[]=$doc;continue; }
            $reading=self::reading($doc);$months=$reading['meses_cubiertos']??[(int)$doc['mes']];
            foreach ($months as $month) { if (isset(self::MONTHS[(int)$month])) { $copy=$doc;$copy['mes']=(int)$month;$copy['_mes_consulta']=(int)$month;$rows[]=$copy; } }
        }return $rows;
    }

    public static function money(int $cents): string { return '$'.number_format($cents/100,2,',','.'); }

    public static function match(array $reading, array $data, array $taxpayer): void
    {
        if (!isset($reading['inicio'],$reading['cuenta'],$reading['titular'])) { return; }
        if (substr($reading['inicio'],0,4)!==(string)$data['anio'] || (int)substr($reading['inicio'],5,2)!==$data['mes']
            || $reading['cuenta']!==$data['cuenta'] || $data['banco']!==($reading['banco']??'BBVA')) {
            throw new \InvalidArgumentException('El PDF leído no coincide con el año, mes, banco o cuenta elegidos. Revisa el período impreso en el documento.');
        }
        $normalize=static fn($s)=>preg_replace('/[^A-Z0-9]+/',' ',strtoupper(iconv('UTF-8','ASCII//TRANSLIT',$s)?:$s));
        if (trim($normalize($taxpayer['nombre']))!==trim($normalize($reading['titular']))) {
            throw new \InvalidArgumentException('El titular leído no coincide con el nombre del contribuyente. Revisa que hayas elegido su ficha y su PDF.');
        }
    }
}
