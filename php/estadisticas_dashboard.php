<?php
header('Content-Type: application/json');
require_once __DIR__ . '/Conexion.php';

$id_usuario   = $_GET['id_usuario']   ?? null;
$tipo_usuario = $_GET['tipo_usuario'] ?? '';

$conn = Conexion::getInstance()->getConexion();

if ($tipo_usuario === 'Ajustador' && $id_usuario) {
    // Stats personales del ajustador desde V_SiniestrosCompletos
    $stmt = $conn->prepare("
        SELECT
            COUNT(*) AS total_siniestros,
            SUM(CASE WHEN estado_actual = 'Registrado'   THEN 1 ELSE 0 END) AS registrados,
            SUM(CASE WHEN estado_actual = 'En revisión'  THEN 1 ELSE 0 END) AS en_revision,
            SUM(CASE WHEN estado_actual IN (
                'Aceptado','Aceptado con deducible',
                'Aceptado sin deducible','Aplica reparación'
            ) THEN 1 ELSE 0 END) AS aprobados,
            SUM(CASE WHEN estado_actual = 'Rechazado'    THEN 1 ELSE 0 END) AS rechazados,
            SUM(CASE WHEN estado_actual = 'Pérdida total' THEN 1 ELSE 0 END) AS perdida_total,
            SUM(CASE WHEN estado_actual = 'Cerrado'      THEN 1 ELSE 0 END) AS cerrados
        FROM V_SiniestrosCompletos
        WHERE id_ajustador = ?
    ");
    if (!$stmt) {
        echo json_encode(["ok" => false, "mensaje" => "Error en consulta: " . $conn->error]);
        exit;
    }
    $stmt->bind_param("i", $id_usuario);
    $stmt->execute();
    $result = $stmt->get_result();
    $row    = $result->fetch_assoc();
    $stmt->close();
} else {
    // Stats globales desde la vista (Supervisor u otros roles)
    $result = $conn->query("SELECT * FROM V_EstadisticasDashboard");
    if (!$result) {
        echo json_encode(["ok" => false, "mensaje" => "Error en consulta: " . $conn->error]);
        exit;
    }
    $row = $result->fetch_assoc();
}

$row = $row ?: [];

echo json_encode([
    "ok"               => true,
    "total_siniestros" => (int)($row['total_siniestros'] ?? 0),
    "registrados"      => (int)($row['registrados']      ?? 0),
    "en_revision"      => (int)($row['en_revision']      ?? 0),
    "aprobados"        => (int)($row['aprobados']        ?? 0),
    "rechazados"       => (int)($row['rechazados']       ?? 0),
    "perdida_total"    => (int)($row['perdida_total']    ?? 0),
    "cerrados"         => (int)($row['cerrados']         ?? 0),
]);
