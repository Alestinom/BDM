<?php
header('Content-Type: application/json');
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/debug.log');
error_reporting(E_ALL);

require_once __DIR__ . '/SiniestroModel.php';
require_once __DIR__ . '/MultimediaModel.php';

$id_poliza      = $_POST['id_poliza']      ?? null;
$id_unidad      = $_POST['id_unidad']      ?? null;
$id_usuario     = $_POST['id_usuario']     ?? null;
$fecha          = $_POST['fecha']          ?? '';
$hora           = $_POST['hora']           ?? '';
$ubicacion      = trim($_POST['ubicacion']    ?? '');
$descripcion    = trim($_POST['descripcion']  ?? '');
$otras_unidades = $_POST['otras_unidades'] ?? '0';

error_log("=== INICIO REGISTRO SINIESTRO ===");
error_log("POST recibido: " . json_encode($_POST));

if (!$id_poliza || !$id_unidad || !$id_usuario || !$fecha || !$hora || !$ubicacion || !$descripcion) {
    echo json_encode(["ok" => false, "mensaje" => "Todos los campos obligatorios deben estar presentes."]);
    exit;
}

$model  = new SiniestroModel();
$rowSin = $model->alta($id_poliza, $id_unidad, $id_usuario, $fecha, $hora, $ubicacion, $descripcion, $otras_unidades);

error_log("Resultado SP_Siniestros ALTA: " . json_encode($rowSin));

if (!$rowSin || $rowSin['resultado'] !== 'OK') {
    echo json_encode(["ok" => false, "mensaje" => $rowSin['mensaje'] ?? 'Error al registrar el siniestro.']);
    exit;
}

$id_siniestro = $rowSin['id_siniestro'];
error_log("id_siniestro creado: $id_siniestro");

$modelMedia  = new MultimediaModel();
$mediaErrores = [];

// Procesar imágenes
if (isset($_FILES['imagenes']) && is_array($_FILES['imagenes']['name'])) {
    $count = count($_FILES['imagenes']['name']);
    for ($i = 0; $i < $count; $i++) {
        if ($_FILES['imagenes']['error'][$i] !== UPLOAD_ERR_OK) continue;
        $binario  = file_get_contents($_FILES['imagenes']['tmp_name'][$i]);
        $nombre   = $_FILES['imagenes']['name'][$i];
        $rowMedia = $modelMedia->alta($id_siniestro, $id_usuario, 'imagen', $binario, $nombre);
        error_log("Multimedia imagen ($nombre): " . json_encode($rowMedia));
        if (!$rowMedia || $rowMedia['resultado'] !== 'OK') {
            $mediaErrores[] = "Imagen $nombre: " . ($rowMedia['mensaje'] ?? 'error');
        }
    }
}

// Procesar videos
if (isset($_FILES['videos']) && is_array($_FILES['videos']['name'])) {
    $count = count($_FILES['videos']['name']);
    for ($i = 0; $i < $count; $i++) {
        if ($_FILES['videos']['error'][$i] !== UPLOAD_ERR_OK) continue;
        $binario  = file_get_contents($_FILES['videos']['tmp_name'][$i]);
        $nombre   = $_FILES['videos']['name'][$i];
        $rowMedia = $modelMedia->alta($id_siniestro, $id_usuario, 'video', $binario, $nombre);
        error_log("Multimedia video ($nombre): " . json_encode($rowMedia));
        if (!$rowMedia || $rowMedia['resultado'] !== 'OK') {
            $mediaErrores[] = "Video $nombre: " . ($rowMedia['mensaje'] ?? 'error');
        }
    }
}

$msg = $rowSin['mensaje'] ?? 'Siniestro registrado correctamente.';
if (!empty($mediaErrores)) {
    $msg .= ' Advertencias en multimedia: ' . implode('; ', $mediaErrores);
}

echo json_encode([
    "ok"           => true,
    "id_siniestro" => $id_siniestro,
    "mensaje"      => $msg,
]);
