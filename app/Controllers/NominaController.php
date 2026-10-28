<?php
declare(strict_types=1);
namespace App\Controllers;
use App\Models\{Contribuyente,Ingreso,NominaLaboral};
use App\Services\{AuthService,Csrf,AccesoRoles,NominaData};
use App\Core\View;

final class NominaController
{
    public function __construct(private AuthService $auth,private Contribuyente $taxpayers,private Ingreso $incomes,
        private NominaLaboral $model,private View $view,private array $config) {}
    public function index(): void
    {
        $user=$this->auth->user();if (!$user) { \redirect($this->config,'login'); }
        $owner=(int)$user['_ID'];$year=\input($_GET,'anio','2026');
        if (!in_array($year,['2025','2026'],true)) { http_response_code(422);echo 'Selecciona 2025 o 2026.';return; }
        $rows=$this->taxpayers->listFor($owner);$targets=[];$employers=[];$errors=[];$preview=null;$notice=null;$taxpayer=null;
        $navigationId=filter_var(\input($_GET,'contribuyente'),FILTER_VALIDATE_INT,['options'=>['min_range'=>1]])?:0;
        foreach ($rows as $t) {
            if ((int)$t['id']===$navigationId) { $taxpayer=$t; }
            if ($t['tipo']==='juridica') { $employers[]=['nit'=>$t['documento_numero'],'nombre'=>$t['nombre']];continue; }
            if ($t['tipo']!=='natural') { continue; }
            $profiles=$this->incomes->profiles((int)$t['id'],$owner);$p=null;
            foreach ($profiles as $candidate) { if ((string)$candidate['anio']===$year) { $p=$candidate;break; } }
            $targets[]=['id'=>(int)$t['id'],'documento'=>$t['documento_numero'],'nombre'=>$t['nombre'],
                'perfil'=>$p && $p['formulario']==='210' && $p['regimen']==='ordinario'?(int)$p['id']:0];
        }
        if (isset($_GET['contribuyente']) && !$taxpayer) { http_response_code(404);echo 'Contribuyente no encontrado.';return; }
        if ($_SERVER['REQUEST_METHOD']==='POST') {
            if (!Csrf::valid(\input($_POST,'_csrf_token'))) { http_response_code(419);$errors[]='Formulario vencido. Recarga antes de continuar.'; }
            else {
                try {
                    if (\input($_POST,'accion')==='guardar') {
                        $staged=$_SESSION['nomina_preview']??null;
                        if (!$staged || $staged['owner']!==$owner || $staged['year']!==$year || $staged['expires']<time()
                            || !hash_equals($staged['token'],\input($_POST,'preview_token'))) { throw new \DomainException('La vista previa venció. Selecciona de nuevo las hojas.'); }
                        $replace=\input($_POST,'reemplazar')==='1';
                        $preview=$staged;
                        $notice=$this->model->save($staged['records'],$owner,$replace);unset($_SESSION['nomina_preview']);$preview=null;
                    } else {
                        $staged=$_SESSION['nomina_preview']??null;unset($_SESSION['nomina_preview']);$json=\input($_POST,'filas');
                        if (strlen($json)>600000) { throw new \DomainException('La selección es demasiado grande. Importa menos períodos.'); }
                        if (\input($_POST,'accion')==='ajustar_periodo') {
                            if (!$staged || $staged['owner']!==$owner || $staged['year']!==$year || $staged['expires']<time()
                                || !hash_equals($staged['token'],\input($_POST,'preview_token'))) {
                                throw new \DomainException('La vista previa venció. Selecciona de nuevo las hojas.');
                            }
                            $index=filter_var(\input($_POST,'fila'),FILTER_VALIDATE_INT);
                            if ($index===false || !isset($staged['records'][$index])) { throw new \DomainException('Selecciona la quincena que deseas corregir.'); }
                            $raw=$staged['records'];
                            $raw[$index]['periodo_origen']=$raw[$index]['periodo_origen']??($raw[$index]['inicio'].' a '.$raw[$index]['fin']);
                            $raw[$index]['inicio']=\input($_POST,'inicio');$raw[$index]['fin']=\input($_POST,'fin');
                        } else { $raw=json_decode($json,true,32,JSON_THROW_ON_ERROR); }
                        if (!is_array($raw) || !array_is_list($raw) || !$raw || count($raw)>150) { throw new \DomainException('Selecciona entre una y 150 filas de nómina.'); }
                        $records=[];$seen=[];
                        foreach ($raw as $entry) {
                            if (!is_array($entry)) { throw new \DomainException('Fila inválida.'); }
                            $t=$this->taxpayers->findFor((int)($entry['contribuyente_id']??0),$owner);
                            $p=$this->incomes->profileFor((int)($entry['perfil_id']??0),$owner);
                            if (!$t || !$p || (int)$p['contribuyente_id']!==(int)$t['id'] || (string)$p['anio']!==$year || !AccesoRoles::edit($user,$t)) { throw new \DomainException('Una fila no corresponde a un contribuyente y año autorizados. Prepara su año en Ingresos.'); }
                            $r=NominaData::read($entry,$t,$p);$key=implode('|',[$p['id'],$r['empleador_documento'],$r['inicio'],$r['fin']]);
                            if (isset($seen[$key])) { throw new \DomainException('Hay dos versiones del mismo empleado y período. Selecciona una sola.'); }$seen[$key]=true;
                            $old=$this->model->existing($r,$owner);$r['revision']=(int)($old['revision']??0);
                            $r['estado']=$old?(hash_equals($old['huella'],$r['huella'])?'Ya importada':'Corrección'):'Nueva';$r['nombre']=$t['nombre'];$records[]=$r;
                        }
                        $preview=['owner'=>$owner,'year'=>$year,'expires'=>time()+1200,'token'=>bin2hex(random_bytes(24)),'records'=>$records];
                        $_SESSION['nomina_preview']=$preview;
                    }
                } catch (\JsonException) { http_response_code(422);$errors[]='No fue posible leer la selección. Vuelve a elegir las hojas del Excel.'; }
                catch (\DomainException $e) { http_response_code(422);$errors[]=$e->getMessage(); }
                catch (\Throwable $e) {
                    error_log((string)$e);
                    http_response_code(500);$errors[]=\App\Services\NominaFailure::message($e);
                }
            }
        }
        $this->view->render('nomina/index',compact('user','year','taxpayer','targets','employers','errors','preview','notice'));
    }
}
