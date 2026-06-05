<?php
require_once __DIR__ . '/Conexion.php';

$id_usuario = $_GET['id'] ?? null;
if (!$id_usuario) { http_response_code(400); exit; }

$conn = Conexion::getInstance()->getConexion();
$stmt = $conn->prepare("SELECT foto FROM Usuarios WHERE id_usuario = ? AND foto IS NOT NULL");
if (!$stmt) { http_response_code(500); exit; }
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$result = $stmt->get_result();
$row    = $result->fetch_assoc();
$stmt->close();

if (!$row || !$row['foto']) { http_response_code(404); exit; }

$data = $row['foto'];
// Detect MIME from magic bytes
$sig  = substr($data, 0, 4);
if (substr($sig, 0, 3) === "\xFF\xD8\xFF") $mime = 'image/jpeg';
elseif (substr($sig, 0, 4) === "\x89PNG")  $mime = 'image/png';
elseif (substr($sig, 0, 4) === "GIF8")     $mime = 'image/gif';
elseif (substr($sig, 0, 4) === "RIFF")     $mime = 'image/webp';
else                                        $mime = 'image/jpeg';

header('Content-Type: ' . $mime);
header('Cache-Control: max-age=3600');
echo $data;
exit;
