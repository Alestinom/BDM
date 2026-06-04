<?php
header('Content-Type: application/json');
require_once __DIR__ . '/Conexion.php';

$id_usuario      = $_GET['id_usuario']      ?? null;
$tipo_usuario    = $_GET['tipo_usuario']    ?? '';
$fecha_inicio    = $_GET['fecha_inicio']    ?? '';
$fecha_fin       = $_GET['fecha_fin']       ?? '';
$estado          = $_GET['estado']          ?? '';
$ajustador       = $_GET['ajustador']       ?? '';
$numero_siniestro = $_GET['numero_siniestro'] ?? '';

$conn   = Conexion::getInstance()->getConexion();
$where  = [];
$types  = '';
$params = [];

// Filtro obligatorio por rol
if ($tipo_usuario === 'Ajustador') {
    $where[]  = 'id_ajustador = ?';
    $types   .= 's';
    $params[] = $id_usuario;
} elseif ($tipo_usuario === 'Asegurado') {
    $where[]  = 'id_asegurado_usuario = ?';
    $types   .= 's';
    $params[] = $id_usuario;
}
// Supervisor: sin filtro base

// Filtros opcionales
if ($numero_siniestro) {
    $num = preg_replace('/[^0-9]/', '', $numero_siniestro);
    if ($num) {
        $where[]  = 'id_siniestro = ?';
        $types   .= 'i';
        $params[] = (int)$num;
    }
}
if ($fecha_inicio) {
    $where[]  = 'fecha >= ?';
    $types   .= 's';
    $params[] = $fecha_inicio;
}
if ($fecha_fin) {
    $where[]  = 'fecha <= ?';
    $types   .= 's';
    $params[] = $fecha_fin;
}
if ($estado) {
    $where[]  = 'estado_actual = ?';
    $types   .= 's';
    $params[] = $estado;
}
if ($ajustador && $tipo_usuario === 'Supervisor') {
    $where[]  = 'id_ajustador = ?';
    $types   .= 's';
    $params[] = $ajustador;
}

$sql = "SELECT * FROM V_SiniestrosCompletos";
if ($where) $sql .= " WHERE " . implode(' AND ', $where);
$sql .= " ORDER BY fecha DESC, hora DESC";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    echo json_encode(["ok" => false, "mensaje" => "Error en consulta: " . $conn->error]);
    exit;
}

if ($params) {
    $stmt->bind_param($types, ...$params);
}

if (!$stmt->execute()) {
    echo json_encode(["ok" => false, "mensaje" => "Error al ejecutar consulta: " . $stmt->error]);
    $stmt->close();
    exit;
}

$result     = $stmt->get_result();
$siniestros = [];
while ($row = $result->fetch_assoc()) {
    $siniestros[] = $row;
}
$stmt->close();

echo json_encode(["ok" => true, "siniestros" => $siniestros]);
