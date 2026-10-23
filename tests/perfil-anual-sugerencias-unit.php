<?php
declare(strict_types=1);
use App\Services\PerfilAnualSugerencias;
$natural=['tipo'=>'natural'];
check(PerfilAnualSugerencias::build($natural,'2025',[],[],'2026')['notes']===[], 'No anuncia un RUT de otro año cuando no hay datos documentales.');
$rut=['identificacion'=>['datos'=>['campos'=>['rut_formulario'=>'RUT FICTICIO','rut_fecha'=>'2026-01-10']]],
    'actividades'=>['datos'=>['campos'=>['principal'=>'0020']]],'responsabilidades'=>['datos'=>['filas'=>[['codigo'=>'5']]]]];
$suggestion=PerfilAnualSugerencias::build($natural,'2025',[],$rut,'2026');
check($suggestion['record']['regimen']==='ordinario' && $suggestion['record']['residencia']==='' && $suggestion['record']['servicios']==='', 'RUT propone régimen sin inventar residencia ni opción de servicios por ser pensionado.');
check(str_contains($suggestion['record']['soporte_perfil'],'RUT FICTICIO') && str_contains(implode(' ',$suggestion['notes']),'2026'), 'Precarga evidencia y avisa cuando el RUT es de otro perfil anual.');
$fiscal=['fiscal'=>['datos'=>['campos'=>['residencia'=>'no_residente','regimen'=>'ordinario'],'fuente'=>'Soporte fiscal ficticio']]];
$suggestion=PerfilAnualSugerencias::build($natural,'2025',$fiscal,$rut,'2026');
check($suggestion['record']['residencia']==='no_residente' && str_contains($suggestion['origins']['residencia'],'2025'), 'Recupera residencia del año solicitado con su origen.');
$old=array_replace($rut,$fiscal);
check(PerfilAnualSugerencias::build($natural,'2025',[],$old,'2026')['record']['residencia']==='', 'No traslada residencia de un año diferente.');
$simple=$rut;$simple['responsabilidades']['datos']['filas']=[['codigo'=>'47']];
check(PerfilAnualSugerencias::build($natural,'2025',$simple)['record']['regimen']==='simple', 'Reconoce SIMPLE y no propone ordinario en su lugar.');
$conflict=$rut;$conflict['responsabilidades']['datos']['filas'][]=['codigo'=>'47'];
check(PerfilAnualSugerencias::build($natural,'2025',$conflict)['record']['regimen']==='', 'Responsabilidades contradictorias exigen revisión sin escoger régimen.');
$special=$rut;$special['responsabilidades']['datos']['filas']=[['codigo'=>'04']];
check(PerfilAnualSugerencias::build(['tipo'=>'juridica'],'2025',$special)['record']['regimen']==='', 'Código 04 no se convierte automáticamente en modalidad ESAL.');
check(PerfilAnualSugerencias::build(['tipo'=>'juridica'],'2025',$rut)['record']['servicios']==='no_aplica', 'Entidad no recibe elección personal de exención.');
$noFees=['ingresos'=>['datos'=>['campos'=>['honorarios'=>'no']]]];
check(PerfilAnualSugerencias::build($natural,'2025',$noFees)['record']['servicios']==='no_aplica', 'Reutiliza respuesta explícita del año sobre ausencia de servicios.');
foreach (['natural','juridica','sucesion'] as $type) {
    foreach (App\Services\PerfilGuidance::fields($type) as $definition) {
        foreach ($definition['options'] as $option) {
            foreach (App\Services\NormaCatalog::references(['id'=>'','norm'=>$option['norma']]) as $ref) { check(App\Services\NormaCatalog::read($ref['id'])!==null,'Las ayudas de preparación tienen texto normativo interno.'); }
        }
    }
}
