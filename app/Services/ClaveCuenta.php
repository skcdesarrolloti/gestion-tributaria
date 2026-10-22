<?php
declare(strict_types=1);
namespace App\Services;

final class ClaveCuenta
{
    public static function validate(string $password): void
    {
        if (mb_strlen($password)<12 || strlen($password)>72 || str_contains($password,"\0")) {
            throw new \InvalidArgumentException('Usa una contraseña de al menos 12 caracteres y máximo 72 bytes. Puedes utilizar una frase larga.');
        }
    }
    public static function identity(string $login,string $name,string $email): array
    {
        $login=strtolower(trim($login));$name=trim($name);$email=trim($email);
        if (!preg_match('/^[a-z0-9][a-z0-9._-]{2,99}$/D',$login) || mb_strlen($name)<2 || mb_strlen($name)>160
            || ($email!=='' && (strlen($email)>190 || !filter_var($email,FILTER_VALIDATE_EMAIL)))) {
            throw new \InvalidArgumentException('Indica nombre, usuario de 3 a 100 caracteres (letras, números, punto, guion) y un correo válido si lo incluyes.');
        }
        return [$login,$name,$email?:null];
    }
}
