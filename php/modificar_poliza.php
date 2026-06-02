<?php
header('Content-Type: application/json');
require_once __DIR__ . '/PolizaModel.php';

$id_poliza         = $_POST['id_poliza']          ?? null;
$id_asegurado      = $_POST['id_asegurado']       ?? null;
$id_compania       = $_POST['id_compania']         ?? '';
$numero_poliza     = $_POST['numero_poliza']       ?? '';
$fecha_inicio      = $_POST['fecha_inicio']        ?? '';
$fecha_vencimiento = $_POST['fecha_vencimiento']   ?? '';
$tipo_cobertura    = $_POST['tipo_cobertura']      ?? '';
$monto_deducible   = $_POST['monto_deducible']     ?? '';

if (!$id_poliza) {
    echo json_encode(['ok' => false, 'mensaje' => 'ID de póliza requerido.']);
    exit;
}

$model = new PolizaModel();
$row   = $model->modificar(
    $id_poliza, $id_asegurado, $id_compania, $numero_poliza,
    $fecha_inicio, $fecha_vencimiento, $tipo_cobertura, $monto_deducible
);

if ($row) {
    echo json_encode($row['resultado'] === 'OK'
        ? ['ok' => true,  'mensaje' => $row['mensaje']]
        : ['ok' => false, 'mensaje' => $row['mensaje']]
    );
} else {
    echo json_encode(['ok' => false, 'mensaje' => 'Error al modificar la póliza.']);
}
