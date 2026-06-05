<?php
header('Content-Type: application/json');
require_once __DIR__ . '/UsuarioModel.php';

$model = new UsuarioModel();
$todos = $model->listar();

$ajustadores = [];
foreach ($todos as $row) {
    if ($row['tipo_usuario'] === 'Ajustador' && ($row['activo'] ?? 1) == 1) {
        $ajustadores[] = [
            'id_usuario' => $row['id_usuario'],
            'nombre'     => $row['nombre'],
            'apellidos'  => $row['apellidos'],
        ];
    }
}

usort($ajustadores, fn($a, $b) => ($a['nombre'] . $a['apellidos']) <=> ($b['nombre'] . $b['apellidos']));

echo json_encode(["ok" => true, "ajustadores" => $ajustadores]);
