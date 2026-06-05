<?php
require_once __DIR__ . '/MultimediaModel.php';

$id_multimedia = $_GET['id'] ?? null;

if (!$id_multimedia) {
    http_response_code(400);
    exit;
}

$model = new MultimediaModel();
$row   = $model->obtener($id_multimedia);

if (!$row || !$row['archivo']) {
    http_response_code(404);
    exit;
}

// Detectar MIME según extensión del nombre de archivo
$ext  = strtolower(pathinfo($row['nombre_archivo'], PATHINFO_EXTENSION));
$mimes = [
    'jpg'  => 'image/jpeg',
    'jpeg' => 'image/jpeg',
    'png'  => 'image/png',
    'gif'  => 'image/gif',
    'webp' => 'image/webp',
    'mp4'  => 'video/mp4',
    'avi'  => 'video/avi',
    'mov'  => 'video/quicktime',
    'wmv'  => 'video/x-ms-wmv',
    'mkv'  => 'video/x-matroska',
];

if (isset($mimes[$ext])) {
    $mime = $mimes[$ext];
} elseif ($row['tipo'] === 'video') {
    $mime = 'video/mp4';
} else {
    $mime = 'image/jpeg';
}

header('Content-Type: ' . $mime);
header('Content-Disposition: inline; filename="' . basename($row['nombre_archivo']) . '"');
echo $row['archivo'];
exit;
