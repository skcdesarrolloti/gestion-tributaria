<?php
declare(strict_types=1);
namespace App\Services;

final class CaracterizacionData
{
    public static function read(array $input, array $schema): array
    {
        $data=['campos'=>[],'filas'=>[]];
        foreach ($schema['fields'] as $key=>$f) { $data['campos'][$key]=trim(\input($input['campos']??[],$key)); }
        $rows=$input['filas']??[];
        if (!is_array($rows) || count($rows)>100) { throw new \InvalidArgumentException('Usa como máximo 100 renglones por bloque.'); }
        foreach ($rows as $row) {
            if (!is_array($row)) { throw new \InvalidArgumentException('Revisa los renglones del bloque.'); }
            $clean=[];
            foreach ($schema['rows']??[] as $key=>$f) { $clean[$key]=trim(\input($row,$key)); }
            if (array_filter($clean,static fn($v)=>$v!=='')) { $data['filas'][]=$clean; }
        }
        foreach (['aplicabilidad','justificacion','estado','fuente','fecha','notas'] as $key) { $data[$key]=trim(\input($input,$key)); }
        if (empty($schema['allow_na'])) { $data['aplicabilidad']='si'; }
        return $data;
    }

    public static function errors(array $data, array $schema, string $type): array
    {
        $errors=[];
        foreach ($schema['fields'] as $key=>$f) { self::validate($data['campos'][$key],$f,$errors); }
        foreach ($data['filas'] as $row) {
            foreach ($schema['rows']??[] as $key=>$f) { self::validate($row[$key],$f,$errors); }
        }
        if (!in_array($data['aplicabilidad'],['','si','no'],true) || !in_array($data['estado'],['','borrador','declarado','documentado'],true)) {
            $errors[]='Selecciona un estado y una aplicabilidad válidos.';
        }
        foreach (['justificacion'=>500,'fuente'=>500,'notas'=>2000] as $key=>$max) {
            if (mb_strlen($data[$key])>$max) { $errors[]='El texto de '.$key.' supera '.$max.' caracteres.'; }
        }
        self::validate($data['fecha'],['label'=>'Fecha de revisión','kind'=>'date'],$errors);
        if ($data['estado']==='documentado') {
            $missing=self::missing($data,$schema,$type);
            if ($missing) { $errors[]='Antes de marcar contrastado con documentos completa: '.implode('; ',$missing).'.'; }
            if ($data['fuente']==='' || $data['fecha']==='') { $errors[]='Indica documento de soporte y fecha de revisión.'; }
        }
        return $errors;
    }

    private static function validate(string $value, array $f, array &$errors): void
    {
        if (mb_strlen($value)>($f['kind']==='textarea'?2000:500)) { $errors[]=$f['label'].': texto demasiado largo.'; return; }
        if ($value==='') { return; }
        $valid=match($f['kind']) {
            'select'=>isset($f['options'][$value]),
            'integer'=>preg_match('/^[0-9]{1,15}$/D',$value)===1,
            'decimal'=>preg_match('/^[0-9]{1,15}(\.[0-9]{1,6})?$/D',$value)===1,
            'percent'=>preg_match('/^[0-9]{1,3}(\.[0-9]{1,6})?$/D',$value)===1 && (float)$value<=100,
            'email'=>filter_var($value,FILTER_VALIDATE_EMAIL)!==false,
            'date'=>self::validDate($value), default=>true,
        };
        if (!$valid) { $errors[]='Revisa '.$f['label'].': valor u opción inválidos.'; }
    }

    private static function validDate(string $value): bool
    {
        $date=\DateTimeImmutable::createFromFormat('!Y-m-d',$value);
        return $date!==false && $date->format('Y-m-d')===$value && $value<='2100-12-31' && $value>='1800-01-01';
    }

