<?php
header('Content-Type: application/json');
require_once __DIR__ . '/AprobacionModel.php';

$model      = new AprobacionModel();
$pendientes = $model->listarPendientes();

echo json_encode(["ok" => true, "pendientes" => $pendientes]);
