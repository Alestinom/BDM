<?php
include("conexion.php");

$correo = $_POST['correo'];
$password = $_POST['password'];

$sql = "SELECT nombre, apellidos, tipo_usuario, correo 
        FROM Usuarios 
        WHERE correo = ? AND contrasena = ? AND activo = 1";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ss",$correo,$password);
$stmt->execute();

$result = $stmt->get_result();

if($result->num_rows > 0){

    $usuario = $result->fetch_assoc();

    $nombreCompleto = $usuario['nombre']." ".$usuario['apellidos'];

    $iniciales = strtoupper(substr($usuario['nombre'],0,1) . substr($usuario['apellidos'],0,1));

    echo json_encode([
        "ok"=>true,
        "tipo"=>$usuario['tipo_usuario'],
        "nombre"=>$nombreCompleto,
        "iniciales"=>$iniciales,
        "correo"=>$usuario['correo']
    ]);

}else{

    echo json_encode(["ok"=>false]);

}

$conn->close();