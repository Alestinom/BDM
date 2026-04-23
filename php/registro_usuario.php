<?php
header('Content-Type: application/json');
include("../conexion.php");

// Recibir datos del POST
$accion        = 'ALTA';
$id_usuario    = null;
$nombre        = $_POST['nombre'] ?? '';
$apellidos     = $_POST['apellidos'] ?? '';
$fecha_nac     = $_POST['fecha_nacimiento'] ?? '';
$genero        = $_POST['genero'] ?? '';
$correo        = $_POST['correo'] ?? '';
$contrasena    = $_POST['contrasena'] ?? '';
$alias         = $_POST['alias'] ?? '';
$tipo_usuario  = $_POST['tipo_usuario'] ?? '';
$num_cliente   = null;
$telefono      = null;

// Procesar foto como BLOB
$foto = null;
if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
    $foto = file_get_contents($_FILES['foto']['tmp_name']);
}

// Validaciones basicas
if (!$nombre || !$apellidos || !$fecha_nac || !$genero ||
    !$correo || !$contrasena || !$alias || !$tipo_usuario) {
    echo json_encode(["ok" => false, "mensaje" => "Todos los campos son obligatorios."]);
    exit;
}

// Llamar al SP
$sql = "CALL SP_Usuarios(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    echo json_encode(["ok" => false, "mensaje" => "Error al preparar la consulta."]);
    exit;
}

$stmt->bind_param(
    "sisssssssbss",
    $accion,
    $id_usuario,
    $nombre,
    $apellidos,
    $fecha_nac,
    $genero,
    $correo,
    $contrasena,
    $alias,
    $foto,
    $tipo_usuario,
    $num_cliente,
    $telefono
);

$stmt->execute();
$result = $stmt->get_result();

while ($conn->more_results() && $conn->next_result()) {}

if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    if ($row['resultado'] === 'OK') {
        echo json_encode(["ok" => true, "mensaje" => $row['mensaje']]);
    } else {
        echo json_encode(["ok" => false, "mensaje" => $row['mensaje']]);
    }
} else {
    echo json_encode(["ok" => false, "mensaje" => "Error al registrar el usuario."]);
}

$stmt->close();
$conn->close();
