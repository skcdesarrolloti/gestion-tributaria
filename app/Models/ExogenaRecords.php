<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

class ExogenaRecords
{
    private string $table;
    private string $scope='';
    private string $structure;
    private array $columns;
    private bool $shared=false;
    private string $code;

    public function __construct(private Database $database, string $code, string|array $fields)
    {
        $this->code=$code;
        if (is_array($fields)) {
            if (!\App\Services\ExogenaSchemas::has($code) || $fields['code']!==$code) { throw new \InvalidArgumentException('Formato no disponible.'); }
            $this->table='exogena_registros'; $this->scope=" AND x.formato='$code'";
            $this->columns=$fields['fields']; $this->structure=$fields['structure']; $this->shared=true;
            return;
        }
        $this->columns=$fields::all(); $this->structure=$fields::STRUCTURE;
        $this->table=match($code) { '5247'=>'exogena_5247', '1007'=>'exogena_1007', '1001'=>'exogena_1001',
            default=>throw new \InvalidArgumentException('Formato no disponible.') };
    }

    public function listFor(int $company, int $year, int $owner): array
    {
        return array_map([$this,'decode'],$this->query('SELECT x.* FROM '.$this->table.' x
            JOIN contribuyentes c ON c.id=x.contribuyente_id
            WHERE c.id=? AND x.anio=? AND c.funcionario_id=? AND c.tipo=\'juridica\''.$this->scope.' ORDER BY x.id DESC',
            [$company,$year,$owner])->fetchAll());
    }

    public function findFor(int $id, int $owner): ?array
    {
        $row=$this->query('SELECT x.* FROM '.$this->table.' x JOIN contribuyentes c ON c.id=x.contribuyente_id
            WHERE x.id=? AND c.funcionario_id=? AND c.tipo=\'juridica\''.$this->scope,[$id,$owner])->fetch();
        return $row?$this->decode($row):null;
    }

    public function countsFor(int $company, int $year, int $owner): array
    {
        $parts=[]; $values=[];
        foreach (['1001'=>'exogena_1001','1007'=>'exogena_1007','5247'=>'exogena_5247','shared'=>'exogena_registros'] as $code=>$table) {
            $select=$code==='shared'?'x.formato':"'$code'";
            $group=$code==='shared'?' GROUP BY x.formato':'';
            $parts[]='SELECT '.$select.' AS formato, COUNT(*) AS total FROM '.$table.' x
                JOIN contribuyentes c ON c.id=x.contribuyente_id
                WHERE c.id=? AND x.anio=? AND c.funcionario_id=? AND c.tipo=\'juridica\''.$group;
            array_push($values,$company,$year,$owner);
        }
        return $this->query(implode(' UNION ALL ',$parts),$values)->fetchAll(\PDO::FETCH_KEY_PAIR);
    }

    public function saveFor(array $record, int $company, int $owner, ?int $id): void
    {
        $data=array_intersect_key($record,$this->columns);
        $json=json_encode($data,JSON_THROW_ON_ERROR|JSON_UNESCAPED_UNICODE);
        if ($id===null) {
            $extraColumn=$this->shared?',formato':'';
            $extraValue=$this->shared?',?':'';
            $values=[$record['anio'],$this->structure,$json];
            if ($this->shared) { $values[]=$this->code; }
            array_push($values,$company,$owner);
            $query=$this->query('INSERT INTO '.$this->table.' (contribuyente_id,anio,estructura,datos'.$extraColumn.')
                SELECT id,?,?,?'.$extraValue.' FROM contribuyentes WHERE id=? AND funcionario_id=? AND tipo=\'juridica\'',
                $values);
            if ($query->rowCount()!==1) { throw new \DomainException('Empresa no disponible.'); }
        } else {
            $this->query('UPDATE '.$this->table.' x JOIN contribuyentes c ON c.id=x.contribuyente_id
                SET x.anio=?,x.datos=? WHERE x.id=? AND c.id=? AND c.funcionario_id=? AND c.tipo=\'juridica\'
                AND x.estructura=?'.$this->scope,[$record['anio'],$json,$id,$company,$owner,$this->structure]);
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
