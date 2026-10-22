<?php
declare(strict_types=1);
namespace App\Controllers;
use App\Core\{Database,View};
use App\Models\{Usuario,Contribuyente};
use App\Services\{AuthService,AccesoRoles,Csrf,CuentaRegistro,CuentaClaves,LoginLimiter};

final class CuentaController
{
    public function __construct(private AuthService $auth,private Usuario $users,private Database $database,private View $view,private array $config) {}
    private function user(bool $partner=false): array
    {
        $user=$this->auth->user();if (!$user) { \redirect($this->config,'login'); }
        if ($partner && !AccesoRoles::socio($user)) { http_response_code(403);echo 'Solo un Socio puede administrar cuentas.';exit; }
        return $user;
    }
    private function secure(bool $passwordAttempt=true): void
    {
        if (!Csrf::valid(\input($_POST,'_csrf_token'))) { http_response_code(419);echo 'Formulario vencido. Recarga antes de guardar.';exit; }
        if (!$passwordAttempt) { return; }
        $limiter=new LoginLimiter(($this->config['storage_path']??dirname(__DIR__,2).'/storage').'/rate-limits');
        if ($limiter->consume('cuenta|'.(int)$_SESSION['user_id'],10)>0) { http_response_code(429);echo 'Demasiados intentos. Espera 15 minutos.';exit; }
    }
    public function profile(): void
    {
        $user=$this->user();$errors=[];
        if ($_SERVER['REQUEST_METHOD']==='POST') {
            $this->secure();
            try {
                if (\input($_POST,'password')!==\input($_POST,'confirmation')) { throw new \InvalidArgumentException('Las contraseñas no coinciden.'); }
                $version=$this->users->changePassword((int)$user['_ID'],\input($_POST,'current_password'),\input($_POST,'password'),(int)$user['clave_version']);
                session_regenerate_id(true);$_SESSION['clave_version']=$version;unset($_SESSION['csrf']);
                header('Location: '.\url($this->config,'perfil').'&guardado=1',true,303);return;
            } catch (\InvalidArgumentException $e) { http_response_code(422);$errors[]=$e->getMessage(); }
            catch (\DomainException $e) { http_response_code(409);$errors[]=$e->getMessage(); }
        }
        $this->view->render('home/profile',compact('user','errors'));
    }
    public function create(): void
    {
        $user=$this->user(true);$errors=[];
        if ($_SERVER['REQUEST_METHOD']==='POST') {
            $this->secure(false);
            try {
                if (\input($_POST,'password')!==\input($_POST,'confirmation')) { throw new \InvalidArgumentException('Las contraseñas no coinciden.'); }
                $role=\input($_POST,'perfil');$taxpayer=$role==='socio'?null:(filter_var(\input($_POST,'contribuyente_id'),FILTER_VALIDATE_INT,['options'=>['min_range'=>1]])?:null);
                $id=(new CuentaRegistro($this->database))->create((int)$user['_ID'],\input($_POST,'username'),\input($_POST,'nombre'),\input($_POST,'correo'),\input($_POST,'password'),$role,$taxpayer);
                header('Location: '.\url($this->config,'accesos').'&funcionario='.$id.'&creado=1',true,303);return;
            } catch (\InvalidArgumentException $e) { http_response_code(422);$errors[]=$e->getMessage(); }
            catch (\DomainException $e) { http_response_code(409);$errors[]=$e->getMessage(); }
        }
        $taxpayers=(new Contribuyente($this->database))->listFor((int)$user['_ID']);
        $this->view->render('accesos/create',compact('user','errors','taxpayers'));
    }
    public function reset(): void
    {
        $user=$this->user(true);$this->secure();
        try {
            if (\input($_POST,'password')!==\input($_POST,'confirmation')) { throw new \InvalidArgumentException('Las contraseñas no coinciden.'); }
            $target=filter_var(\input($_POST,'usuario_id'),FILTER_VALIDATE_INT,['options'=>['min_range'=>1]])?:0;
            (new CuentaClaves($this->database))->reset((int)$user['_ID'],$target,\input($_POST,'current_password'),\input($_POST,'password'));
            header('Location: '.\url($this->config,'accesos').'&funcionario='.$target.'&restablecido=1',true,303);
        } catch (\InvalidArgumentException $e) { http_response_code(422);echo \e($e->getMessage()); }
        catch (\DomainException $e) { http_response_code(409);echo \e($e->getMessage()); }
    }
}
