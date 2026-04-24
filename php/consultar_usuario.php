<?php
header('Content-Type: application/json');
include("../conexion.php");

$id_usuario = $_GET['id'] ?? null;
$n          = null;

if (!$id_usuario) {
    echo json_encode(["ok" => false, "mensaje" => "ID de usuario requerido."]);
    exit;
}

$accion = 'CONSULTAR';
$sql = "CALL SP_Usuarios(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    echo json_encode(["ok" => false, "mensaje" => "Error al preparar la consulta."]);
    exit;
}

$stmt->bind_param(
    "sssssssssssss",
    $accion, $id_usuario, $n, $n, $n, $n, $n, $n, $n, $n, $n, $n, $n
);

$stmt->execute();
$result = $stmt->get_result();
$row = $result ? $result->fetch_assoc() : null;

while ($conn->more_results() && $conn->next_result()) {}
$stmt->close();

if ($row) {
    echo json_encode(["ok" => true, "usuario" => $row]);
} else {
    echo json_encode(["ok" => false, "mensaje" => "Usuario no encontrado."]);
}

$conn->close();
