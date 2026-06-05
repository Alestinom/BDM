<?php
require_once __DIR__ . '/UsuarioModel.php';

$id_usuario = $_GET['id'] ?? null;
if (!$id_usuario) { http_response_code(400); exit; }

$model = new UsuarioModel();
$row   = $model->obtenerFoto($id_usuario);

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
