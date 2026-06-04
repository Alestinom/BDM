<?php
header('Content-Type: application/json');
require_once __DIR__ . '/VehiculoModel.php';
require_once __DIR__ . '/PolizaModel.php';

$id_asegurado  = $_POST['id_asegurado'] ?? null;
$vehiculosJson = $_POST['vehiculos']    ?? '[]';

if (!$id_asegurado) {
    echo json_encode(["ok" => false, "mensaje" => "ID de asegurado requerido."]);
    exit;
}

$vehiculos = json_decode($vehiculosJson, true);
if (!is_array($vehiculos) || count($vehiculos) === 0) {
    echo json_encode(["ok" => false, "mensaje" => "No se recibieron vehículos para agregar."]);
    exit;
}

$modelVehiculo = new VehiculoModel();
$modelPoliza   = new PolizaModel();
$errores       = [];
$agregados     = 0;

foreach ($vehiculos as $v) {
    $marca       = trim($v['marca']             ?? '');
    $modelo      = trim($v['modelo']            ?? '');
    $anio        = $v['anio']                   ?? '';
    $placas      = trim($v['placas']            ?? '');
    $serie       = trim($v['serie']             ?? '');
    $color       = trim($v['color']             ?? '');
    $id_compania = $v['id_compania']            ?? '';
    $cobertura   = $v['tipo_cobertura']         ?? '';
    $deducible   = $v['monto_deducible']        ?? '';
    $fInicio     = $v['fecha_inicio']           ?? '';
    $fVence      = $v['fecha_vencimiento']      ?? '';

    $rowVeh = $modelVehiculo->alta($id_asegurado, $marca, $modelo, $anio, $placas, $serie, $color);
    if (!$rowVeh || $rowVeh['resultado'] !== 'OK') {
        $errores[] = "Vehículo $placas: " . ($rowVeh['mensaje'] ?? 'error al registrar');
        continue;
    }

    $rowPol = $modelPoliza->alta($id_asegurado, $id_compania, null, $fInicio, $fVence, $cobertura, $deducible);
    if (!$rowPol || $rowPol['resultado'] !== 'OK') {
        $errores[] = "Póliza para $placas: " . ($rowPol['mensaje'] ?? 'error al registrar');
        continue;
    }

    $agregados++;
}

if (count($errores) > 0) {
    echo json_encode([
        "ok"      => true,
        "mensaje" => "$agregados vehículo(s) agregado(s). Advertencias: " . implode('; ', $errores),
    ]);
} else {
    echo json_encode([
        "ok"      => true,
        "mensaje" => "$agregados vehículo(s) agregado(s) correctamente.",
    ]);
}
