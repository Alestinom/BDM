<?php
header('Content-Type: application/json');
require_once __DIR__ . '/UsuarioModel.php';

$model    = new UsuarioModel();
$todos    = $model->listar();
$clientes = [];

foreach ($todos as $row) {
    if ($row['tipo_usuario'] === 'Asegurado') {
        $clientes[] = [
            "id_usuario"     => $row['id_usuario'],
            "nombre"         => $row['nombre'],
            "apellidos"      => $row['apellidos'],
            "correo"         => $row['correo'],
            "alias"          => $row['alias'],
            "numero_cliente" => $row['numero_cliente'],
            "telefono"       => $row['telefono'],
        ];
    }
}

echo json_encode(["ok" => true, "clientes" => $clientes]);
