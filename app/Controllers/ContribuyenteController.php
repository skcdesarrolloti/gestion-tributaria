<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\View;
use App\Models\Contribuyente;
use App\Services\{AuthService, ContribuyenteData, Csrf};
use PDOException;

final class ContribuyenteController
{
    public function __construct(
        private AuthService $auth, private Contribuyente $model, private View $view, private array $config,
    ) {}

    public function index(): void
    {
        $user = $this->requireUser();
        $rows = $this->model->listFor((int) $user['_ID']);
        $notice = $_SESSION['contribuyente_notice'] ?? '';
        unset($_SESSION['contribuyente_notice']);
        $this->view->render('contribuyentes/index', compact('user', 'rows', 'notice'));
    }

    public function form(): void
    {
        $user = $this->requireUser();
        $owner = (int) $user['_ID'];
        $id = null;
        $record = ContribuyenteData::read([]);
        if (array_key_exists('id', $_GET)) {
            $raw = \input($_GET, 'id');
            if (!preg_match('/^[1-9][0-9]{0,9}$/D', $raw)) { $this->notFound(); return; }
            $id = (int) $raw;
            $record = $this->model->findFor($id, $owner);
            if (!$record) { $this->notFound(); return; }
        }
        $errors = [];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!Csrf::valid(\input($_POST, '_csrf_token'))) {
                http_response_code(419);
                $errors[] = 'El formulario venció. Recarga la página antes de guardar.';
            } else {
                $record = ContribuyenteData::read($_POST);
                $errors = ContribuyenteData::errors($record);
                if (!$errors) {
                    try {
                        $this->model->saveFor($record, $owner, $id);
                        $_SESSION['contribuyente_notice'] = $id === null ? 'Contribuyente registrado.' : 'Contribuyente actualizado.';
                        \redirect($this->config, 'contribuyentes');
                    } catch (PDOException $error) {
                        if ((int) ($error->errorInfo[1] ?? 0) !== 1062) { throw $error; }
                        $errors[] = 'Ya registraste un contribuyente con ese tipo y número de documento.';
                    }
                }
                http_response_code(422);
            }
        }
        $this->view->render('contribuyentes/form', compact('user', 'id', 'record', 'errors'));
    }

    private function requireUser(): array
    {
        $user = $this->auth->user();
        if (!$user) { \redirect($this->config, 'login'); }
        // Política inicial: todo funcionario activo gestiona exclusivamente sus propios registros.
        return $user;
    }

    private function notFound(): void
    {
        http_response_code(404);
        echo 'Contribuyente no encontrado.';
    }
}
