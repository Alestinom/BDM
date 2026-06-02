<?php
header('Content-Type: application/json');
require_once __DIR__ . '/CompaniaModel.php';

$id_compania = $_POST['id_compania'] ?? null;

if (!$id_compania) {
    echo json_encode(['ok' => false, 'mensaje' => 'ID de compañía requerido.']);
    exit;
}

$model = new CompaniaModel();
$row   = $model->baja($id_compania);

if ($row) {
    echo json_encode($row['resultado'] === 'OK'
        ? ['ok' => true,  'mensaje' => $row['mensaje']]
        : ['ok' => false, 'mensaje' => $row['mensaje']]
    );
} else {
    echo json_encode(['ok' => false, 'mensaje' => 'Error al eliminar la compañía.']);
}
