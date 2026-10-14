<?php
declare(strict_types=1);

use App\Services\{Exogena1007Fields,Exogena1007Data};

$blank1007=Exogena1007Data::read(['anio'=>Exogena1007Fields::DEFAULT_YEAR]);
check(count(Exogena1007Fields::all())===11 && count(array_filter(array_diff_key($blank1007,['anio'=>true])))===0,
    '1007 conserva once campos vacíos, incluidos país e importes.');
$income1007=Exogena1007Data::read(['anio'=>'2025','concepto'=>'4001','documento_tipo'=>'31',
    'identificacion'=>'001234567','razon_social'=>'Entidad ficticia','pais'=>'169','ingresos_brutos'=>'1000000','devoluciones'=>'0']);
check(Exogena1007Data::errors($income1007)===[], '1007 válido con identidad e importes explícitos.');
check($income1007['identificacion']==='001234567', '1007 conserva ceros iniciales del documento.');
check(Exogena1007Data::errors(array_replace($income1007,['devoluciones'=>'']))!==[], 'Devolución desconocida no es cero.');
check(Exogena1007Data::errors(array_replace($income1007,['ingresos_brutos'=>'1e6']))!==[], 'No admite notación científica en ingreso.');
check(Exogena1007Data::errors(array_replace($income1007,['ingresos_brutos'=>'-1']))!==[], 'Importes no negativos.');
check(Exogena1007Data::errors(array_replace($income1007,['ingresos_brutos'=>'999999999999999999']))===[], 'Conserva hasta 18 dígitos sin convertir a float.');
check(Exogena1007Data::errors(array_replace($income1007,['pais'=>'']))!==[], 'País no se presume.');
check(Exogena1007Data::errors(array_replace($income1007,['razon_social'=>'']))!==[], 'Exige datos del tercero informado.');
