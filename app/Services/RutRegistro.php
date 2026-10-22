<?php
declare(strict_types=1);
namespace App\Services;

use App\Core\Database;
use App\Models\{Contribuyente,Caracterizacion,RutDocumento};

/** Borrador de alta desde RUT; solo persiste al confirmar Guardar contribuyente. */
final class RutRegistro
{
    private RutUpload $files;
    public function __construct(private Database $database, string $storage) { $this->files=new RutUpload($storage); }

    public function draft(int $owner): ?array
    {
        $draft=$_SESSION['rut_registro']??null;
        if ($draft && $draft['time']<time()-1800) { $this->files->remove($draft['file']['key']); unset($_SESSION['rut_registro']); return null; }
        return $draft && $draft['owner']===$owner?$draft:null;
    }

    public static function seed(array $parsed): array
    {
        $fields=$parsed['fields']['identificacion']; $class=RutPdfReader::normalize($fields['tipo_contribuyente_rut']??''); $type='';
        if (str_contains($class,'natural') || trim($class)==='2') { $type='natural'; }
        elseif (str_contains($class,'juridica') || trim($class)==='1') { $type='juridica'; }
        $person=trim(implode(' ',array_filter(array_map(static fn($key)=>$fields[$key]??'',['nombre1','otros_nombres','apellido1','apellido2']),static fn($v)=>$v!=='')));
        return ContribuyenteData::read(['tipo'=>$type,'nombre'=>($type==='juridica'?($fields['razon_social']??''):$person)?:($fields['razon_social']??$person),
            'documento_tipo'=>'NIT','documento_numero'=>$parsed['nit'],'dv'=>$parsed['dv']]);
    }

    public function receive(array $upload, int $owner, string $year): ?array
    {
        if (!in_array($year,['2025','2026'],true)) { throw new \InvalidArgumentException('Selecciona el año del perfil: 2025 o 2026.'); }
        $this->files->cleanup(); $file=$this->files->receive($upload);
        try {
            $existing=(new Contribuyente($this->database))->findFromRutFor($file['parsed'],$owner);
            if ($existing) { return $this->openExisting($file,$existing,$owner,$year); }
            $seed=self::seed($file['parsed']); $schemas=CaracterizacionSchema::all((new Caracterizacion($this->database))->catalogs());
            $plan=RutImportPlan::build($file['parsed'],$seed,$schemas,$year);
        } catch (\Throwable $e) { $this->files->remove($file['key']); throw $e; }
        $old=$this->draft($owner); if ($old) { $this->files->remove($old['file']['key']); }
        $_SESSION['rut_registro']=['owner'=>$owner,'time'=>time(),'token'=>bin2hex(random_bytes(24)),
            'year'=>$year,'file'=>$file,'seed'=>$seed,'warnings'=>$plan['warnings'],'count'=>count($plan['items']),
            'summary'=>self::summary($plan,$schemas)];
        return null;
    }

    private function openExisting(array $file, array $existing, int $owner, string $year): array
    {
        $profiles=new Caracterizacion($this->database); $schemas=CaracterizacionSchema::all($profiles->catalogs());
        $plan=RutImportPlan::build($file['parsed'],$existing,$schemas,$year);
        $blocks=$profiles->blocksFor((int)$existing['id'],$owner,(int)$year);
        $preview=['owner'=>$owner,'id'=>(int)$existing['id'],'year'=>$year,'time'=>time(),'token'=>bin2hex(random_bytes(24)),
            'file'=>$file,'plan'=>$plan,'revisions'=>array_map(static fn($b)=>(int)$b['revision'],$blocks)];
        $result=(new RutAplicacion(new RutDocumento($this->database),$this->files))->apply($preview,array_keys($plan['items']),false,$schemas);
        $previous=$_SESSION['rut_import']['file']['key']??'';
        if ($previous!=='' && $previous!==$file['key']) { $this->files->remove($previous); }
        $draft=$this->draft($owner);
        if ($draft && $draft['file']['key']!==$file['key']) { $this->files->remove($draft['file']['key']); }
        unset($_SESSION['rut_registro'],$_SESSION['rut_import']);
        $_SESSION['rut_notice'][$existing['id'].':'.$year]='Este contribuyente ya existía. Completamos su perfil con el RUT: '.$result['applied'].' campos y renglones importados. Sus datos anteriores se conservan.';
        return ['id'=>(int)$existing['id'],'year'=>$year,'existing'=>true];
    }

