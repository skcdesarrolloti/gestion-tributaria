<?php
declare(strict_types=1);
namespace App\Models;
use App\Core\Database;
use App\Services\AccesoSql;

final class ComprobantePension
{
    public function __construct(private Database $database) {}
    public function documents(int $profile,int $owner): array
    {
        return $this->q('SELECT d.* FROM comprobantes_pension d JOIN perfiles_renta p ON p.id=d.perfil_id JOIN contribuyentes c ON c.id=p.contribuyente_id
            WHERE p.id=? AND '.AccesoSql::condition('c').' ORDER BY d.id DESC',[$profile,$owner])->fetchAll();
    }
    public function findFor(int $id,int $owner): ?array
    {
        $row=$this->q('SELECT d.*,p.anio,p.contribuyente_id FROM comprobantes_pension d JOIN perfiles_renta p ON p.id=d.perfil_id JOIN contribuyentes c ON c.id=p.contribuyente_id
            WHERE d.id=? AND '.AccesoSql::condition('c'),[$id,$owner])->fetch();
        if ($row) { $row['lectura']=json_decode($row['lectura'],true,64,JSON_THROW_ON_ERROR); }return $row?:null;
    }
    public function rows(int $profile,int $owner): array
    {
        $rows=$this->q('SELECT m.*,d.titulo FROM mesadas_pension m JOIN comprobantes_pension d ON d.id=m.comprobante_id
            JOIN perfiles_renta p ON p.id=m.perfil_id JOIN contribuyentes c ON c.id=p.contribuyente_id
            WHERE p.id=? AND m.vigente=1 AND '.AccesoSql::condition('c').' ORDER BY m.mes',[$profile,$owner])->fetchAll();
        foreach ($rows as &$row) { $row['datos']=json_decode($row['datos'],true,32,JSON_THROW_ON_ERROR); }return $rows;
    }
    public function store(int $profile,int $owner,array $file): array
    {
        $db=$this->database->connection();$db->beginTransaction();
        try {
            $this->profile($profile,$owner);
            $old=$this->q('SELECT id FROM comprobantes_pension WHERE perfil_id=? AND sha256=?',[$profile,$file['sha256']])->fetchColumn();
            if ($old) { $db->commit();return ['id'=>(int)$old,'duplicado'=>true]; }
            $this->q('INSERT INTO comprobantes_pension (perfil_id,titulo,archivo,sha256,bytes,lectura,actor_id) VALUES (?,?,?,?,?,?,?)',
                [$profile,$file['titulo'],$file['archivo'],$file['sha256'],$file['bytes'],json_encode($file['lectura'],JSON_THROW_ON_ERROR|JSON_UNESCAPED_UNICODE),$owner]);
            $id=(int)$db->lastInsertId();$db->commit();return ['id'=>$id,'duplicado'=>false];
        } catch (\Throwable $e) { $db->rollBack();throw $e; }
    }
    public function confirm(int $document,int $owner,array $posted,array $expected,bool $replace): void
    {
        $db=$this->database->connection();$db->beginTransaction();
        try {
            $doc=$this->findFor($document,$owner);
            if (!$doc) { throw new \DomainException('Comprobante no encontrado.'); }
            $profile=$this->profile((int)$doc['perfil_id'],$owner);
            $rows=\App\Services\PensionImportData::rows($posted,(string)$profile['anio'],$doc['lectura']);
            $originalMonths=$this->q('SELECT DISTINCT mes FROM mesadas_pension WHERE comprobante_id=? ORDER BY mes',[$document])->fetchAll(\PDO::FETCH_COLUMN);
            $selectedMonths=array_column($rows,'mes');sort($selectedMonths);
            if ($originalMonths && array_map('intval',$originalMonths)!==$selectedMonths) { throw new \DomainException('Este PDF ya está asociado a otros meses. No se utiliza de nuevo para sumar una segunda mesada.'); }
            foreach ($rows as $row) {
                $old=$this->q('SELECT * FROM mesadas_pension WHERE perfil_id=? AND mes=? AND vigente=1 FOR UPDATE',[$profile['id'],$row['mes']])->fetch();
                if ((int)($old['id']??0)!==(int)($expected[$row['mes']]??0)) { throw new \DomainException('El mes cambió en otra ventana. Vuelve a abrir el comprobante antes de guardar.'); }
                if ($old && (int)$old['comprobante_id']===$document && json_decode($old['datos'],true)===$row) { continue; }
                if ($old && !$replace) { throw new \DomainException('Ya existe una mesada en ese mes. Marca «Corregir meses ya importados» después de revisar; no se sumará otra vez.'); }
                $next=(new \DateTimeImmutable($row['periodo'].'-01'))->modify('+1 month')->format('Y-m-d');
                $manual=$this->q('SELECT m.id FROM movimientos_ingresos m JOIN conceptos_ingresos k ON k.id=m.concepto_id
                    WHERE m.perfil_id=? AND m.estado="real" AND k.casilla IN (99,100) AND m.fecha>=? AND m.fecha<?
                    AND NOT EXISTS(SELECT 1 FROM mesadas_pension s WHERE s.vigente=1 AND (s.ingreso_id=m.id OR s.aporte_id=m.id)) LIMIT 1',
                    [$profile['id'],$row['periodo'].'-01',$next])->fetchColumn();
                if ($manual) { throw new \DomainException('Ese mes tiene datos de pensión escritos manualmente. Revísalos antes de importar para evitar duplicarlos.'); }
                $income=$this->movement($profile,$document,$row,99,$row['bruto'],$old['ingreso_id']??null);
                $health=$this->movement($profile,$document,$row,100,$row['salud'],$old['aporte_id']??null);
                if ($old) { $this->q('UPDATE mesadas_pension SET vigente=NULL WHERE id=?',[$old['id']]); }
                $this->q('INSERT INTO mesadas_pension (perfil_id,mes,comprobante_id,datos,ingreso_id,aporte_id,actor_id) VALUES (?,?,?,?,?,?,?)',
                    [$profile['id'],$row['mes'],$document,json_encode($row,JSON_THROW_ON_ERROR),$income,$health,$owner]);
            }
            $db->commit();
        } catch (\Throwable $e) { $db->rollBack();throw $e; }
    }
    private function profile(int $id,int $owner): array
    {
        $profile=$this->q('SELECT p.* FROM perfiles_renta p JOIN contribuyentes c ON c.id=p.contribuyente_id WHERE p.id=? AND '.AccesoSql::condition('c',true).' FOR UPDATE',[$id,$owner])->fetch();
        if (!$profile || $profile['tipo']!=='natural' || $profile['formulario']!=='210') { throw new \DomainException('Prepara el año de la persona natural y comprueba su acceso antes de importar.'); }return $profile;
    }
    private function movement(array $profile,int $document,array $row,int $code,int $cents,?int $id): int
    {
        $concept=$this->q('SELECT id FROM conceptos_ingresos WHERE formulario="210" AND version=? AND casilla=?',[$profile['version'],$code])->fetchColumn();
        if (!$concept) { throw new \DomainException('No está disponible la casilla de pensión del año.'); }
        $amount=(string)(int)round($cents/100);
        $data=[$concept,$row['periodo'].'-01','real','Colpensiones · '.$row['periodo'].' · '.($code===99?'Pensión bruta':'Salud obligatoria'),
            'COLPENSIONES','Colombia','COP',$amount,'',$amount,'Comprobante Colpensiones #'.$document,'ET 56 y 337; comprobante mensual revisado'];
        if ($id) { $this->q('UPDATE movimientos_ingresos SET '.implode(',',array_map(static fn($k)=>$k.'=?',Ingreso::MOVEMENT_FIELDS)).' WHERE id=? AND perfil_id=?',[...$data,$id,$profile['id']]);return $id; }
        $this->q('INSERT INTO movimientos_ingresos ('.implode(',',Ingreso::MOVEMENT_FIELDS).',perfil_id) VALUES ('.implode(',',array_fill(0,13,'?')).')',[...$data,$profile['id']]);
        return (int)$this->database->connection()->lastInsertId();
    }
    private function q(string $sql,array $values): \PDOStatement
    { $q=$this->database->connection()->prepare($sql);$q->execute($values);return $q; }
}
