<?php
header('Content-Type: application/json');
include("../conexion.php");

$accion      = 'MODIFICAR';
$id_usuario  = $_POST['id_usuario'] ?? null;
$nombre      = $_POST['nombre'] ?? '';
$apellidos   = $_POST['apellidos'] ?? '';
$fecha_nac   = $_POST['fecha_nacimiento'] ?? '';
$genero      = $_POST['genero'] ?? '';
$correo      = $_POST['correo'] ?? '';
$contrasena  = ($_POST['contrasena'] ?? '') ?: null;
$alias       = $_POST['alias'] ?? '';
$tipo_usuario = $_POST['tipo_usuario'] ?? null;
$num_cliente = $_POST['numero_cliente'] ?? null;
$telefono    = $_POST['telefono'] ?? null;

if (!$id_usuario) {
    echo json_encode(["ok" => false, "mensaje" => "ID de usuario requerido."]);
    exit;
}

$foto = null;
if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
    $foto = file_get_contents($_FILES['foto']['tmp_name']);
}

$sql = "CALL SP_Usuarios(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    echo json_encode(["ok" => false, "mensaje" => "Error al preparar la consulta."]);
    exit;
}

$stmt->bind_param(
    "sssssssssssss",
    $accion, $id_usuario, $nombre, $apellidos, $fecha_nac,
    $genero, $correo, $contrasena, $alias, $foto, $tipo_usuario,
    $num_cliente, $telefono
);

$stmt->execute();
$result = $stmt->get_result();
$row = $result ? $result->fetch_assoc() : null;

while ($conn->more_results() && $conn->next_result()) {}
$stmt->close();

if ($row) {
    if ($row['resultado'] === 'OK') {
        echo json_encode(["ok" => true, "mensaje" => $row['mensaje']]);
    } else {
        echo json_encode(["ok" => false, "mensaje" => $row['mensaje']]);
    }
} else {
    echo json_encode(["ok" => false, "mensaje" => "Error al modificar el asegurado."]);
}

$conn->close();
