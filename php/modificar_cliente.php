<?php
header('Content-Type: application/json');
require_once __DIR__ . '/UsuarioModel.php';

$id_usuario   = $_POST['id_usuario']       ?? null;
$nombre       = $_POST['nombre']           ?? '';
$apellidos    = $_POST['apellidos']        ?? '';
$fecha_nac    = $_POST['fecha_nacimiento'] ?? '';
$genero       = $_POST['genero']           ?? '';
$correo       = $_POST['correo']           ?? '';
$contrasena   = ($_POST['contrasena']      ?? '') ?: null;
$alias        = $_POST['alias']            ?? '';
$tipo_usuario = $_POST['tipo_usuario']     ?? null;
$num_cliente  = $_POST['numero_cliente']   ?? null;
$telefono     = $_POST['telefono']         ?? null;

if (!$id_usuario) {
    echo json_encode(["ok" => false, "mensaje" => "ID de usuario requerido."]);
    exit;
}

$foto = null;
if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
    $foto = file_get_contents($_FILES['foto']['tmp_name']);
}

$model = new UsuarioModel();
$row   = $model->modificar(
    $id_usuario, $nombre, $apellidos, $fecha_nac, $genero,
    $correo, $contrasena, $alias, $foto, $tipo_usuario,
    $num_cliente, $telefono
);

if ($row) {
    echo json_encode($row['resultado'] === 'OK'
        ? ["ok" => true,  "mensaje" => $row['mensaje']]
        : ["ok" => false, "mensaje" => $row['mensaje']]
    );
} else {
    echo json_encode(["ok" => false, "mensaje" => "Error al modificar el asegurado."]);
}
