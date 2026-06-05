<?php
header('Content-Type: application/json');
require_once __DIR__ . '/SiniestroModel.php';

$id_usuario   = $_GET['id_usuario']   ?? null;
$tipo_usuario = $_GET['tipo_usuario'] ?? '';

$model = new SiniestroModel();

if ($tipo_usuario === 'Ajustador' && $id_usuario) {
    $row = $model->estadisticasAjustador($id_usuario);
} else {
    $row = $model->estadisticasGlobales();
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
