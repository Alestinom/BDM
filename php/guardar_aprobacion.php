<?php
header('Content-Type: application/json');
require_once __DIR__ . '/AprobacionModel.php';

$id_aprobacion            = $_POST['id_aprobacion']            ?? null;
$id_siniestro             = $_POST['id_siniestro']             ?? null;
$id_usuario               = $_POST['id_usuario']               ?? null;
$tipo_resolucion          = trim($_POST['tipo_resolucion']      ?? '');
$monto_pago               = $_POST['monto_pago']               ?? null;
$monto_deducible_aplicado = $_POST['monto_deducible_aplicado'] ?? null;
$fecha_resolucion         = $_POST['fecha_resolucion']         ?? date('Y-m-d');
$fecha_compromiso         = $_POST['fecha_compromiso']         ?? null;
$observaciones            = $_POST['observaciones']            ?? null;

if (!$id_usuario) {
    echo json_encode(["ok" => false, "mensaje" => "Faltan datos obligatorios."]);
    exit;
}
if (!$tipo_resolucion) {
    echo json_encode(["ok" => false, "mensaje" => "El tipo de resolución es obligatorio."]);
    exit;
}

// Convertir vacíos a null para que el SP los maneje correctamente
$monto_pago               = ($monto_pago !== null && $monto_pago !== '')                         ? $monto_pago               : null;
$monto_deducible_aplicado = ($monto_deducible_aplicado !== null && $monto_deducible_aplicado !== '') ? $monto_deducible_aplicado : null;
$fecha_compromiso         = ($fecha_compromiso !== null && $fecha_compromiso !== '')             ? $fecha_compromiso         : null;
$observaciones            = ($observaciones !== null && $observaciones !== '')                   ? $observaciones            : null;

$model = new AprobacionModel();

if ($id_aprobacion) {
    // Modificar resolución existente
    $result = $model->modificar(
        $id_aprobacion,
        $tipo_resolucion,
        $monto_pago,
        $monto_deducible_aplicado,
        $fecha_resolucion,
        $fecha_compromiso,
        $observaciones
    );
} else {
    // Alta: requiere id_siniestro
    if (!$id_siniestro) {
        echo json_encode(["ok" => false, "mensaje" => "Faltan datos obligatorios."]);
        exit;
    }
    $result = $model->alta(
        $id_siniestro,
        $id_usuario,
        $tipo_resolucion,
        $monto_pago,
        $monto_deducible_aplicado,
        $fecha_resolucion,
        $fecha_compromiso,
        $observaciones
    );
}

if (!$result) {
    echo json_encode(["ok" => false, "mensaje" => "Error al procesar la resolución."]);
    exit;
}

if ($result['resultado'] === 'OK') {
    echo json_encode([
        "ok"            => true,
        "mensaje"       => $result['mensaje'],
        "id_aprobacion" => $result['id_aprobacion'],
    ]);
} else {
    echo json_encode(["ok" => false, "mensaje" => $result['mensaje']]);
}
