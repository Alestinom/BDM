<?php
header('Content-Type: application/json');
require_once __DIR__ . '/UsuarioModel.php';

$correo   = $_POST['correo']   ?? '';
$password = $_POST['password'] ?? '';

error_log("LOGIN INTENT - correo: " . $correo . " | password: " . $password);

$model   = new UsuarioModel();
$usuario = $model->login($correo, $password);

if ($usuario) {
    $nombreCompleto = trim($usuario['nombre'] . ' ' . $usuario['apellidos']);
    $iniciales = strtoupper(
        substr($usuario['nombre'], 0, 1) . substr($usuario['apellidos'], 0, 1)
    );
    echo json_encode([
        "ok"        => true,
        "tipo"      => $usuario['tipo_usuario'],
        "nombre"    => $nombreCompleto,
        "iniciales" => $iniciales,
        "correo"    => $usuario['correo'],
        "id_usuario" => $usuario['id_usuario'],
    ]);
} else {
    echo json_encode(["ok" => false]);
}
