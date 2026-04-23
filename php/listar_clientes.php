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
$clientes = [];

if ($result) {
    while ($row = $result->fetch_assoc()) {
        if ($row['tipo_usuario'] === 'Asegurado') {
            $clientes[] = [
                "id_usuario"     => $row['id_usuario'],
                "nombre"         => $row['nombre'],
                "apellidos"      => $row['apellidos'],
                "correo"         => $row['correo'],
                "alias"          => $row['alias'],
                "numero_cliente" => $row['numero_cliente'],
                "telefono"       => $row['telefono'],
            ];
        }
    }
}

while ($conn->more_results() && $conn->next_result()) {}
$stmt->close();
$conn->close();

echo json_encode(["ok" => true, "clientes" => $clientes]);
