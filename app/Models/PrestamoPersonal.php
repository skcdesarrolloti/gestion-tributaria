<?php
declare(strict_types=1);
namespace App\Models;
use App\Core\Database;
use App\Services\AccesoSql;

final class PrestamoPersonal
{
    public function __construct(private Database $database) {}
    public function listFor(int $taxpayer,int $owner): array
    {
        $q=$this->database->connection()->prepare('SELECT p.* FROM prestamos_documentos p JOIN contribuyentes c ON c.id=p.contribuyente_id WHERE c.id=? AND '.AccesoSql::condition('c').' ORDER BY p.credito,p.fecha_pago,p.fecha_factura');
        $q->execute([$taxpayer,$owner]);$rows=$q->fetchAll();foreach ($rows as &$r) { $r['datos']=json_decode($r['datos'],true,64,JSON_THROW_ON_ERROR); }return $rows;
    }
    public function findFor(int $id,int $owner): ?array
    {
        $q=$this->database->connection()->prepare('SELECT p.* FROM prestamos_documentos p JOIN contribuyentes c ON c.id=p.contribuyente_id WHERE p.id=? AND '.AccesoSql::condition('c'));
        $q->execute([$id,$owner]);$r=$q->fetch();if ($r) { $r['datos']=json_decode($r['datos'],true,64,JSON_THROW_ON_ERROR); }return $r?:null;
    }
    public function save(int $taxpayer,int $owner,array $file,array $reading): int
    {
        $db=$this->database->connection();$db->beginTransaction();
        try {
            $q=$db->prepare('SELECT id FROM contribuyentes WHERE id=? AND tipo="natural" AND '.AccesoSql::condition('contribuyentes',true).' FOR UPDATE');$q->execute([$taxpayer,$owner]);
            if (!$q->fetchColumn()) { throw new \DomainException('Comprueba la ficha personal y tu permiso para guardar.'); }
            foreach ($this->listFor($taxpayer,$owner) as $old) {
                if ($old['sha256']===$file['sha256']) { throw new \DomainException('Ese PDF ya está guardado. No se ha duplicado.'); }
                if ($old['credito']===$reading['credito'] && ($old['fecha_factura']===$reading['factura'] || $old['fecha_pago']===$reading['pago'])) { throw new \DomainException('Ya existe ese corte o pago del crédito. Revisa el documento guardado antes de añadir otro.'); }
            }
            $q=$db->prepare('INSERT INTO prestamos_documentos (contribuyente_id,credito,fecha_factura,fecha_pago,titulo,archivo,sha256,datos,actor_id) VALUES (?,?,?,?,?,?,?,?,?)');
            $q->execute([$taxpayer,$reading['credito'],$reading['factura'],$reading['pago'],$file['titulo'],$file['archivo'],$file['sha256'],json_encode($reading,JSON_THROW_ON_ERROR|JSON_UNESCAPED_UNICODE),$owner]);
            $id=(int)$db->lastInsertId();$db->commit();return $id;
        } catch (\Throwable $e) { $db->rollBack();throw $e; }
    }
}
