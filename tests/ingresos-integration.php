<?php
declare(strict_types=1);

// Solo datos sintéticos y bases temporales de integration.php.
$taxpayerId=(int)$db->query("SELECT id FROM contribuyentes WHERE nombre='Nombre actualizado'")->fetchColumn();
$route='ingresos&contribuyente='.$taxpayerId;
[$status,$html]=request($client,$base,$route);
verify($status===200 && token($html)!=='', 'Ingresos abre perfil anual propio');
$annual=['_csrf_token'=>token($html),'anio'=>'2025','residencia'=>'residente','regimen'=>'ordinario',
    'servicios'=>'costos','soporte_perfil'=>'RUT y análisis fiscal ficticios'];
verify(request($client,$base,$route,array_replace($annual,['_csrf_token'=>'']))[0]===419, 'Perfil anual exige CSRF');
verify(request($client,$base,$route,array_replace($annual,['regimen'=>'simple']))[0]===422, 'No aplica reglas ordinarias a SIMPLE');
[$status,$helpHtml]=request($client,$base,$route,array_replace($annual,['regimen'=>'simple']));
verify(str_contains($helpHtml,'Impuesto unificado sujeto a requisitos') && (int)$db->query('SELECT COUNT(*) FROM perfiles_renta')->fetchColumn()===0,
    'Sin JavaScript conserva la explicación y no crea un perfil con otro régimen');
verify(request($client,$base,$route,$annual)[0]===303, 'Perfil residente crea formulario 210');
$profileId=(int)$db->query('SELECT id FROM perfiles_renta')->fetchColumn();
[$status,$savedHtml]=request($client,$base,$route);
verify($status===200 && str_contains($savedHtml,'Evidencia guardada:') && str_contains($savedHtml,'RUT y análisis fiscal ficticios'),
    'Al volver muestra las selecciones y evidencia guardadas');
$profileRoute='ingresos/perfil&id='.$profileId;
[$status,$editHtml]=request($client,$base,$profileRoute);
verify($status===200 && str_contains($editHtml,'value="2025" selected') && str_contains($editHtml,'RUT y análisis fiscal ficticios'),
    'Edición carga datos persistidos en lugar de vaciar campos');
verify(request($client,$base,$profileRoute,array_replace($annual,['_csrf_token'=>'']))[0]===419, 'Edición del perfil exige CSRF');
verify(request($client,$base,$profileRoute,array_replace($annual,['anio'=>'2026']))[0]===303, 'Permite corregir año sin movimientos');
verify(request($client,$base,$profileRoute,$annual)[0]===303, 'Puede restaurar el año antes de capturar');
verify(request($client,$base,$route,array_replace($annual,['anio'=>'2026']))[0]===303, 'Otro año se registra por separado');
verify(request($client,$base,$profileRoute,array_replace($annual,['anio'=>'2026']))[0]===422, 'No sobrescribe otro perfil al corregir año');
verify((int)$db->query('SELECT anio FROM perfiles_renta WHERE id='.$profileId)->fetchColumn()===2025, 'Conflicto de año no pierde el perfil');
verify(request($client,$base,$route,$annual)[0]===422, 'Evita duplicar año del contribuyente');
[$status,$html]=request($client,$base,'ingresos/detalle&id='.$profileId);
verify($status===200 && str_contains($html,'Casillas y fichas') && str_contains($html,'ET 335 numeral 2'), 'Casillas muestran respaldo normativo');
$codeId=static function(string $form,int $code) use($db): string {
    $q=$db->prepare('SELECT id FROM conceptos_ingresos WHERE formulario=? AND casilla=?');$q->execute([$form,$code]);return (string)$q->fetchColumn();
};
$movement=['_csrf_token'=>token($html),'concepto_id'=>$codeId('210',58),'fecha'=>'2025-09-30','estado'=>'real',
    'descripcion'=>'<b>Arriendo ficticio</b>','tercero'=>'Tercero ficticio','pais'=>'Colombia','moneda'=>'COP',
    'valor_original'=>'2000000','conversion'=>'','valor_cop'=>'2000000','soporte'=>'Contrato ficticio','justificacion'=>''];
