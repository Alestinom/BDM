<?php
header('Content-Type: application/json');
require_once __DIR__ . '/PolizaModel.php';

$model   = new PolizaModel();
$rows    = $model->listar();
$polizas = [];

foreach ($rows as $row) {
    $nombreAsegurado = $row['nombre_asegurado']
        ?? (trim(($row['nombre'] ?? '') . ' ' . ($row['apellidos'] ?? '')) ?: null);

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
        'nombre_asegurado'  => $nombreAsegurado,
    ];
}

echo json_encode(['ok' => true, 'polizas' => $polizas]);