    public static function missing(array $data, array $schema, string $type): array
    {
        if ($data['aplicabilidad']==='no' && !empty($schema['allow_na'])) {
            if (in_array($type,['juridica','sucesion'],true) && in_array($schema['key'],['entidad','relacionados'],true)) {
                return ['naturaleza / representación requiere revisión para este tipo de contribuyente'];
            }
            return $data['justificacion']===''?['justificación de No aplica']:[];
        }
        $missing=[];
        if ($data['aplicabilidad']==='') { $missing[]='confirmar si aplica'; }
        foreach ($schema['fields'] as $key=>$f) {
            if ($f['required'] && $data['campos'][$key]==='') { $missing[]=$f['label']; }
        }
        foreach ($data['filas'] as $i=>$row) {
            foreach ($schema['rows']??[] as $key=>$f) {
                if ($f['required'] && $row[$key]==='') { $missing[]='renglón '.($i+1).': '.$f['label']; }
            }
        }
        $c=$data['campos'];
        foreach (['secundaria'=>'secundaria_inicio','principal'=>'principal_inicio'] as $key=>$date) {
            if (($c[$key]??'')!=='' && ($c[$date]??'')==='') { $missing[]='fecha de inicio de '.$key; }
        }
        if (isset($c['rut_estado'])) {
            if ($c['rut_estado']!=='disponible') { $missing[]='RUT pendiente de conseguir'; }
            elseif ($c['rut_formulario']==='' || $c['rut_fecha']==='') { $missing[]='número y fecha del RUT consultado'; }
            if ($type==='juridica' && $c['razon_social']==='') { $missing[]='razón social'; }
            if ($type!=='juridica' && ($c['apellido1']==='' || $c['nombre1']==='')) { $missing[]='primer nombre y apellido'; }
        }
        if (($c['fallecido']??'')==='si' && ($c['fallecimiento_fecha']==='' || $c['sucesion_estado']==='')) { $missing[]='fecha de fallecimiento y estado de sucesión'; }
        if (($c['sucesion_estado']??'')==='liquidada' && $c['liquidacion_fecha']==='') { $missing[]='fecha de liquidación de sucesión'; }
        if (isset($schema['rows']) && !$data['filas'] && ($c['sin_responsabilidades']??'')!=='si') {
            if (!isset($c['tiene_activos']) || $c['tiene_activos']==='si' || $c['tiene_deudas']==='si') { $missing[]='detalle de renglones o No aplica justificado'; }
        }
        if (($c['otros_codigos']??'')!=='') { $missing[]='verificar códigos fuera del catálogo'; }
        if (($c['sin_responsabilidades']??'')==='si' && $data['filas']) { $missing[]='corregir: casilla sin códigos y códigos registrados a la vez'; }
        if (($c['exterior_revisado']??'')==='no') { $missing[]='revisar expresamente cuentas y activos en el exterior'; }
        if (($c['otras_revisadas']??'')==='no') { $missing[]='revisar actividades secundarias y otras'; }
        if (($c['fallecido']??'')==='si' && $type==='natural') { $missing[]='revisar registro como sucesión ilíquida, sin reclasificar automáticamente'; }
        if ($schema['key']==='entidad' && $type==='juridica' && ($c['naturaleza']==='' || $c['forma']==='')) { $missing[]='naturaleza y forma de la entidad'; }
        $hasForeign=false; $hasAsset=false; $hasDebt=false;
        foreach ($data['filas'] as $i=>$row) {
            $prefix='renglón '.($i+1).': ';
            if (($row['rol']??'')==='dependiente' && ($row['parentesco']==='' || $row['nacimiento']==='' || $row['reclamado']==='')) { $missing[]=$prefix.'parentesco, nacimiento y revisión de solicitud por otra persona'; }
            if (in_array($row['rol']??'', ['contador','revisor'],true) && $row['tarjeta']==='') { $missing[]=$prefix.'tarjeta profesional'; }
            if (($row['rol']??'')==='socio' && $row['participacion']==='') { $missing[]=$prefix.'participación del socio'; }
            if ($schema['key']==='patrimonio') {
                $debt=($row['tipo']??'')==='deuda'; $hasDebt=$hasDebt||$debt; $hasAsset=$hasAsset||!$debt;
                $foreign=($row['pais']??'')!=='' && !in_array(mb_strtolower(trim($row['pais'])),['colombia','co','col','169'],true);
                $hasForeign=$hasForeign||(!$debt && $foreign);
                if (strtoupper($row['moneda'])!=='COP' && $row['valor_origen']==='') { $missing[]=$prefix.'valor en moneda de origen'; }
            }
        }
        if ($schema['key']==='patrimonio') {
            if (($c['exterior']??'')==='si' && !$hasForeign) { $missing[]='detalle de cuentas o activos del exterior'; }
            if (($c['exterior']??'')==='no' && $hasForeign) { $missing[]='corregir: hay activos extranjeros pero se indicó que no existen'; }
            if (($c['tiene_activos']??'')==='no' && $hasAsset) { $missing[]='corregir declaración sin activos frente al detalle'; }
            if (($c['tiene_deudas']??'')==='si' && !$hasDebt) { $missing[]='detalle de deudas'; }
            if (($c['tiene_deudas']??'')==='no' && $hasDebt) { $missing[]='corregir declaración sin deudas frente al detalle'; }
        }
        return array_values(array_unique($missing));
    }

    public static function status(array $data, array $schema, string $type): string
    {
        if (self::missing($data,$schema,$type)) { return 'Incompleto'; }
        if ($data['estado']==='documentado' && $data['fuente']!=='' && $data['fecha']!=='') { return 'Contrastado por usuario'; }
        return 'Diligenciado · falta contraste';
    }
}
