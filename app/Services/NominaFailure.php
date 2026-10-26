<?php
declare(strict_types=1);
namespace App\Services;

/** Mensajes cerrados: nunca publica el mensaje SQL, rutas, filas ni credenciales. */
final class NominaFailure
{
    public static function code(\Throwable $error): string
    {
        if ($error instanceof \PDOException) {
            $number=(int)($error->errorInfo[1]??0);
            return 'NOM-DB-'.($number>0 && $number<100000?$number:0);
        }
        return match (true) {
            $error instanceof \TypeError=>'NOM-TIPO',
            $error instanceof \JsonException=>'NOM-JSON',
            $error instanceof \Error=>'NOM-EJECUCION',
            default=>'NOM-INTERNO',
        };
    }
    public static function message(\Throwable $error): string
    {
        return 'No se pudo guardar la nómina. La importación se revirtió; revisa los acumulados antes de reintentar. '
            .'La selección se conserva mientras siga vigente. Código de incidencia: '.self::code($error).'.';
    }
}
