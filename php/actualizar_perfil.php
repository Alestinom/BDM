<?php
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/debug.log');
error_reporting(E_ALL);

header('Content-Type: application/json');
require_once __DIR__ . '/UsuarioModel.php';

error_log("=== INICIO ACTUALIZAR PERFIL ===");
error_log("POST: " . json_encode($_POST));

$id_usuario = $_POST['id_usuario'] ?? null;
$nombre     = trim($_POST['nombre']    ?? '');
$apellidos  = trim($_POST['apellidos'] ?? '');
$genero     = $_POST['genero']         ?? null;
$correo     = trim($_POST['correo']    ?? '');
$alias      = trim($_POST['alias']     ?? '');
$telefono   = $_POST['telefono']       ?? null;

if (!$id_usuario || !$nombre || !$apellidos || !$correo || !$alias) {
    echo json_encode(["ok" => false, "mensaje" => "Completa todos los campos obligatorios."]);
    exit;
}

// Foto nueva si se subió
$foto = null;
if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
    $foto = file_get_contents($_FILES['foto']['tmp_name']);
    error_log("Foto recibida, tamaño: " . strlen($foto));
}

$telefono = ($telefono !== null && $telefono !== '') ? $telefono : null;

$model  = new UsuarioModel();
$result = $model->modificar(
    $id_usuario,
    $nombre,
    $apellidos,
    null,        // fecha_nacimiento — IFNULL preserva existente
    $genero,
    $correo,
    null,        // contrasena — IFNULL preserva existente
    $alias,
    $foto,       // null si no hay foto nueva — IFNULL preserva existente
    null,        // tipo_usuario — IFNULL preserva existente
    null,        // numero_cliente — IFNULL preserva existente
    $telefono
);

error_log("Resultado modificar: " . json_encode($result));

if (!$result) {
    echo json_encode(["ok" => false, "mensaje" => "Error al actualizar el perfil."]);
    exit;
}

echo json_encode([
    "ok"      => $result['resultado'] === 'OK',
    "mensaje" => $result['mensaje'] ?? ($result['resultado'] === 'OK' ? "Perfil actualizado correctamente." : "Error al actualizar."),
]);
