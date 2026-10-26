<?php
declare(strict_types=1);
namespace App\Models;
use App\Core\Database;
use App\Services\{AccesoSql,NominaData};

final class NominaLaboral
{
    public function __construct(private Database $database) {}
    public function rows(int $profile,int $owner): array
    {
        $rows=$this->q('SELECT n.* FROM nominas_laborales n JOIN perfiles_renta p ON p.id=n.perfil_id
            JOIN contribuyentes c ON c.id=p.contribuyente_id WHERE p.id=? AND '.AccesoSql::condition('c').' ORDER BY n.inicio,n.fin,n.id',[$profile,$owner])->fetchAll();
        foreach ($rows as &$r) { $r['datos']=json_decode($r['datos'],true,32,JSON_THROW_ON_ERROR); }
        return $rows;
    }
    public function existing(array $r,int $owner): ?array
    {
        return $this->q('SELECT n.* FROM nominas_laborales n JOIN perfiles_renta p ON p.id=n.perfil_id
            JOIN contribuyentes c ON c.id=p.contribuyente_id WHERE n.perfil_id=? AND n.empleador_documento=? AND n.inicio=? AND n.fin=? AND '.AccesoSql::condition('c'),
            [$r['perfil_id'],$r['empleador_documento'],$r['inicio'],$r['fin'],$owner])->fetch()?:null;
    }
    public function save(array $records,int $owner,bool $replace): array
    {
        if (!$records || count($records)>150) { throw new \DomainException('Selecciona entre una y 150 filas.'); }
        $db=$this->database->connection();$db->beginTransaction();$saved=0;$duplicates=0;$keys=[];
        try {
            foreach ($records as $r) {
                $p=$this->q('SELECT p.*,c.documento_numero,c.tipo AS tipo_actual FROM perfiles_renta p JOIN contribuyentes c ON c.id=p.contribuyente_id
                    WHERE p.id=? AND '.AccesoSql::condition('c',true).' FOR UPDATE',[$r['perfil_id'],$owner])->fetch();
                if (!$p) { throw new \DomainException('El contribuyente ya no está disponible para tu cuenta.'); }
                $validated=NominaData::read($r,['id'=>$p['contribuyente_id'],'tipo'=>$p['tipo_actual'],'documento_numero'=>$p['documento_numero']],$p);
                if (!hash_equals($validated['huella'],$r['huella'])) { throw new \DomainException('La fila cambió desde su lectura.'); }
                $key=implode('|',[$r['perfil_id'],$r['empleador_documento'],$r['inicio'],$r['fin']]);
                if (isset($keys[$key])) { throw new \DomainException('Seleccionaste dos versiones del mismo empleado y período. Elige una.'); }$keys[$key]=true;
                $old=$this->existing($r,$owner);
                if ($old && hash_equals($old['huella'],$r['huella'])) { $duplicates++;continue; }
                if (($old && (!$replace || (int)$old['revision']!==(int)$r['revision'])) || (!$old && $r['revision']!==0)) { throw new \DomainException('La nómina cambió o requiere confirmar su corrección. Vuelve a revisar el archivo.'); }
                $overlap=$this->q('SELECT id FROM nominas_laborales WHERE perfil_id=? AND empleador_documento=? AND inicio<=? AND fin>=? AND id<>?',
                    [$r['perfil_id'],$r['empleador_documento'],$r['fin'],$r['inicio'],$old['id']??0])->fetchColumn();
                if ($overlap) { throw new \DomainException('Este período se superpone con otra nómina del mismo empleador. No se sumará dos veces.'); }
                $monthStart=substr($r['inicio'],0,7).'-01';
                $nextMonth=(new \DateTimeImmutable($monthStart))->modify('+1 month')->format('Y-m-d');
                $manual=$this->q('SELECT m.id FROM movimientos_ingresos m JOIN conceptos_ingresos k ON k.id=m.concepto_id
                    WHERE m.perfil_id=? AND m.estado="real" AND k.casilla IN (32,33) AND m.fecha>=? AND m.fecha<?
                    AND NOT EXISTS (SELECT 1 FROM nominas_laborales n WHERE n.ingreso_id=m.id OR n.aporte_id=m.id) LIMIT 1',[$p['id'],$monthStart,$nextMonth])->fetchColumn();
                if ($manual) { throw new \DomainException('Ya hay captura manual laboral en ese mes. Revisa esos movimientos antes de sumar la nómina para evitar duplicados.'); }
                $amounts=NominaData::amounts($r['valores']);
                $income=$this->movement($p,$r,32,$r['valores']['devengado'],$old['ingreso_id']??null);
                $contribution=$this->movement($p,$r,33,$amounts['aportes'],$old['aporte_id']??null);
                $json=json_encode($validated,JSON_THROW_ON_ERROR|JSON_UNESCAPED_UNICODE);
                if ($old) {
                    $this->q('INSERT INTO historial_nominas (nomina_id,revision,datos,huella,actor_id) VALUES (?,?,?,?,?)',[$old['id'],$old['revision'],$old['datos'],$old['huella'],$owner]);
                    $this->q('UPDATE nominas_laborales SET datos=?,huella=?,ingreso_id=?,aporte_id=?,actor_id=?,revision=revision+1 WHERE id=?',[$json,$r['huella'],$income,$contribution,$owner,$old['id']]);
                } else {
                    $this->q('INSERT INTO nominas_laborales (perfil_id,empleador_documento,inicio,fin,datos,huella,ingreso_id,aporte_id,actor_id) VALUES (?,?,?,?,?,?,?,?,?)',
                        [$p['id'],$r['empleador_documento'],$r['inicio'],$r['fin'],$json,$r['huella'],$income,$contribution,$owner]);
                }
                $saved++;
            }
            $db->commit();return ['guardadas'=>$saved,'duplicadas'=>$duplicates];
        } catch (\Throwable $e) { $db->rollBack();throw $e; }
    }
    private function movement(array $p,array $r,int $code,int $cents,?int $id): ?int
    {
        $amount=(int)round($cents/100);
        if ($amount===0 && !$id) { return null; }
        $concept=$this->q('SELECT id FROM conceptos_ingresos WHERE formulario="210" AND version=? AND casilla=?',[$p['version'],$code])->fetchColumn();
        if (!$concept) { throw new \DomainException('Falta el concepto DIAN para importar esta nómina.'); }
        $desc='Nómina '.$r['inicio'].' a '.$r['fin'].' · '.($code===32?'Devengado':'Salud y pensión del trabajador');
        $support=mb_substr($r['archivo'].' · '.$r['hoja'].' · SHA256 '.$r['archivo_sha'],0,500);
        $values=[$concept,$r['fin'],'real',$desc,$r['empleador'],'Colombia','COP',(string)$amount,'',$amount,$support,$code===33?'Aportes obligatorios laborales: ET 55 y 56; revisar certificado 220.':''];
        if ($id) {
            $set=implode(',',array_map(static fn($f)=>$f.'=?',Ingreso::MOVEMENT_FIELDS));
            $this->q('UPDATE movimientos_ingresos SET '.$set.' WHERE id=? AND perfil_id=?',[...$values,$id,$p['id']]);return $id;
        }
        $this->q('INSERT INTO movimientos_ingresos ('.implode(',',Ingreso::MOVEMENT_FIELDS).',perfil_id) VALUES ('.implode(',',array_fill(0,13,'?')).')',[...$values,$p['id']]);
        return (int)$this->database->connection()->lastInsertId();
    }
    private function q(string $sql,array $values): \PDOStatement
    {
        $q=$this->database->connection()->prepare($sql);$q->execute($values);return $q;
    }
}
