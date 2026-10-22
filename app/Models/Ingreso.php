<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

final class Ingreso
{
    public const PROFILE_FIELDS = ['anio','residencia','regimen','servicios','soporte_perfil'];
    public const MOVEMENT_FIELDS = ['concepto_id','fecha','estado','descripcion','tercero','pais','moneda',
        'valor_original','conversion','valor_cop','soporte','justificacion'];

    public function __construct(private Database $database) {}

    public function profiles(int $taxpayer, int $owner): array
    {
        return $this->query('SELECT p.* FROM perfiles_renta p JOIN contribuyentes c ON c.id=p.contribuyente_id
            WHERE c.id=? AND '.\App\Services\AccesoSql::condition('c',false).' ORDER BY p.anio DESC', [$taxpayer,$owner])->fetchAll();
    }

    public function profileFor(int $id, int $owner): ?array
    {
        return $this->query('SELECT p.*, c.nombre,c.funcionario_id FROM perfiles_renta p JOIN contribuyentes c ON c.id=p.contribuyente_id
            WHERE p.id=? AND '.\App\Services\AccesoSql::condition('c',false), [$id,$owner])->fetch() ?: null;
    }

    public function createProfile(array $record, array $taxpayer, int $owner): int
    {
        $form = $taxpayer['tipo'] !== 'juridica' && $record['residencia'] === 'residente' ? '210' : '110';
        $this->query('INSERT INTO perfiles_renta (contribuyente_id,anio,tipo,residencia,regimen,servicios,soporte_perfil,formulario,version)
            SELECT id,?,?,?,?,?,?,?,? FROM contribuyentes WHERE id=? AND '.\App\Services\AccesoSql::condition('contribuyentes',true),
            [$record['anio'],$taxpayer['tipo'],$record['residencia'],$record['regimen'],$record['servicios'],
                $record['soporte_perfil'],$form,'2026-10',$taxpayer['id'],$owner]);
        return (int) $this->database->connection()->lastInsertId();
    }

    public function concepts(array $profile): array
    {
        return $this->query('SELECT * FROM conceptos_ingresos WHERE formulario=? AND version=? ORDER BY casilla',
            [$profile['formulario'],$profile['version']])->fetchAll();
    }

    public function updateProfile(array $record, int $id, int $owner): void
    {
        $db=$this->database->connection();
        $db->beginTransaction();
        try {
            $profile=$this->lockedProfile($id,$owner);
            if (!$profile) { throw new \DomainException('Perfil no encontrado.'); }
            $count=(int)$this->query('SELECT COUNT(*) FROM movimientos_ingresos WHERE perfil_id=?',[$id])->fetchColumn();
            foreach (['anio','residencia','regimen','servicios'] as $field) {
                if ($count>0 && (string)$record[$field] !== (string)$profile[$field]) {
                    throw new \DomainException('Este perfil ya tiene movimientos. Puedes actualizar la evidencia, pero no cambiar el año, residencia, régimen o tratamiento sin revisar esos registros.');
                }
            }
            $form=$profile['tipo']!=='juridica' && $record['residencia']==='residente'?'210':'110';
            $this->query('UPDATE perfiles_renta SET anio=?,residencia=?,regimen=?,servicios=?,soporte_perfil=?,formulario=? WHERE id=?',
                [$record['anio'],$record['residencia'],$record['regimen'],$record['servicios'],$record['soporte_perfil'],$form,$id]);
            $db->commit();
        } catch (\Throwable $error) { $db->rollBack(); throw $error; }
    }

    public function movements(int $profile, int $owner): array
    {
        return $this->query('SELECT m.*, k.casilla, k.nombre FROM movimientos_ingresos m
            JOIN perfiles_renta p ON p.id=m.perfil_id JOIN contribuyentes c ON c.id=p.contribuyente_id
            JOIN conceptos_ingresos k ON k.id=m.concepto_id WHERE p.id=? AND '.\App\Services\AccesoSql::condition('c',false).' ORDER BY m.fecha,m.id',
            [$profile,$owner])->fetchAll();
    }

    public function saveMovement(array $record, int $profile, int $owner, ?int $id): void
    {
        $db=$this->database->connection();
        $db->beginTransaction();
        try {
            $current=$this->lockedProfile($profile,$owner);
            if (!$current) { throw new \DomainException('Perfil no encontrado.'); }
            $concept=$this->query('SELECT * FROM conceptos_ingresos WHERE id=? AND formulario=? AND version=?',
                [$record['concepto_id'],$current['formulario'],$current['version']])->fetch() ?: null;
            $errors=\App\Services\IngresoData::movementErrors($record,$current,$concept);
            if ($errors) { throw new \DomainException(implode(' ',$errors)); }
            $this->writeMovement($record,$profile,$owner,$id);
            $db->commit();
        } catch (\Throwable $error) { $db->rollBack(); throw $error; }
    }

    private function writeMovement(array $record, int $profile, int $owner, ?int $id): void
    {
        $values = array_map(static fn ($field) => $record[$field], self::MOVEMENT_FIELDS);
        if ($id === null) {
            $sql = 'INSERT INTO movimientos_ingresos ('.implode(',',self::MOVEMENT_FIELDS).',perfil_id)
                SELECT '.implode(',',array_fill(0,count($values),'?')).',p.id FROM perfiles_renta p
                JOIN contribuyentes c ON c.id=p.contribuyente_id WHERE p.id=? AND '.\App\Services\AccesoSql::condition('c',true);
            array_push($values,$profile,$owner);
        } else {
            $set = implode(',',array_map(static fn ($field) => "m.$field=?", self::MOVEMENT_FIELDS));
            $sql = "UPDATE movimientos_ingresos m JOIN perfiles_renta p ON p.id=m.perfil_id
                JOIN contribuyentes c ON c.id=p.contribuyente_id SET $set WHERE m.id=? AND p.id=? AND ".\App\Services\AccesoSql::condition('c',true);
            array_push($values,$id,$profile,$owner);
        }
        $this->query($sql,$values);
    }

    private function lockedProfile(int $id, int $owner): ?array
    {
        return $this->query('SELECT p.* FROM perfiles_renta p JOIN contribuyentes c ON c.id=p.contribuyente_id
            WHERE p.id=? AND '.\App\Services\AccesoSql::condition('c',true).' FOR UPDATE',[$id,$owner])->fetch() ?: null;
    }

    private function query(string $sql, array $values): \PDOStatement
    {
        $query = $this->database->connection()->prepare($sql);
        $query->execute($values);
        return $query;
    }
}
