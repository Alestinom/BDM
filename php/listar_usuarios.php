<?php
header('Content-Type: application/json');
include("../conexion.php");

$sql = "CALL SP_Usuarios(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    echo json_encode(["ok" => false, "mensaje" => "Error al preparar la consulta."]);
    exit;
}

$accion = 'LISTAR';
$n = null;

$stmt->bind_param(
    "sssssssssssss",
    $accion, $n, $n, $n, $n, $n, $n, $n, $n, $n, $n, $n, $n
);

$stmt->execute();
$result = $stmt->get_result();
$usuarios = [];

if ($result) {
    while ($row = $result->fetch_assoc()) {
        if ($row['tipo_usuario'] === 'Supervisor' ||
            $row['tipo_usuario'] === 'Ajustador') {
            $usuarios[] = [
                "id_usuario"   => $row['id_usuario'],
                "nombre"       => $row['nombre'],
                "apellidos"    => $row['apellidos'],
                "correo"       => $row['correo'],
                "alias"        => $row['alias'],
                "tipo_usuario" => $row['tipo_usuario'],
            ];
        }
    }
}

while ($conn->more_results() && $conn->next_result()) {}
$stmt->close();
$conn->close();

echo json_encode(["ok" => true, "usuarios" => $usuarios]);
