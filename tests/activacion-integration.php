<?php
declare(strict_types=1);
// Verifica la continuidad de sesión sin abrir la conexión antigua.
$transition=new App\Services\ActivacionCuenta(new App\Core\Database($dbConfig),$temp.'/activacion-inicial.json',$config);
$_SESSION=['user_id'=>100,'last_activity'=>time(),'auth_source'=>hash('sha256',json_encode([$authConfig['host'],$authConfig['port'],$authConfig['name']]))];
verify($transition->legacySession('admin'),'Reconoce sesión anterior vigente del Socio autorizado sin consultar funcionarios');
verify(!$transition->legacySession('otro'),'La sesión previa no permite reclamar un login diferente');
$_SESSION['last_activity']=time()-1801;
verify(!$transition->legacySession('admin'),'Una sesión anterior vencida exige prueba privada');
$_SESSION=[];
