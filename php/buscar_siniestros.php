<?php
header('Content-Type: application/json');
require_once __DIR__ . '/Conexion.php';

$id_usuario      = $_GET['id_usuario']      ?? null;
$tipo_usuario    = $_GET['tipo_usuario']    ?? '';
$numero_siniestro = $_GET['numero_siniestro'] ?? '';
$numero_poliza   = $_GET['numero_poliza']   ?? '';
$nombre_cliente  = $_GET['nombre_cliente']  ?? '';
$placas          = $_GET['placas']          ?? '';
$numero_serie    = $_GET['numero_serie']    ?? '';
$nombre_compania = $_GET['nombre_compania'] ?? '';
$fecha           = $_GET['fecha']           ?? '';

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

// Criterios de búsqueda
if ($numero_siniestro) {
    $num = preg_replace('/[^0-9]/', '', $numero_siniestro);
    if ($num) {
        $where[]  = 'id_siniestro = ?';
        $types   .= 'i';
        $params[] = (int)$num;
    }
}
if ($numero_poliza) {
    $where[]  = 'numero_poliza LIKE ?';
    $types   .= 's';
    $params[] = '%' . $numero_poliza . '%';
}
if ($nombre_cliente) {
    $where[]  = 'nombre_asegurado LIKE ?';
    $types   .= 's';
    $params[] = '%' . $nombre_cliente . '%';
}
if ($placas) {
    $where[]  = 'placas LIKE ?';
    $types   .= 's';
    $params[] = '%' . $placas . '%';
}
if ($numero_serie) {
    $where[]  = 'numero_serie LIKE ?';
    $types   .= 's';
    $params[] = '%' . $numero_serie . '%';
}
if ($nombre_compania) {
    $where[]  = 'nombre_compania = ?';
    $types   .= 's';
    $params[] = $nombre_compania;
}
if ($fecha) {
    $where[]  = 'fecha = ?';
    $types   .= 's';
    $params[] = $fecha;
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
