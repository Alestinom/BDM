<?php
header('Content-Type: application/json');
require_once __DIR__ . '/SiniestroModel.php';

$id_usuario      = $_GET['id_usuario']      ?? null;
$tipo_usuario    = $_GET['tipo_usuario']    ?? '';
$numero_siniestro = $_GET['numero_siniestro'] ?? '';
$numero_poliza   = $_GET['numero_poliza']   ?? '';
$nombre_cliente  = $_GET['nombre_cliente']  ?? '';
$placas          = $_GET['placas']          ?? '';
$numero_serie    = $_GET['numero_serie']    ?? '';
$nombre_compania = $_GET['nombre_compania'] ?? '';
$fecha           = $_GET['fecha']           ?? '';

$model = new SiniestroModel();

// Obtener siniestros por rol
if ($tipo_usuario === 'Ajustador' && $id_usuario) {
    $siniestros = $model->listarPorAjustador($id_usuario);
} elseif ($tipo_usuario === 'Asegurado' && $id_usuario) {
    $siniestros = $model->listarPorAsegurado($id_usuario);
} else {
    $siniestros = $model->listar();
}

// Aplicar criterios de búsqueda en PHP
if ($numero_siniestro) {
    $num = (int)preg_replace('/[^0-9]/', '', $numero_siniestro);
    if ($num) {
        $siniestros = array_values(array_filter($siniestros, fn($s) => (int)($s['id_siniestro'] ?? 0) === $num));
    }
}
if ($numero_poliza) {
    $siniestros = array_values(array_filter($siniestros, fn($s) => stripos($s['numero_poliza'] ?? '', $numero_poliza) !== false));
}
if ($nombre_cliente) {
    $siniestros = array_values(array_filter($siniestros, fn($s) => stripos($s['nombre_asegurado'] ?? '', $nombre_cliente) !== false));
}
if ($placas) {
    $siniestros = array_values(array_filter($siniestros, fn($s) => stripos($s['placas'] ?? '', $placas) !== false));
}
if ($numero_serie) {
    $siniestros = array_values(array_filter($siniestros, fn($s) => stripos($s['numero_serie'] ?? '', $numero_serie) !== false));
}
if ($nombre_compania) {
    $siniestros = array_values(array_filter($siniestros, fn($s) => ($s['nombre_compania'] ?? '') === $nombre_compania));
}
if ($fecha) {
    $siniestros = array_values(array_filter($siniestros, fn($s) => ($s['fecha'] ?? '') === $fecha));
}

echo json_encode(["ok" => true, "siniestros" => $siniestros]);
