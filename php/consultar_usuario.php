<?php
header('Content-Type: application/json');
require_once __DIR__ . '/Conexion.php';

$id_usuario = $_GET['id'] ?? null;

if (!$id_usuario) {
    echo json_encode(["ok" => false, "mensaje" => "ID de usuario requerido."]);
    exit;
}

// Query directa excluyendo el campo foto (LONGBLOB) para evitar problemas de memoria y JSON corrupto.
// La foto se sirve por separado desde obtener_foto_usuario.php.
$conn = Conexion::getInstance()->getConexion();
$stmt = $conn->prepare(
    "SELECT u.id_usuario, u.nombre, u.apellidos, u.fecha_nacimiento,
            u.genero, u.correo, u.alias, u.tipo_usuario, u.activo,
            a.id_asegurado, a.numero_cliente, a.telefono
     FROM Usuarios u
     LEFT JOIN Asegurados a ON a.id_usuario = u.id_usuario
     WHERE u.id_usuario = ?"
);

if (!$stmt) {
    echo json_encode(["ok" => false, "mensaje" => "Error en consulta: " . $conn->error]);
    exit;
}

$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$result  = $stmt->get_result();
$usuario = $result->fetch_assoc();
$stmt->close();

if ($usuario) {
    echo json_encode(["ok" => true, "usuario" => $usuario]);
} else {
    echo json_encode(["ok" => false, "mensaje" => "Usuario no encontrado."]);
}
