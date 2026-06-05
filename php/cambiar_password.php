<?php
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/debug.log');
error_reporting(E_ALL);

header('Content-Type: application/json');
require_once __DIR__ . '/UsuarioModel.php';
require_once __DIR__ . '/Conexion.php';

error_log("=== INICIO CAMBIAR PASSWORD ===");
error_log("POST: " . json_encode($_POST));

$id_usuario  = $_POST['id_usuario']      ?? null;
$pass_actual = $_POST['password_actual'] ?? '';
$pass_nueva  = trim($_POST['password_nueva'] ?? '');

if (!$id_usuario || !$pass_actual || !$pass_nueva) {
    echo json_encode(["ok" => false, "mensaje" => "Completa todos los campos."]);
    exit;
}
if (strlen($pass_nueva) < 8 || !preg_match('/[A-Z]/', $pass_nueva) || !preg_match('/[0-9]/', $pass_nueva)) {
    echo json_encode(["ok" => false, "mensaje" => "La nueva contraseña no cumple los requisitos de seguridad."]);
    exit;
}

// Verificar contraseña actual
$conn = Conexion::getInstance()->getConexion();
$stmt = $conn->prepare("SELECT id_usuario FROM Usuarios WHERE id_usuario = ? AND contrasena = ?");
if (!$stmt) {
    error_log("Error prepare verificacion: " . $conn->error);
    echo json_encode(["ok" => false, "mensaje" => "Error al verificar la contraseña."]);
    exit;
}
$stmt->bind_param("is", $id_usuario, $pass_actual);
$stmt->execute();
$row = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$row) {
    echo json_encode(["ok" => false, "mensaje" => "La contraseña actual es incorrecta."]);
    exit;
}

$model  = new UsuarioModel();
$result = $model->modificar(
    $id_usuario,
    null,        // nombre — IFNULL preserva existente
    null,        // apellidos — IFNULL preserva existente
    null,        // fecha_nacimiento — IFNULL preserva existente
    null,        // genero — IFNULL preserva existente
    null,        // correo — IFNULL preserva existente
    $pass_nueva,
    null,        // alias — IFNULL preserva existente
    null,        // foto — IFNULL preserva existente
    null,        // tipo_usuario — IFNULL preserva existente
    null,        // numero_cliente — IFNULL preserva existente
    null         // telefono — IFNULL preserva existente
);

error_log("Resultado cambiar password: " . json_encode($result));

if (!$result) {
    echo json_encode(["ok" => false, "mensaje" => "Error al actualizar la contraseña."]);
    exit;
}

echo json_encode([
    "ok"      => $result['resultado'] === 'OK',
    "mensaje" => $result['resultado'] === 'OK' ? "Contraseña actualizada correctamente." : ($result['mensaje'] ?? "Error al actualizar."),
]);
