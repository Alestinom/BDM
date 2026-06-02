<?php
header('Content-Type: application/json');
require_once __DIR__ . '/PolizaModel.php';

$id_poliza = $_POST['id_poliza'] ?? null;

if (!$id_poliza) {
    echo json_encode(['ok' => false, 'mensaje' => 'ID de póliza requerido.']);
    exit;
}

$model = new PolizaModel();
$row   = $model->baja($id_poliza);

if ($row) {
    echo json_encode($row['resultado'] === 'OK'
        ? ['ok' => true,  'mensaje' => $row['mensaje']]
        : ['ok' => false, 'mensaje' => $row['mensaje']]
    );
} else {
    echo json_encode(['ok' => false, 'mensaje' => 'Error al eliminar la póliza.']);
}
