<?php
declare(strict_types=1);

namespace App\Services;

use App\Models\Usuario;

final class AuthService
{
    // Hash de relleno para ejecutar password_verify incluso si el usuario no existe.
    private const DUMMY_HASH = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2uheWG/igi.';

    public function __construct(
        private Usuario $users,
        private string $sourceId,
        private ?\App\Models\AccesoTributario $access=null,
    ) {}

    public function attempt(string $login, string $password): bool
    {
        if ($login === '' || strlen($login) > 100 || $password === '' || strlen($password) > 72 || str_contains($password,"\0")) {
            return false;
        }
        $user = $this->users->findActiveByLogin($login);
        $valid = password_verify($password, $user['clave_hash'] ?? self::DUMMY_HASH);
        if (!$user || !$valid) {
            return false;
        }
        session_regenerate_id(true);
        $_SESSION = ['user_id' => (int) $user['_ID'], 'last_activity' => time(), 'auth_source' => $this->sourceId,'clave_version'=>(int)$user['clave_version']];
        return true;
    }

    public function user(): ?array
    {
        if (empty($_SESSION['user_id'])) {
            return null;
        }
        // Una sesión de otro origen nunca autentica una cuenta propia con el mismo ID.
        if (($_SESSION['auth_source'] ?? '') !== $this->sourceId) {
            $this->forget();
            return null;
        }
        if (time() - (int) ($_SESSION['last_activity'] ?? 0) > 1800) {
            $this->forget();
            return null;
        }
        $user = $this->users->findActiveById((int) $_SESSION['user_id']);
        if (!$user || (int)($user['clave_version']??0)!==(int)($_SESSION['clave_version']??0)) {
            $this->forget();
            return null;
        }
        $_SESSION['last_activity'] = time();
        unset($user['clave_hash']);
        $user['_acceso']=$this->access?->forUser($user)??[];
        return $user;
    }

    public function logout(): void
    {
        $_SESSION = [];
        $params = session_get_cookie_params();
        setcookie(session_name(), '', [
            'expires' => time() - 3600, 'path' => $params['path'],
            'secure' => $params['secure'], 'httponly' => true, 'samesite' => 'Lax',
        ]);
        session_destroy();
    }

    private function forget(): void
    {
        $_SESSION = [];
        session_regenerate_id(true);
    }
}
