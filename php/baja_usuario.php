<?php
header('Content-Type: application/json');
require_once __DIR__ . '/UsuarioModel.php';

$id_usuario = $_POST['id_usuario'] ?? null;

if (!$id_usuario) {
    echo json_encode(["ok" => false, "mensaje" => "ID de usuario requerido."]);
    exit;
}

$model = new UsuarioModel();
$row   = $model->baja($id_usuario);

if ($row) {
    echo json_encode($row['resultado'] === 'OK'
        ? ["ok" => true,  "mensaje" => $row['mensaje']]
        : ["ok" => false, "mensaje" => $row['mensaje']]
    );
} else {
    echo json_encode(["ok" => false, "mensaje" => "Error al dar de baja el usuario."]);
}
