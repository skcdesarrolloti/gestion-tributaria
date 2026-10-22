<?php
declare(strict_types=1);
namespace App\Controllers;
use App\Core\View;
use App\Models\Usuario;
use App\Services\{ActivacionCuenta,Csrf,LoginLimiter};

final class ActivacionController
{
    public function __construct(private Usuario $users,private ActivacionCuenta $activation,private View $view,private array $config) {}
    public function index(): void
    {
        if ($this->users->exists()) { \redirect($this->config,'login'); }
        $this->activation->prepare();$errors=[];
        if ($_SERVER['REQUEST_METHOD']==='POST') {
            if (!Csrf::valid(\input($_POST,'_csrf_token'))) { http_response_code(419);$errors[]='Formulario vencido. Recarga antes de guardar.'; }
            else {
                $limiter=new LoginLimiter(($this->config['storage_path']??dirname(__DIR__,2).'/storage').'/rate-limits');
                if ($limiter->consume('activacion|'.($_SERVER['REMOTE_ADDR']??'unknown'),10)>0) { http_response_code(429);$errors[]='Demasiados intentos. Espera 15 minutos.'; }
                else { try { $this->activation->activate($_POST);$_SESSION=[];session_regenerate_id(true);\redirect($this->config,'login'); }
                    catch (\InvalidArgumentException $e) { http_response_code(422);$errors[]=$e->getMessage(); }
                    catch (\DomainException $e) { http_response_code(409);$errors[]=$e->getMessage(); }
                }
            }
        }
        $this->view->render('auth/activate',compact('errors'));
    }
}
