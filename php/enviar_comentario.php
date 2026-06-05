<?php
header('Content-Type: application/json');
require_once __DIR__ . '/ComentarioModel.php';
require_once __DIR__ . '/MultimediaModel.php';

$id_siniestro        = $_POST['id_siniestro']        ?? null;
$id_usuario          = $_POST['id_usuario']          ?? null;
$mensaje             = trim($_POST['mensaje']         ?? '');
$id_comentario_padre = $_POST['id_comentario_padre'] ?? null;

$tieneImagen = !empty($_FILES['imagen']['tmp_name']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK;

if (!$id_siniestro || !$id_usuario) {
    echo json_encode(["ok" => false, "mensaje" => "Faltan datos obligatorios."]);
    exit;
}
if (!$mensaje && !$tieneImagen) {
    echo json_encode(["ok" => false, "mensaje" => "El mensaje no puede estar vacío."]);
    exit;
}

$id_comentario_padre = ($id_comentario_padre !== null && $id_comentario_padre !== '') ? $id_comentario_padre : null;

// Procesar imagen adjunta si existe
$id_multimedia = null;
if ($tieneImagen) {
    $archivo        = file_get_contents($_FILES['imagen']['tmp_name']);
    $nombre_archivo = basename($_FILES['imagen']['name']);

    $mmModel  = new MultimediaModel();
    $mmResult = $mmModel->alta($id_siniestro, $id_usuario, 'imagen_chat', $archivo, $nombre_archivo);
    if (!$mmResult) {
        echo json_encode(["ok" => false, "mensaje" => "Error al guardar la imagen."]);
        exit;
    }
    $id_multimedia = $mmResult['id_multimedia'];
}

// El mensaje es obligatorio si no hay imagen; usar espacio si solo se envió imagen
$mensajeEnviar = $mensaje ?: ' ';

$model  = new ComentarioModel();
$result = $model->alta($id_siniestro, $id_usuario, $id_comentario_padre, $mensajeEnviar, $id_multimedia);

if (!$result) {
    echo json_encode(["ok" => false, "mensaje" => "Error al enviar el comentario."]);
    exit;
}

if ($result['resultado'] === 'OK') {
    echo json_encode([
        "ok"            => true,
        "mensaje"       => $result['mensaje'],
        "id_comentario" => $result['id_comentario'],
        "id_multimedia" => $id_multimedia,
    ]);
} else {
    echo json_encode(["ok" => false, "mensaje" => $result['mensaje']]);
}
