<?php
header('Content-Type: application/json');
include("conexion.php");

$correo = $_POST['correo'] ?? '';
$password = $_POST['password'] ?? '';

error_log("LOGIN INTENT - correo: " . $correo . " | password: " . $password);

$sql = "CALL SP_Usuarios(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    echo json_encode(["ok" => false]);
    $conn->close();
    exit;
}

$accion = 'LOGIN';
$n1 = $n2 = $n3 = $n4 = $n5 = null;
$n6 = $n7 = $n8 = $n9 = $n10 = null;

$stmt->bind_param(
    "sssssssssssss",
    $accion,
    $n1,
    $n2,
    $n3,
    $n4,
    $n5,
    $correo,
    $password,
    $n6,
    $n7,
    $n8,
    $n9,
    $n10
);

$stmt->execute();
error_log("Execute error: " . $stmt->error);
$result = $stmt->get_result();
error_log("Rows: " . ($result ? $result->num_rows : 'result es false'));

if ($result && $result->num_rows > 0) {

    $usuario = $result->fetch_assoc();

    $nombreCompleto = trim($usuario['nombre'] . ' ' . $usuario['apellidos']);

    $iniciales = strtoupper(
        substr($usuario['nombre'], 0, 1) . substr($usuario['apellidos'], 0, 1)
    );

    echo json_encode([
        "ok" => true,
        "tipo" => $usuario['tipo_usuario'],
        "nombre" => $nombreCompleto,
        "iniciales" => $iniciales,
        "correo" => $usuario['correo'],
    ]);
} else {

    echo json_encode(["ok" => false]);
}

while ($conn->more_results() && $conn->next_result()) {
    /* consumir resultsets adicionales del SP */
}

$stmt->close();

$conn->close();
