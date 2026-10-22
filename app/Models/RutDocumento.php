<?php
declare(strict_types=1);
namespace App\Models;

use App\Core\Database;
use App\Services\RutImportPlan;

final class RutDocumento
{
    public function __construct(private Database $database) {}

    public function listFor(int $id, int $owner, int $year): array
    {
        $q=$this->database->connection()->prepare('SELECT d.id,d.titulo,d.sha256,d.created_at,d.metadatos FROM rut_documentos d
            JOIN contribuyentes c ON c.id=d.contribuyente_id WHERE c.id=? AND '.\App\Services\AccesoSql::condition('c',false).' AND d.anio=? ORDER BY d.id DESC LIMIT 10');
        $q->execute([$id,$owner,$year]); return $q->fetchAll();
    }

    public function findFor(int $id, int $owner): ?array
    {
        $q=$this->database->connection()->prepare('SELECT d.* FROM rut_documentos d JOIN contribuyentes c ON c.id=d.contribuyente_id
            WHERE d.id=? AND '.\App\Services\AccesoSql::condition('c',false));
        $q->execute([$id,$owner]); return $q->fetch()?:null;
    }

    public function applyFor(int $id, int $owner, int $year, array $preview, array $selected, bool $replace, array $schemas, string $fileKey): array
    {
        $db=$this->database->connection(); $ownsTransaction=!$db->inTransaction();
        if ($ownsTransaction) { $db->beginTransaction(); }
        try {
            $q=$db->prepare('SELECT * FROM contribuyentes WHERE id=? AND '.\App\Services\AccesoSql::condition('contribuyentes',true).' FOR UPDATE'); $q->execute([$id,$owner]);
            $taxpayer=$q->fetch();
            if (!$taxpayer) { throw new \RuntimeException('Contribuyente no disponible.'); }
            $taxpayer['dv']??=''; RutImportPlan::matchIdentity($preview['file']['parsed'],$taxpayer);
            $q=$db->prepare('SELECT * FROM caracterizaciones_tributarias WHERE contribuyente_id=? AND anio=? FOR UPDATE');
            $q->execute([$id,$year]); $blocks=[];
            foreach ($q->fetchAll() as $row) { $row['datos']=json_decode($row['datos'],true,512,JSON_THROW_ON_ERROR); $blocks[$row['bloque']]=$row; }
            foreach ($selected as $key) {
                if (!is_string($key) || !isset($preview['plan']['items'][$key])) { throw new \InvalidArgumentException('Selección de campos inválida.'); }
                $block=$preview['plan']['items'][$key]['block'];
                if ((int)($blocks[$block]['revision']??0)!==(int)($preview['revisions'][$block]??0)) {
                    throw new \DomainException('El perfil cambió desde la vista previa. Vuelve a leer el PDF para comparar los datos actuales.');
                }
            }
            $merged=RutImportPlan::merge($preview,$selected,$replace,$blocks,$schemas,$taxpayer['tipo']);
            $meta=$preview['file']['parsed']; $meta['aplicados']=$merged['applied']; $meta['conservados']=$merged['skipped'];
            $json=json_encode($meta,JSON_THROW_ON_ERROR|JSON_UNESCAPED_UNICODE);
            $q=$db->prepare('SELECT id,archivo FROM rut_documentos WHERE contribuyente_id=? AND anio=? AND sha256=?');
            $q->execute([$id,$year,$preview['file']['sha256']]); $document=$q->fetch();
            if (!$document) {
                $q=$db->prepare('INSERT INTO rut_documentos (contribuyente_id,anio,titulo,archivo,sha256,bytes,metadatos) VALUES (?,?,?,?,?,?,?)');
                $q->execute([$id,$year,$preview['file']['titulo'],$fileKey,$preview['file']['sha256'],$preview['file']['bytes'],$json]);
                $document=['id'=>(int)$db->lastInsertId(),'archivo'=>$fileKey];
            }
            $profiles=new Caracterizacion($this->database);
            foreach ($merged['updates'] as $block=>$data) {
                if (!$profiles->saveFor($id,$owner,$year,$block,$data,(int)($blocks[$block]['revision']??0))) {
                    throw new \DomainException('El bloque cambió antes de guardar. Vuelve a revisar el PDF.');
                }
            }
            if ($ownsTransaction) { $db->commit(); }
            return ['document'=>$document,'applied'=>count($merged['applied']),'skipped'=>count($merged['skipped'])];
        } catch (\Throwable $e) { if ($ownsTransaction && $db->inTransaction()) { $db->rollBack(); } throw $e; }
    }
}
