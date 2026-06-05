<?php
header('Content-Type: application/json');
require_once __DIR__ . '/SiniestroModel.php';

$id_usuario       = $_GET['id_usuario']       ?? null;
$tipo_usuario     = $_GET['tipo_usuario']     ?? '';
$fecha_inicio     = $_GET['fecha_inicio']     ?? '';
$fecha_fin        = $_GET['fecha_fin']        ?? '';
$estado           = $_GET['estado']           ?? '';
$ajustador        = $_GET['ajustador']        ?? '';
$numero_siniestro = $_GET['numero_siniestro'] ?? '';

$model = new SiniestroModel();

// Obtener siniestros por rol
if ($tipo_usuario === 'Ajustador' && $id_usuario) {
    $siniestros = $model->listarPorAjustador($id_usuario);
} elseif ($tipo_usuario === 'Asegurado' && $id_usuario) {
    $siniestros = $model->listarPorAsegurado($id_usuario);
} else {
    $siniestros = $model->listar();
}

// Aplicar filtros opcionales en PHP
if ($numero_siniestro) {
    $num = (int)preg_replace('/[^0-9]/', '', $numero_siniestro);
    if ($num) {
        $siniestros = array_values(array_filter($siniestros, fn($s) => (int)($s['id_siniestro'] ?? 0) === $num));
    }
}
if ($fecha_inicio) {
    $siniestros = array_values(array_filter($siniestros, fn($s) => ($s['fecha'] ?? '') >= $fecha_inicio));
}
if ($fecha_fin) {
    $siniestros = array_values(array_filter($siniestros, fn($s) => ($s['fecha'] ?? '') <= $fecha_fin));
}
if ($estado) {
    $siniestros = array_values(array_filter($siniestros, fn($s) => ($s['estado_actual'] ?? '') === $estado));
}
if ($ajustador && $tipo_usuario === 'Supervisor') {
    $siniestros = array_values(array_filter($siniestros, fn($s) => (string)($s['id_ajustador'] ?? '') === (string)$ajustador));
}

echo json_encode(["ok" => true, "siniestros" => $siniestros]);
