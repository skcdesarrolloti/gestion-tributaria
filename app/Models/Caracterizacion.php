<?php
declare(strict_types=1);
namespace App\Models;

use App\Core\Database;

final class Caracterizacion
{
    public function __construct(private Database $database) {}

    public function blocksFor(int $id, int $owner, int $year): array
    {
        $q=$this->database->connection()->prepare('SELECT p.* FROM caracterizaciones_tributarias p
            JOIN contribuyentes c ON c.id=p.contribuyente_id WHERE c.id=? AND c.funcionario_id=? AND p.anio=?');
        $q->execute([$id,$owner,$year]); $rows=[];
        foreach ($q->fetchAll() as $row) {
            $row['datos']=json_decode($row['datos'],true,512,JSON_THROW_ON_ERROR); $rows[$row['bloque']]=$row;
        }
        return $rows;
    }

    public function catalogs(): array
    {
        $rows=$this->database->connection()->query('SELECT grupo,codigo,nombre FROM catalogos_rut ORDER BY grupo,codigo')->fetchAll();
        $catalogs=[];
        foreach ($rows as $row) { $catalogs[$row['grupo']][$row['codigo']]=$row['codigo'].' · '.$row['nombre']; }
        return $catalogs;
    }

    public function latestYearFor(int $id, int $owner): int
    {
        $q=$this->database->connection()->prepare('SELECT MAX(p.anio) FROM caracterizaciones_tributarias p
            JOIN contribuyentes c ON c.id=p.contribuyente_id WHERE c.id=? AND c.funcionario_id=?');
        $q->execute([$id,$owner]); return (int)($q->fetchColumn()?:2026);
    }

    public function incomeProfileFor(int $id, int $owner, int $year): ?array
    {
        $q=$this->database->connection()->prepare('SELECT p.residencia,p.regimen,p.formulario FROM perfiles_renta p
            JOIN contribuyentes c ON c.id=p.contribuyente_id WHERE c.id=? AND c.funcionario_id=? AND p.anio=?');
        $q->execute([$id,$owner,$year]); return $q->fetch()?:null;
    }

    public function saveFor(int $id, int $owner, int $year, string $block, array $data, int $revision): bool
    {
        $db=$this->database->connection(); $json=json_encode($data,JSON_THROW_ON_ERROR|JSON_UNESCAPED_UNICODE);
        if ($revision===0) {
            $q=$db->prepare('INSERT INTO caracterizaciones_tributarias (contribuyente_id,anio,bloque,datos)
                SELECT id,?,?,? FROM contribuyentes WHERE id=? AND funcionario_id=?');
            try { $q->execute([$year,$block,$json,$id,$owner]); }
            catch (\PDOException $e) { if ((int)($e->errorInfo[1]??0)===1062) { return false; } throw $e; }
        } else {
            $q=$db->prepare('UPDATE caracterizaciones_tributarias p JOIN contribuyentes c ON c.id=p.contribuyente_id
                SET p.datos=?, p.revision=p.revision+1 WHERE c.id=? AND c.funcionario_id=? AND p.anio=? AND p.bloque=? AND p.revision=?');
            $q->execute([$json,$id,$owner,$year,$block,$revision]);
        }
        return $q->rowCount()===1;
    }

    public function saveIdentityFor(int $id, int $owner, int $year, array $data, int $revision, array $identity): bool
    {
        $db=$this->database->connection(); $db->beginTransaction();
        try {
            $lock=$db->prepare('SELECT id FROM contribuyentes WHERE id=? AND funcionario_id=? FOR UPDATE');
            $lock->execute([$id,$owner]);
            if (!$lock->fetchColumn() || !$this->saveFor($id,$owner,$year,'identificacion',$data,$revision)) { $db->rollBack(); return false; }
            (new Contribuyente($this->database))->saveFor($identity,$owner,$id);
            $db->commit(); return true;
        } catch (\Throwable $e) { if ($db->inTransaction()) { $db->rollBack(); } throw $e; }
    }
}