$movementRoute='ingresos/movimiento&id='.$profileId;
verify(request($client,$base,$movementRoute,array_replace($movement,['_csrf_token'=>'']))[0]===419, 'Movimiento exige CSRF');
verify(request($client,$base,$movementRoute,array_replace($movement,['concepto_id'=>$codeId('110',48)]))[0]===422, 'No mezcla códigos de formularios');
verify(request($client,$base,$movementRoute,array_replace($movement,['concepto_id'=>$codeId('210',61)]))[0]===422, 'Totales no se guardan como movimientos');
verify(request($client,$base,$movementRoute,array_replace($movement,['fecha'=>'2026-01-01']))[0]===422, 'Rechaza movimientos de otro año');
verify(request($client,$base,$movementRoute,$movement)[0]===303, 'Guarda movimiento fiscal con soporte');
$movementId=(int)$db->query('SELECT id FROM movimientos_ingresos')->fetchColumn();
[$status,$html]=request($client,$base,'ingresos/detalle&id='.$profileId);
verify(str_contains($html,'&lt;b&gt;Arriendo ficticio&lt;/b&gt;') && str_contains($html,'2.000.000'), 'Detalle escapa descripción y muestra total');
$movement['descripcion']='Movimiento corregido';
$movement['valor_original']=$movement['valor_cop']='2500000';
verify(request($client,$base,$movementRoute.'&movimiento='.$movementId,$movement)[0]===303, 'Permite corregir movimientos propios');
verify((int)$db->query('SELECT valor_cop FROM movimientos_ingresos')->fetchColumn()===2500000, 'Corrección persiste sin duplicar movimiento');
$movement['concepto_id']=$codeId('210',60);
verify(request($client,$base,$movementRoute,$movement)[0]===422, 'Costos requieren justificación particular');
$movement['justificacion']='ET 107: relación con actividad; factura ficticia';
$movement['valor_original']=$movement['valor_cop']='300000';
verify(request($client,$base,$movementRoute,$movement)[0]===303, 'Registra reducción documentada para revisión');
[$status,$html]=request($client,$base,'ingresos/detalle&id='.$profileId);
verify(str_contains($html,'2.200.000'), 'Primera depuración calcula capital menos costo');
verify(request($client,$base,$profileRoute,array_replace($annual,['anio'=>'2026']))[0]===422, 'Con movimientos protege año y clasificación');
$updatedProfile=array_replace($annual,['soporte_perfil'=>'Evidencia <b>actualizada</b>']);
verify(request($client,$base,$profileRoute,$updatedProfile)[0]===303, 'Con movimientos permite actualizar evidencia');
verify(str_contains(request($client,$base,$route)[1],'Evidencia &lt;b&gt;actualizada&lt;/b&gt;'), 'Resumen escapa la evidencia y conserva cambios');
verify((int)$db->query('SELECT COUNT(*) FROM movimientos_ingresos WHERE perfil_id='.$profileId)->fetchColumn()===2,
    'Editar perfil no borra ni duplica movimientos');
verify(request($client,$base,'ingresos&contribuyente='.$otherId)[0]===404, 'No crea perfiles para contribuyentes ajenos');
$otherProfile=$db->prepare("INSERT INTO perfiles_renta (contribuyente_id,anio,tipo,residencia,regimen,servicios,soporte_perfil,formulario,version)
    VALUES (?,2025,'natural','residente','ordinario','pendiente','Prueba','210','2026-10')");
$otherProfile->execute([$otherId]);$otherProfileId=(int)$db->lastInsertId();
verify(request($client,$base,'ingresos/detalle&id='.$otherProfileId)[0]===404, 'Perfil ajeno permanece privado');
verify(request($client,$base,'ingresos/perfil&id='.$otherProfileId,$annual)[0]===404, 'No puede editar perfil ajeno');
verify(request($client,$base,'ingresos/movimiento&id='.$otherProfileId,$movement)[0]===404, 'No escribe movimientos en perfil ajeno');
verify(request($client,$base,$movementRoute.'&movimiento=999999',$movement)[0]===404, 'No crea mediante una edición inexistente');
foreach ([['juridica','residente','110'],['sucesion','no_residente','110']] as [$type,$residence,$form]) {
    $entityId=(int)$db->query("SELECT id FROM contribuyentes WHERE tipo='$type' AND funcionario_id=100")->fetchColumn();
    verify(request($client,$base,'ingresos&contribuyente='.$entityId,array_replace($annual,['residencia'=>$residence,'servicios'=>'no_aplica']))[0]===303, 'Perfil '.$type.' determina formulario '.$form);
}
