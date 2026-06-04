<?php
require_once __DIR__ . '/Conexion.php';

$id_multimedia = $_GET['id'] ?? null;

if (!$id_multimedia) {
    http_response_code(400);
    exit;
}

$conn = Conexion::getInstance()->getConexion();
$stmt = $conn->prepare("SELECT tipo, archivo, nombre_archivo FROM Multimedia WHERE id_multimedia = ?");
if (!$stmt) {
    http_response_code(500);
    exit;
}
$stmt->bind_param("i", $id_multimedia);
$stmt->execute();
$result = $stmt->get_result();
$row    = $result->fetch_assoc();
$stmt->close();

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
