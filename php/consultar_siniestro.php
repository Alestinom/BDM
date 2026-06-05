<?php
header('Content-Type: application/json');
require_once __DIR__ . '/Conexion.php';
require_once __DIR__ . '/AprobacionModel.php';

$id_siniestro = $_GET['id'] ?? null;

if (!$id_siniestro) {
    echo json_encode(["ok" => false, "mensaje" => "ID de siniestro requerido."]);
    exit;
}

$conn = Conexion::getInstance()->getConexion();

// Datos del siniestro desde la vista
$stmt = $conn->prepare("SELECT * FROM V_SiniestrosCompletos WHERE id_siniestro = ?");
if (!$stmt) {
    echo json_encode(["ok" => false, "mensaje" => "Error en consulta: " . $conn->error]);
    exit;
}
$stmt->bind_param("i", $id_siniestro);
$stmt->execute();
$result    = $stmt->get_result();
$siniestro = $result->fetch_assoc();
$stmt->close();

if (!$siniestro) {
    echo json_encode(["ok" => false, "mensaje" => "Siniestro no encontrado."]);
    exit;
}

// Historial de estados
$stmt2 = $conn->prepare("SELECT * FROM V_HistorialSiniestro WHERE id_siniestro = ?");
if (!$stmt2) {
    echo json_encode(["ok" => false, "mensaje" => "Error en historial: " . $conn->error]);
    exit;
}
$stmt2->bind_param("i", $id_siniestro);
$stmt2->execute();
$result2  = $stmt2->get_result();
$historial = [];
while ($row = $result2->fetch_assoc()) {
    $historial[] = $row;
}
$stmt2->close();

// Multimedia (sin BLOB)
$stmt3 = $conn->prepare("SELECT * FROM V_MultimediaSiniestro WHERE id_siniestro = ?");
if (!$stmt3) {
    echo json_encode(["ok" => false, "mensaje" => "Error en multimedia: " . $conn->error]);
    exit;
}
$stmt3->bind_param("i", $id_siniestro);
$stmt3->execute();
$result3    = $stmt3->get_result();
$multimedia = [];
while ($row = $result3->fetch_assoc()) {
    $multimedia[] = $row;
}
$stmt3->close();

// Aprobación existente (null si no hay)
$aprobacionModel = new AprobacionModel();
$aprobacion      = $aprobacionModel->consultar($id_siniestro);

echo json_encode([
    "ok"         => true,
    "siniestro"  => $siniestro,
    "historial"  => $historial,
    "multimedia" => $multimedia,
    "aprobacion" => $aprobacion,
]);
