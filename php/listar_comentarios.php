<?php
header('Content-Type: application/json');
require_once __DIR__ . '/ComentarioModel.php';

$id_siniestro = $_GET['id_siniestro'] ?? null;

if (!$id_siniestro) {
    echo json_encode(["ok" => false, "mensaje" => "ID de siniestro requerido."]);
    exit;
}

$model       = new ComentarioModel();
$comentarios = $model->listarPorSiniestro($id_siniestro);

echo json_encode(["ok" => true, "comentarios" => $comentarios]);
