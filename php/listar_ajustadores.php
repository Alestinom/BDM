<?php
header('Content-Type: application/json');
require_once __DIR__ . '/Conexion.php';

$conn   = Conexion::getInstance()->getConexion();
$result = $conn->query(
    "SELECT id_usuario, nombre, apellidos
     FROM Usuarios
     WHERE tipo_usuario = 'Ajustador' AND activo = 1
     ORDER BY nombre, apellidos"
);

$ajustadores = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $ajustadores[] = $row;
    }
}

echo json_encode(["ok" => true, "ajustadores" => $ajustadores]);
