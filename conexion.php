<?php
$host = "localhost";
$usuario = "root";
$password = "";
$base_datos = "BDM";

$conn = new mysqli($host, $usuario, $password, $base_datos);

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

$sql = "INSERT INTO Usuarios (nombre, correo) 
        VALUES ('Prueba', 'test@test.com')";

if ($conn->query($sql) === TRUE) {
    echo "✅ Datos insertados";
} else {
    echo "❌ Error: " . $conn->error;
}
?>