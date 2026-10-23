<?php
declare(strict_types=1);
$q=$db->prepare("INSERT INTO contribuyentes (funcionario_id,tipo,documento_tipo,documento_numero,nombre,fuentes) SELECT funcionario_id,'natural','CC','101019999','Preparación anual ficticia','[]' FROM contribuyentes WHERE id=?");$q->execute([$taxpayerId]);
$preparationTaxpayer=(int)$db->lastInsertId();
$preparationRoute='registro&contribuyente='.$preparationTaxpayer.'&anio=2026';
$characterization=new App\Models\Caracterizacion(new App\Core\Database($dbConfig));
$owner=(int)$db->query('SELECT funcionario_id FROM contribuyentes WHERE id='.$preparationTaxpayer)->fetchColumn();
$saved=$characterization->blocksFor($preparationTaxpayer,$owner,2026);
$responsibility=$saved['responsabilidades']['datos']??['campos'=>[]];$responsibility['filas']=[['codigo'=>'05']];
verify($characterization->saveFor($preparationTaxpayer,$owner,2026,'responsabilidades',$responsibility,(int)($saved['responsabilidades']['revision']??0)),'Prepara responsabilidad sintética del RUT');
$fiscal=$saved['fiscal']['datos']??['campos'=>[],'filas'=>[]];$fiscal['campos']['residencia']='residente';$fiscal['campos']['regimen']='';
verify($characterization->saveFor($preparationTaxpayer,$owner,2026,'fiscal',$fiscal,(int)($saved['fiscal']['revision']??0)),'Prepara residencia anual sintética');
[$s,$preparationHtml]=request($client,$base,$preparationRoute);
verify($s===200 && str_contains($preparationHtml,'value="ordinario" selected') && str_contains($preparationHtml,'value="residente" selected') && str_contains($preparationHtml,'RUT · casilla 53 del perfil 2026'),'Registro precarga régimen del RUT y residencia del mismo año');
verify(!preg_match('/<a\b[^>]*href="https?:/i',$preparationHtml) && str_contains($preparationHtml,'data-norma-url='),'Las ayudas de preparación consultan normas dentro del aplicativo');
verify(!(bool)$db->query('SELECT 1 FROM perfiles_renta WHERE contribuyente_id='.$preparationTaxpayer.' AND anio=2026')->fetchColumn(),'La propuesta no crea perfil por GET ni modifica ingresos');
verify(request($client,$base,'beneficios/articulo&articulo=et-10')[0]===200 && request($client,$base,'beneficios/articulo&articulo=et-595')[0]===200,'Residencia y año tienen artículos internos disponibles');
