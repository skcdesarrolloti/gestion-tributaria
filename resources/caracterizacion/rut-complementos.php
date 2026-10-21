<?php
// Complementos de la hoja 2; claves aditivas en el JSON existente.
return ['entidad'=>[
 'entidades_publicas'=>$f('64 · Entidades de derecho público (código)'), 'fondos'=>$f('65 · Fondos (código)'),
 'cooperativas'=>$f('66 · Cooperativas (código)'), 'sociedades_extranjeras'=>$f('67 · Sociedades / organismos extranjeros (código)'),
 'sin_personeria'=>$f('68 · Sin personería jurídica (código)'), 'otras_organizaciones'=>$f('69 · Otras organizaciones (código)'),
 'constitucion_numero'=>$f('72 · Número de documento de constitución'), 'notaria'=>$f('74 · Número de notaría'),
 'registro_departamento'=>$f('78 · Departamento de registro'),
 'reforma_clase'=>$f('71 · Reforma: clase'), 'reforma_numero'=>$f('72 · Reforma: número'), 'reforma_fecha'=>$f('73 · Reforma: fecha','date'),
 'reforma_notaria'=>$f('74 · Reforma: notaría'), 'reforma_registro'=>$f('75 · Reforma: entidad de registro'),
 'reforma_registro_fecha'=>$f('76 · Reforma: fecha de registro','date'), 'reforma_matricula'=>$f('77 · Reforma: matrícula'),
 'reforma_departamento'=>$f('78 · Reforma: departamento'), 'reforma_municipio'=>$f('79 · Reforma: municipio'),
 'reforma_desde'=>$f('80 · Reforma: vigencia desde','date'), 'reforma_hasta'=>$f('81 · Reforma: vigencia hasta','date')+['max_date'=>'9999-12-31'],
 'capital_nacional_publico'=>$f('83 · Capital nacional público (%)','percent'), 'capital_nacional_privado'=>$f('84 · Capital nacional privado (%)','percent'),
 'capital_extranjero_publico'=>$f('86 · Capital extranjero público (%)','percent'), 'capital_extranjero_privado'=>$f('87 · Capital extranjero privado (%)','percent'),
 'vigilancia'=>$f('88 · Entidad de vigilancia y control'),
 'estados_rut'=>$f('89–92 · Estados, fechas y NIT asociados de las hojas 2 y 7','textarea',[],false,'Transcripción de los estados que figuran en el PDF. No equivale a una consulta del estado actual de la inscripción ante la DIAN.'),
 'grupo'=>$f('94 · Grupo económico / empresarial'), 'matriz_nit'=>$f('95 · NIT de matriz / controlante'), 'matriz_dv'=>$f('96 · DV de matriz / controlante'),
 'matriz_nombre'=>$f('97 · Nombre de matriz / controlante'), 'exterior_id'=>$f('170 · Identificación tributaria exterior'),
 'exterior_pais'=>$f('171 · País'), 'ep_id'=>$f('172 · Identificación de sociedad / natural del exterior con EP'), 'ep_nombre'=>$f('173 · Nombre de sociedad / natural del exterior con EP'),
]];
