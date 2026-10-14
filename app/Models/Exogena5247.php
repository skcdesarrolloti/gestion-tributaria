<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use App\Services\Exogena5247Fields;

final class Exogena5247
{
    public function __construct(private Database $database) {}

    public function listFor(int $company, int $year, int $owner): array
    {
        return array_map([$this,'decode'],$this->query('SELECT x.* FROM exogena_5247 x
            JOIN contribuyentes c ON c.id=x.contribuyente_id
            WHERE c.id=? AND x.anio=? AND c.funcionario_id=? AND c.tipo=\'juridica\' ORDER BY x.id DESC',
            [$company,$year,$owner])->fetchAll());
    }

    public function findFor(int $id, int $owner): ?array
    {
        $row=$this->query('SELECT x.* FROM exogena_5247 x JOIN contribuyentes c ON c.id=x.contribuyente_id
            WHERE x.id=? AND c.funcionario_id=? AND c.tipo=\'juridica\'',[$id,$owner])->fetch();
        return $row?$this->decode($row):null;
    }

    public function saveFor(array $record, int $company, int $owner, ?int $id): void
    {
        $data=array_intersect_key($record,Exogena5247Fields::all());
        $json=json_encode($data,JSON_THROW_ON_ERROR|JSON_UNESCAPED_UNICODE);
        if ($id===null) {
            $query=$this->query('INSERT INTO exogena_5247 (contribuyente_id,anio,estructura,datos)
                SELECT id,?,?,? FROM contribuyentes WHERE id=? AND funcionario_id=? AND tipo=\'juridica\'',
                [$record['anio'],Exogena5247Fields::STRUCTURE,$json,$company,$owner]);
            if ($query->rowCount()!==1) { throw new \DomainException('Empresa no disponible.'); }
        } else {
            $this->query('UPDATE exogena_5247 x JOIN contribuyentes c ON c.id=x.contribuyente_id
                SET x.anio=?,x.datos=? WHERE x.id=? AND c.id=? AND c.funcionario_id=? AND c.tipo=\'juridica\'
                AND x.estructura=?',[$record['anio'],$json,$id,$company,$owner,Exogena5247Fields::STRUCTURE]);
        }
    }

    private function decode(array $row): array
    {
        $row['datos']=json_decode($row['datos'],true,512,JSON_THROW_ON_ERROR);
        return $row;
    }

    private function query(string $sql, array $values): \PDOStatement
    {
        $query=$this->database->connection()->prepare($sql);
        $query->execute($values);
        return $query;
    }
}
