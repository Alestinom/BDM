<?php
header('Content-Type: application/json');
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/debug.log');
error_reporting(E_ALL);
require_once __DIR__ . '/UsuarioModel.php';
require_once __DIR__ . '/PolizaModel.php';
require_once __DIR__ . '/VehiculoModel.php';

// Datos del asegurado (numero_cliente generado automáticamente por SP)
$nombre     = $_POST['nombre']           ?? '';
$apellidos  = $_POST['apellidos']        ?? '';
$fecha_nac  = $_POST['fecha_nacimiento'] ?? '';
$genero     = $_POST['genero']           ?? '';
$correo     = $_POST['correo']           ?? '';
$contrasena = $_POST['contrasena']       ?? '';
$alias      = $_POST['alias']            ?? '';
$telefono   = $_POST['telefono']         ?? '';

error_log("=== INICIO REGISTRO CLIENTE ===");
error_log("POST recibido: " . json_encode($_POST));
error_log("Vehiculos JSON: " . ($_POST['vehiculos'] ?? 'NO VIENE'));

$foto = null;
if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
    $foto = file_get_contents($_FILES['foto']['tmp_name']);
}

if (!$nombre || !$apellidos || !$fecha_nac || !$genero ||
    !$correo || !$contrasena || !$alias || !$telefono) {
    echo json_encode(["ok" => false, "mensaje" => "Todos los campos del asegurado son obligatorios."]);
    exit;
}

$vehiculosJson = $_POST['vehiculos'] ?? '[]';
error_log("registro_cliente.php: Vehiculos recibidos: " . $vehiculosJson);
$vehiculos     = json_decode($vehiculosJson, true);

if (!is_array($vehiculos) || count($vehiculos) === 0) {
    echo json_encode(["ok" => false, "mensaje" => "Debes registrar al menos un vehículo con su póliza."]);
    exit;
}

// 1. Crear asegurado (numero_cliente generado por F_GenerarNumeroCliente() en el SP)
$modelUsuario = new UsuarioModel();
$rowUsuario   = $modelUsuario->alta(
    $nombre, $apellidos, $fecha_nac, $genero, $correo,
    $contrasena, $alias, $foto, 'Asegurado', null, $telefono
);

if (!$rowUsuario) {
    echo json_encode(["ok" => false, "mensaje" => "Error al registrar el asegurado."]);
    exit;
}

if ($rowUsuario['resultado'] !== 'OK') {
    echo json_encode(["ok" => false, "mensaje" => $rowUsuario['mensaje']]);
    exit;
}

$id_asegurado = $rowUsuario['id_asegurado'] ?? null;
error_log("Resultado SP_Usuarios: " . json_encode($rowUsuario));
error_log("id_asegurado: " . ($id_asegurado ?? 'NULL'));
error_log("registro_cliente.php: id_asegurado obtenido: " . $id_asegurado);
if (!$id_asegurado) {
    error_log("registro_cliente.php: ALTA exitosa pero id_asegurado no devuelto. Fila: " . json_encode($rowUsuario));
    echo json_encode(["ok" => false, "mensaje" => "Asegurado registrado, pero no se pudo obtener su ID interno."]);
    exit;
}

// 2. Crear vehículos y pólizas
$modelVehiculo = new VehiculoModel();
$modelPoliza   = new PolizaModel();
$errores       = [];

foreach ($vehiculos as $v) {
    error_log("Procesando vehiculo: " . json_encode($v));
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

    // Crear vehículo
    $rowVeh = $modelVehiculo->alta($id_asegurado, $marca, $modelo, $anio, $placas, $serie, $color);
    error_log("registro_cliente.php: Resultado vehiculo ($placas): " . json_encode($rowVeh));
    if (!$rowVeh || $rowVeh['resultado'] !== 'OK') {
        $errores[] = "Vehículo $placas: " . ($rowVeh['mensaje'] ?? 'error al registrar');
        continue;
    }

    // Crear póliza (numero_poliza generado por F_GenerarNumeroPoliza() en el SP)
    $rowPol = $modelPoliza->alta($id_asegurado, $id_compania, null, $fInicio, $fVence, $cobertura, $deducible);
    error_log("registro_cliente.php: Resultado poliza ($placas): " . json_encode($rowPol));
    if (!$rowPol || $rowPol['resultado'] !== 'OK') {
        $errores[] = "Póliza para $placas: " . ($rowPol['mensaje'] ?? 'error al registrar');
    }
}

$total = count($vehiculos);
$ok    = $total - count($errores);

if (count($errores) > 0) {
    echo json_encode([
        "ok"     => true,
        "mensaje" => "Asegurado registrado. $ok/$total vehículo(s) y póliza(s) creados correctamente. Advertencias: " . implode('; ', $errores),
    ]);
} else {
    echo json_encode([
        "ok"     => true,
        "mensaje" => "Asegurado registrado correctamente con $total vehículo(s) y póliza(s).",
    ]);
}
