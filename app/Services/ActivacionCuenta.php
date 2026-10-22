<?php
declare(strict_types=1);
namespace App\Services;
use App\Core\Database;

final class ActivacionCuenta
{
    public function __construct(private Database $database,private string $file,private array $config) {}
    public function prepare(): void
    {
        if (is_file($this->file)) { return; }
        $handle=@fopen($this->file,'x');
        if (!$handle) { if (is_file($this->file)) { return; }throw new \RuntimeException('No se pudo preparar la activación privada.'); }
        try { chmod($this->file,0600);fwrite($handle,json_encode(['clave'=>bin2hex(random_bytes(32))],JSON_THROW_ON_ERROR)); }
        finally { fclose($handle); }
    }
    public function legacySession(string $login): bool
    {
        // Solo conserva la sesión del Socio ya autenticado. No abre la conexión antigua.
        $central=$this->config['auth_database']??[];
        $source=hash('sha256',json_encode([$central['host']??'', $central['port']??'', $central['name']??'']));
        if (empty($_SESSION['user_id']) || ($_SESSION['auth_source']??'')!==$source || time()-(int)($_SESSION['last_activity']??0)>1800) { return false; }
        $q=$this->database->connection()->prepare("SELECT 1 FROM accesos_tributarios WHERE funcionario_id=? AND login_hash=? AND perfil='socio' AND activo=1");
        $q->execute([(int)$_SESSION['user_id'],hash('sha256',strtolower(trim($login)))]);return (bool)$q->fetchColumn();
    }
    public function activate(array $data): void
    {
        $login=trim(\input($data,'username'));$secret=\input($data,'activation_key');
        $stored=json_decode((string)file_get_contents($this->file),true);
        $delivery=preg_match('/^[a-f0-9]{64}$/D',$secret) && hash_equals($this->config['activation_token_sha256']??'',hash('sha256',$secret));
        if (!$this->legacySession($login) && !$delivery && (!preg_match('/^[a-f0-9]{64}$/D',$secret) || !hash_equals($stored['clave']??'', $secret))) {
            throw new \InvalidArgumentException('Se necesita la sesión previa del Socio autorizado o la clave privada de activación.');
        }
        if (\input($data,'password')!==\input($data,'confirmation')) { throw new \InvalidArgumentException('Las contraseñas no coinciden.'); }
        (new CuentaRegistro($this->database))->create(null,$login,\input($data,'nombre'),\input($data,'correo'),\input($data,'password'),'socio',null,true);
        // El archivo ya no concede acceso: la activación comprueba que no exista ninguna cuenta.
        if (is_file($this->file)) { unlink($this->file); }
    }
}
