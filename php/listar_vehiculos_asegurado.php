<?php
header('Content-Type: application/json');
require_once __DIR__ . '/VehiculoModel.php';

$id_asegurado = $_GET['id'] ?? null;

if (!$id_asegurado) {
    echo json_encode(["ok" => false, "mensaje" => "ID de asegurado requerido."]);
    exit;
}

$model     = new VehiculoModel();
$vehiculos = $model->listarPorAsegurado($id_asegurado);

echo json_encode(["ok" => true, "vehiculos" => $vehiculos]);
