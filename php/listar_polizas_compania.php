<?php
header('Content-Type: application/json');
require_once __DIR__ . '/PolizaModel.php';

$id_compania = $_GET['id_compania'] ?? null;

if (!$id_compania) {
    echo json_encode(['ok' => false, 'mensaje' => 'ID de compañía requerido.']);
    exit;
}

$model   = new PolizaModel();
$rows    = $model->listarPorCompania($id_compania);
$polizas = [];

foreach ($rows as $row) {
    $polizas[] = [
        'id_poliza'         => $row['id_poliza'],
        'id_compania'       => $row['id_compania'],
        'id_asegurado'      => $row['id_asegurado'],
        'numero_poliza'     => $row['numero_poliza'],
        'tipo_cobertura'    => $row['tipo_cobertura'],
        'monto_deducible'   => $row['monto_deducible'],
        'fecha_inicio'      => $row['fecha_inicio'],
        'fecha_vencimiento' => $row['fecha_vencimiento'],
        'nombre_compania'   => $row['nombre_compania'] ?? null,
    ];
}

echo json_encode(['ok' => true, 'polizas' => $polizas]);