    private function confirmed(int $owner, string $token): array
    {
        $draft=$this->draft($owner);
        if (!$draft || !hash_equals($draft['token'],$token)) { throw new \InvalidArgumentException('La lectura del RUT venció o cambió en otra ventana. Vuelve a subirlo antes de guardar.'); }
        return $draft;
    }

    public function discard(int $owner, string $token): void
    {
        $draft=$this->confirmed($owner,$token); $this->files->remove($draft['file']['key']); unset($_SESSION['rut_registro']);
    }

    public function create(array $record, int $owner, string $token): array
    {
        $draft=$this->confirmed($owner,$token); $file=$draft['file'];
        RutImportPlan::matchIdentity($file['parsed'],$record);
        $existing=(new Contribuyente($this->database))->findFromRutFor($file['parsed'],$owner);
        if ($existing) { return $this->openExisting($file,$existing,$owner,$draft['year']); }
        $type=$draft['seed']['tipo'];
        if (($type==='juridica' && $record['tipo']!=='juridica') || ($type==='natural' && $record['tipo']==='juridica')) {
            throw new \InvalidArgumentException('El tipo de contribuyente no coincide con la casilla 24 del RUT. Para una persona fallecida revisa Sucesión ilíquida; no Persona jurídica.');
        }
        $schemas=CaracterizacionSchema::all((new Caracterizacion($this->database))->catalogs());
        $preview=['file'=>$file,'plan'=>RutImportPlan::build($file['parsed'],$record,$schemas,$draft['year']),'revisions'=>[]];
        $path=$this->files->path($file['key']); $final=substr($file['key'],0,64).'.pdf';
        if (!is_file($path) || !hash_equals($file['sha256'],hash_file('sha256',$path))) { throw new \InvalidArgumentException('El PDF temporal no está disponible. Vuelve a subir el RUT.'); }
        if (!rename($path,$this->files->path($final))) { throw new \RuntimeException('No se pudo conservar el soporte.'); }
        $db=$this->database->connection();
        try {
            $db->beginTransaction();
            $late=(new Contribuyente($this->database))->findFromRutFor($file['parsed'],$owner);
            if ($late) {
                $db->rollBack(); rename($this->files->path($final),$path);
                return $this->openExisting($file,$late,$owner,$draft['year']);
            }
            $id=(new Contribuyente($this->database))->saveFor($record,$owner,null);
            $result=(new RutDocumento($this->database))->applyFor($id,$owner,(int)$draft['year'],$preview,array_keys($preview['plan']['items']),false,$schemas,$final);
            $db->commit();
        } catch (\Throwable $e) {
            if ($db->inTransaction()) { $db->rollBack(); }
            if (is_file($this->files->path($final))) { rename($this->files->path($final),$path); }
            throw $e;
        }
        unset($_SESSION['rut_registro']);
        return ['id'=>$id,'year'=>$draft['year'],'applied'=>$result['applied']];
    }

    private static function summary(array $plan, array $schemas): array
    {
        $groups=[];
        foreach ($plan['items'] as $item) {
            $schema=$schemas[$item['block']]; $value=$item['value'];
            if ($item['kind']==='campo') { $value=$schema['fields'][$item['field']]['options'][$value]??$value; }
            elseif ($item['kind']==='filas') { $value=implode(', ',array_column($value,'codigo')); }
            else {
                $parts=[];
                foreach ($value as $key=>$text) { if ($text!=='') { $f=$schema['rows'][$key]; $parts[]=$f['label'].': '.($f['options'][$text]??$text); } }
                $value=implode(' · ',$parts);
            }
            $groups[$schema['title']][]=['label'=>$item['label'],'value'=>$value];
        }
        return $groups;
    }
}
