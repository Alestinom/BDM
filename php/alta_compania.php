<?php
header('Content-Type: application/json');
require_once __DIR__ . '/CompaniaModel.php';

$nombre    = $_POST['nombre']    ?? '';
$telefono  = $_POST['telefono']  ?? '';
$direccion = $_POST['direccion'] ?? '';

if (!$nombre || !$telefono || !$direccion) {
    echo json_encode(['ok' => false, 'mensaje' => 'Todos los campos son obligatorios.']);
    exit;
}

$model = new CompaniaModel();
$row   = $model->alta($nombre, $telefono, $direccion);

if ($row) {
    echo json_encode($row['resultado'] === 'OK'
        ? ['ok' => true,  'mensaje' => $row['mensaje']]
        : ['ok' => false, 'mensaje' => $row['mensaje']]
    );
} else {
    echo json_encode(['ok' => false, 'mensaje' => 'Error al registrar la compañía.']);
}
