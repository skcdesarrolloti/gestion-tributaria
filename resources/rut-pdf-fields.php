<?php
// Coordenadas del formulario 001, página carta, origen inferior izquierdo.
// Un campo en blanco no equivale a cero ni a No aplica.
return [
 'identificacion'=>[
  'rut_seccional'=>[201,490,601,611], 'identificacion_numero'=>[330,498,566,572,true],
  'apellido1'=>[25,151,518,525], 'apellido2'=>[151,280,518,525], 'nombre1'=>[280,405,518,525], 'otros_nombres'=>[405,596,518,525],
  'razon_social'=>[25,596,494,501], 'nombre_comercial'=>[25,343,470,477], 'sigla'=>[343,596,470,477],
  'expedicion_pais'=>[25,217,542,549], 'expedicion_departamento'=>[217,405,542,549], 'expedicion_municipio'=>[405,596,542,549],
 ],
 'ubicacion'=>[
  'pais'=>[25,200,434,441], 'departamento'=>[200,392,434,441], 'municipio'=>[392,596,434,441],
  'direccion'=>[25,596,409,416], 'correo'=>[25,596,398,405], 'postal'=>[25,200,384,391,true],
  'telefono1'=>[200,400,384,391,true], 'telefono2'=>[400,596,384,391,true],
 ],
 'actividades'=>[
  'principal'=>[25,75,326,333,true], 'principal_inicio'=>[75,162,326,333,true],
  'secundaria'=>[162,210,326,333,true], 'secundaria_inicio'=>[210,295,326,333,true],
  'otra1'=>[299,337,326,333,true], 'otra2'=>[337,387,326,333,true],
  'ocupacion'=>[430,510,326,333,true], 'establecimientos'=>[510,596,326,333,true],
 ],
];
