<?php
header('Content-Type: application/json');
require_once __DIR__ . '/SiniestroModel.php';
require_once __DIR__ . '/MultimediaModel.php';
require_once __DIR__ . '/AprobacionModel.php';

$id_siniestro = $_GET['id'] ?? null;

if (!$id_siniestro) {
    echo json_encode(["ok" => false, "mensaje" => "ID de siniestro requerido."]);
    exit;
}

$siniestroModel = new SiniestroModel();

// Datos del siniestro
$siniestro = $siniestroModel->consultar($id_siniestro);

if (!$siniestro) {
    echo json_encode(["ok" => false, "mensaje" => "Siniestro no encontrado."]);
    exit;
}

// Historial de estados via SP_Siniestros('HISTORIAL')
$historial = $siniestroModel->historial($id_siniestro);

// Multimedia (metadatos sin BLOB) via SP_Multimedia('LISTAR_POR_SINIESTRO')
$multimediaModel = new MultimediaModel();
$multimedia      = $multimediaModel->listarPorSiniestro($id_siniestro);

// Aprobación existente (null si no hay)
$aprobacionModel = new AprobacionModel();
$aprobacion      = $aprobacionModel->consultar($id_siniestro);

echo json_encode([
    "ok"         => true,
    "siniestro"  => $siniestro,
    "historial"  => $historial,
    "multimedia" => $multimedia,
    "aprobacion" => $aprobacion,
]);
