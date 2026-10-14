<?php
declare(strict_types=1);

use App\Services\ContribuyenteData;

$data = ContribuyenteData::read(['nombre' => 'Ejemplo', 'tipo' => 'natural',
    'documento_tipo' => 'CC', 'documento_numero' => '12345678', 'fuentes' => ['pension', 'arriendos']]);
check(ContribuyenteData::errors($data) === [], 'Pensión y arriendos pueden coexistir.');
check(ContribuyenteData::errors(array_replace($data, ['fuentes' => []])) === [], 'Fuentes pendientes permitidas.');
check(ContribuyenteData::errors(array_replace($data, ['fuentes' => [['pension']]])) !== [], 'Rechaza fuentes anidadas.');
check(ContribuyenteData::errors(array_replace($data, ['tipo' => 'invalido'])) !== [], 'Valida tipo de contribuyente.');
check(ContribuyenteData::errors(array_replace($data, ['nombre' => str_repeat('ñ', 151)])) !== [], 'Limita nombres Unicode.');
check(ContribuyenteData::errors(array_replace($data, ['tipo' => 'juridica'])) !== [], 'Entidad requiere NIT.');
check(ContribuyenteData::errors(array_replace($data, ['dv' => '2'])) !== [], 'DV solo para NIT.');
$passport = ContribuyenteData::read(array_replace($data, ['documento_tipo' => 'PAS', 'documento_numero' => 'ab1234']));
check($passport['documento_numero'] === 'AB1234' && ContribuyenteData::errors($passport) === [], 'Normaliza pasaporte.');
check(ContribuyenteData::errors(ContribuyenteData::read(['nombre' => ['ataque']])) !== [], 'Rechaza campos escalares malformados.');
