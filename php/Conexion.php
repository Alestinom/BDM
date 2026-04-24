<?php
class Conexion {
    private $host       = "localhost";
    private $usuario    = "root";
    private $password   = "";
    private $base_datos = "BDM";

    private static $instancia = null;
    private $conn;

    private function __construct() {
        $this->conn = new mysqli(
            $this->host,
            $this->usuario,
            $this->password,
            $this->base_datos
        );
        if ($this->conn->connect_error) {
            die(json_encode([
                "ok"      => false,
                "mensaje" => "Error de conexión: " . $this->conn->connect_error
            ]));
        }
    }

    public static function getInstance() {
        if (self::$instancia === null) {
            self::$instancia = new Conexion();
        }
        return self::$instancia;
    }

    public function getConexion() {
        return $this->conn;
    }
}
