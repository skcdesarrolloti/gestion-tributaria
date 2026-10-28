<?php
declare(strict_types=1);
namespace App\Models;

use App\Core\Database;

final class ParametroAnual
{
    public function __construct(private Database $database) {}
    public function latest(): array
    {
        $q=$this->database->connection()->query('SELECT p.* FROM parametros_anuales p
            WHERE p.revision=(SELECT MAX(h.revision) FROM parametros_anuales h WHERE h.anio=p.anio)');
        $all=[];
        foreach ($q as $row) { $all[(int)$row['anio']]=json_decode($row['datos'],true,512,JSON_THROW_ON_ERROR)+['revision'=>(int)$row['revision']]; }
        return $all;
    }
    public function history(int $year): array
    {
        $q=$this->database->connection()->prepare('SELECT p.revision,p.datos,p.created_at,u.nombre FROM parametros_anuales p
            LEFT JOIN usuarios_tributarios u ON u.id=p.creado_por WHERE p.anio=? ORDER BY p.revision DESC');
        $q->execute([$year]);return $q->fetchAll();
    }
    public function save(int $year,array $data,int $revision,int $actor): bool
    {
        try {
            $q=$this->database->connection()->prepare('SELECT COALESCE(MAX(revision),0) FROM parametros_anuales WHERE anio=?');
            $q->execute([$year]);if ((int)$q->fetchColumn()!==$revision) { return false; }
            // La clave única resuelve también dos inserciones simultáneas con la misma revisión.
            $q=$this->database->connection()->prepare('INSERT INTO parametros_anuales (anio,revision,datos,creado_por) VALUES (?,?,?,?)');
            $q->execute([$year,$revision+1,json_encode($data,JSON_THROW_ON_ERROR),$actor]);return true;
        } catch (\PDOException $e) { if (($e->errorInfo[1]??0)===1062) { return false; }throw $e; }
    }
}
