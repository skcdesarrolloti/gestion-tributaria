<?php
declare(strict_types=1);

// Ejecutado por integration.php dentro de su base temporal y sesión autenticada.
verify(request($client, $base, 'contribuyentes', [])[0] === 405, 'Listado rechaza POST');
[$status, $html] = request($client, $base, 'contribuyentes');
verify($status === 200 && str_contains($html, 'Todavía no tienes'), 'Listado vacío y guía disponibles');
[$status, $html] = request($client, $base, 'contribuyentes/formulario');
verify($status === 200 && token($html) !== '' && str_contains($html, 'Guardar contribuyente'), 'Formulario de registro completo disponible');
$taxpayer = ['_csrf_token' => token($html), 'nombre' => '<script>Ejemplo</script>', 'tipo' => 'natural',
    'documento_tipo' => 'CC', 'documento_numero' => '12345678', 'dv' => '', 'fuentes' => ['pension', 'arriendos']];
verify(request($client, $base, 'contribuyentes/formulario', array_replace($taxpayer, ['_csrf_token' => '']))[0] === 419, 'Registro exige CSRF');
verify(request($client, $base, 'contribuyentes/formulario', array_replace($taxpayer, ['tipo' => 'otro']))[0] === 422, 'Registro valida clasificación');
verify(request($client, $base, 'contribuyentes/formulario', array_replace($taxpayer, ['fuentes' => [['pension']]]))[0] === 422, 'Registro rechaza fuentes malformadas');
verify(request($client, $base, 'contribuyentes/formulario', $taxpayer)[0] === 303, 'Registro guarda múltiples fuentes');
$taxpayerId = (int) $db->query('SELECT id FROM contribuyentes')->fetchColumn();
verify(request($client, $base, 'contribuyentes/formulario', $taxpayer)[0] === 422, 'Documento duplicado no crea otro registro');
[$status, $html] = request($client, $base, 'contribuyentes');
verify($status === 200 && str_contains($html, '&lt;script&gt;Ejemplo&lt;/script&gt;') && !str_contains($html, '<script>Ejemplo'), 'Listado escapa nombres');
[$status, $html] = request($client, $base, 'contribuyentes/formulario&id=' . $taxpayerId);
verify($status === 303, 'El enlace antiguo de edición lleva a la ficha única');
verify(json_decode($db->query('SELECT fuentes FROM contribuyentes WHERE id='.(int)$taxpayerId)->fetchColumn(),true)===['pension','arriendos'], 'Abrir el perfil conserva fuentes anteriores aunque ya no se capturen aquí');
$taxpayer['nombre'] = 'Nombre actualizado';
$taxpayer['fuentes'] = ['pension'];
verify(request($client, $base, 'contribuyentes/formulario&id=' . $taxpayerId, $taxpayer)[0] === 303, 'Edición guarda datos');
verify($db->query('SELECT nombre FROM contribuyentes')->fetchColumn() === 'Nombre actualizado', 'Edición persiste en base');
$taxpayer['documento_numero'] = '87654321';
$taxpayer['fuentes'] = [];
verify(request($client, $base, 'contribuyentes/formulario', $taxpayer)[0] === 303, 'Permite registrar sin fuentes conocidas');
verify(request($client, $base, 'contribuyentes/formulario&id=abc')[0] === 404, 'Identificador inválido rechazado');
verify(request($client, $base, 'contribuyentes/formulario&id=99999', $taxpayer)[0] === 404, 'No crea registros mediante edición inexistente');
$insert = $authDb->prepare("INSERT INTO wp_jet_cct_funcionarios (nombre, user_others_apss, pass_others_apss, rol) VALUES (?, ?, ?, ?)");
$insert->execute(['Otro funcionario', 'otro', password_hash('Otra-clave-segura-2026', PASSWORD_DEFAULT), 'administrador']);
$otherOwner = (int) $authDb->lastInsertId();
$insert = $db->prepare('INSERT INTO contribuyentes (funcionario_id, nombre, tipo, documento_tipo, documento_numero, fuentes)
                       VALUES (?, ?, ?, ?, ?, ?)');
$insert->execute([$otherOwner, 'Registro privado ajeno', 'natural', 'CC', '12345678', '[]']);
$otherId = (int) $db->lastInsertId();
verify(str_contains(request($client, $base, 'contribuyentes')[1], 'Registro privado ajeno'), 'Socio consulta registros de todos los contribuyentes');
verify(request($client, $base, 'contribuyentes/formulario&id=' . $otherId)[0] === 303, 'Socio puede abrir la ficha ajena de consulta');
verify(request($client, $base, 'contribuyentes/formulario&id=' . $otherId, $taxpayer)[0] === 403, 'La consulta global no permite modificar registro ajeno');
verify($db->query('SELECT nombre FROM contribuyentes WHERE id = ' . $otherId)->fetchColumn() === 'Registro privado ajeno', 'Registro ajeno permanece intacto');
foreach (['juridica', 'sucesion'] as $offset => $type) {
    $entity = array_replace($taxpayer, ['tipo' => $type, 'documento_tipo' => 'NIT', 'documento_numero' => '90012345' . $offset, 'dv' => '0']);
    if ($type === 'juridica') {
        verify(request($client, $base, 'contribuyentes/formulario', array_replace($entity, ['fuentes' => ['salario']]))[0] === 422,
            'Servidor rechaza fuentes personales para entidad aunque se omita JavaScript');
    }
    verify(request($client, $base, 'contribuyentes/formulario', $entity)[0] === 303, 'Registro admite ' . $type);
}
verify((int) $db->query("SELECT COUNT(*) FROM contribuyentes WHERE dv = '0'")->fetchColumn() === 2, 'Conserva dígito de verificación cero');
verify((int) $db->query('SELECT COUNT(*) FROM wp_jet_cct_funcionarios WHERE _ID = 100')->fetchColumn() === 0,
    'Registro funciona sin copiar el funcionario central a la tabla local');
