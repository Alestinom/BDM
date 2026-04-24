<?php
header('Content-Type: application/json');
require_once __DIR__ . '/UsuarioModel.php';

$nombre      = $_POST['nombre']           ?? '';
$apellidos   = $_POST['apellidos']        ?? '';
$fecha_nac   = $_POST['fecha_nacimiento'] ?? '';
$genero      = $_POST['genero']           ?? '';
$correo      = $_POST['correo']           ?? '';
$contrasena  = $_POST['contrasena']       ?? '';
$alias       = $_POST['alias']            ?? '';
$num_cliente = $_POST['numero_cliente']   ?? '';
$telefono    = $_POST['telefono']         ?? '';

$foto = null;
if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
    $foto = file_get_contents($_FILES['foto']['tmp_name']);
}

if (!$nombre || !$apellidos || !$fecha_nac || !$genero ||
    !$correo || !$contrasena || !$alias || !$num_cliente || !$telefono) {
    echo json_encode(["ok" => false, "mensaje" => "Todos los campos son obligatorios."]);
    exit;
}

$model = new UsuarioModel();
$row   = $model->alta(
    $nombre, $apellidos, $fecha_nac, $genero, $correo,
    $contrasena, $alias, $foto, 'Asegurado', $num_cliente, $telefono
);

if ($row) {
    echo json_encode($row['resultado'] === 'OK'
        ? ["ok" => true,  "mensaje" => $row['mensaje']]
        : ["ok" => false, "mensaje" => $row['mensaje']]
    );
} else {
    echo json_encode(["ok" => false, "mensaje" => "Error al registrar el asegurado."]);
}
