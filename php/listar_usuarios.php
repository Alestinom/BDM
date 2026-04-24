<?php
header('Content-Type: application/json');
require_once __DIR__ . '/UsuarioModel.php';

$model    = new UsuarioModel();
$todos    = $model->listar();
$usuarios = [];

foreach ($todos as $row) {
    if ($row['tipo_usuario'] === 'Supervisor' || $row['tipo_usuario'] === 'Ajustador') {
        $usuarios[] = [
            "id_usuario"   => $row['id_usuario'],
            "nombre"       => $row['nombre'],
            "apellidos"    => $row['apellidos'],
            "correo"       => $row['correo'],
            "alias"        => $row['alias'],
            "tipo_usuario" => $row['tipo_usuario'],
        ];
    }
}

echo json_encode(["ok" => true, "usuarios" => $usuarios]);
