<?php
header('Content-Type: application/json');
require_once __DIR__ . '/UsuarioModel.php';

$id_usuario = $_GET['id'] ?? null;

if (!$id_usuario) {
    echo json_encode(["ok" => false, "mensaje" => "ID de usuario requerido."]);
    exit;
}

$model   = new UsuarioModel();
$usuario = $model->consultar($id_usuario);

if ($usuario) {
    echo json_encode(["ok" => true, "usuario" => $usuario]);
} else {
    echo json_encode(["ok" => false, "mensaje" => "Usuario no encontrado."]);
}
