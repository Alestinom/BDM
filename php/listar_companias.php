<?php
header('Content-Type: application/json');
require_once __DIR__ . '/CompaniaModel.php';

$model  = new CompaniaModel();
$result = $model->listarConEstado();

if (!$result['ok']) {
    echo json_encode(['ok' => false, 'mensaje' => 'Error al consultar compañías.']);
    exit;
}

$companias = [];
foreach ($result['rows'] as $row) {
    $companias[] = [
        'id_compania' => $row['id_compania'],
        'nombre'      => $row['nombre'],
        'telefono'    => $row['telefono'],
        'direccion'   => $row['direccion'],
    ];
}

echo json_encode(['ok' => true, 'companias' => $companias]);
