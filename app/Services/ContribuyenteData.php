<?php
declare(strict_types=1);

namespace App\Services;

final class ContribuyenteData
{
    public const TIPOS = [
        'natural' => 'Persona natural',
        'juridica' => 'Persona jurídica',
        'sucesion' => 'Sucesión ilíquida',
    ];
    public const DOCUMENTOS = [
        'CC' => 'Cédula de ciudadanía', 'CE' => 'Cédula de extranjería',
        'NIT' => 'NIT (sin dígito de verificación)', 'PAS' => 'Pasaporte',
    ];
    public const FUENTES = [
        'salario' => 'Salarios y otros ingresos laborales',
        'honorarios' => 'Honorarios y servicios independientes',
        'pension' => 'Pensiones', 'arriendos' => 'Arrendamientos',
        'rendimientos' => 'Intereses y rendimientos financieros',
        'negocio' => 'Comercio u otras actividades de negocio',
        'dividendos' => 'Dividendos y participaciones', 'otros' => 'Otros ingresos',
    ];

    public static function sourcesFor(string $type): array
    {
        if (!isset(self::TIPOS[$type])) { return []; }
        if ($type !== 'juridica') { return self::FUENTES; }
        return [
            'negocio' => 'Ventas y otras actividades ordinarias',
            'honorarios' => 'Prestación de servicios', 'arriendos' => 'Arrendamientos',
            'rendimientos' => 'Ingresos financieros',
            'dividendos' => 'Dividendos y participaciones', 'otros' => 'Otros ingresos',
        ];
    }

    public static function read(array $input): array
    {
        $data = [];
        foreach (['nombre', 'tipo', 'documento_tipo', 'documento_numero', 'dv'] as $field) {
            $data[$field] = trim(\input($input, $field));
        }
        $data['documento_numero'] = strtoupper($data['documento_numero']);
        $data['fuentes'] = is_array($input['fuentes'] ?? null) ? $input['fuentes'] : [];
        return $data;
    }

    public static function errors(array $data): array
    {
        $errors = [];
        if ($data['nombre'] === '' || mb_strlen($data['nombre']) > 150) {
            $errors[] = 'Escribe un nombre o razón social de máximo 150 caracteres.';
        }
        if (!isset(self::TIPOS[$data['tipo']])) { $errors[] = 'Selecciona el tipo de contribuyente.'; }
        if (!isset(self::DOCUMENTOS[$data['documento_tipo']])) { $errors[] = 'Selecciona el tipo de documento.'; }
        $pattern = $data['documento_tipo'] === 'PAS' ? '/^[A-Z0-9]{3,30}$/D' : '/^[0-9]{3,20}$/D';
        if (!preg_match($pattern, $data['documento_numero'])) {
            $errors[] = 'Escribe el documento sin puntos, espacios ni guiones; el pasaporte admite letras y números.';
        }
        if (in_array($data['tipo'], ['juridica', 'sucesion'], true) && $data['documento_tipo'] !== 'NIT') {
            $errors[] = 'Para una persona jurídica o sucesión ilíquida registra el NIT del RUT.';
        }
        if ($data['dv'] !== '' && ($data['documento_tipo'] !== 'NIT' || !preg_match('/^[0-9]$/D', $data['dv']))) {
            $errors[] = 'El dígito de verificación es un solo número y corresponde únicamente al NIT.';
        }
        foreach ($data['fuentes'] as $source) {
            if (!is_string($source) || !isset(self::sourcesFor($data['tipo'])[$source])) {
                $errors[] = 'Selecciona fuentes de ingresos válidas para el tipo de contribuyente.';
                break;
            }
        }
        if ($data['tipo'] === 'juridica' && (in_array('salario', $data['fuentes'], true) || in_array('pension', $data['fuentes'], true))) {
            $errors[] = 'Salarios y pensiones son fuentes personales; revisa las fuentes de la entidad.';
        }
        return $errors;
    }
}
