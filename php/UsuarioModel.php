<?php
require_once __DIR__ . '/Conexion.php';

class UsuarioModel {

    private function ejecutarSP(
        $accion,
        $id_usuario  = null,
        $nombre      = null,
        $apellidos   = null,
        $fecha_nac   = null,
        $genero      = null,
        $correo      = null,
        $contrasena  = null,
        $alias       = null,
        $foto        = null,
        $tipo_usuario = null,
        $num_cliente = null,
        $telefono    = null
    ) {
        $conn = Conexion::getInstance()->getConexion();
        $sql  = "CALL SP_Usuarios(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);

        if (!$stmt) {
            return ['ok' => false, 'rows' => []];
        }

        $stmt->bind_param(
            "sssssssssssss",
            $accion, $id_usuario, $nombre, $apellidos, $fecha_nac,
            $genero, $correo, $contrasena, $alias, $foto, $tipo_usuario,
            $num_cliente, $telefono
        );

        $stmt->execute();
        $result = $stmt->get_result();

        $rows = [];
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $rows[] = $row;
            }
        }

        while ($conn->more_results() && $conn->next_result()) {}
        $stmt->close();

        return ['ok' => true, 'rows' => $rows];
    }

    public function login($correo, $contrasena) {
        $n      = null;
        $result = $this->ejecutarSP('LOGIN', $n, $n, $n, $n, $n, $correo, $contrasena, $n, $n, $n, $n, $n);
        return (!$result['ok'] || empty($result['rows'])) ? null : $result['rows'][0];
    }

    public function alta($nombre, $apellidos, $fecha_nac, $genero, $correo,
                         $contrasena, $alias, $foto, $tipo_usuario,
                         $num_cliente, $telefono) {
        $result = $this->ejecutarSP(
            'ALTA', null,
            $nombre, $apellidos, $fecha_nac, $genero, $correo,
            $contrasena, $alias, $foto, $tipo_usuario, $num_cliente, $telefono
        );
        return (!$result['ok'] || empty($result['rows'])) ? null : $result['rows'][0];
    }

    public function baja($id_usuario) {
        $n      = null;
        $result = $this->ejecutarSP('BAJA', $id_usuario, $n, $n, $n, $n, $n, $n, $n, $n, $n, $n, $n);
        return (!$result['ok'] || empty($result['rows'])) ? null : $result['rows'][0];
    }

    public function modificar($id_usuario, $nombre, $apellidos, $fecha_nac, $genero,
                               $correo, $contrasena, $alias, $foto, $tipo_usuario,
                               $num_cliente, $telefono) {
        $result = $this->ejecutarSP(
            'MODIFICAR', $id_usuario,
            $nombre, $apellidos, $fecha_nac, $genero, $correo,
            $contrasena, $alias, $foto, $tipo_usuario, $num_cliente, $telefono
        );
        return (!$result['ok'] || empty($result['rows'])) ? null : $result['rows'][0];
    }

    public function consultar($id_usuario) {
        $n      = null;
        $result = $this->ejecutarSP('CONSULTAR', $id_usuario, $n, $n, $n, $n, $n, $n, $n, $n, $n, $n, $n);
        return (!$result['ok'] || empty($result['rows'])) ? null : $result['rows'][0];
    }

    public function listar() {
        $n      = null;
        $result = $this->ejecutarSP('LISTAR', $n, $n, $n, $n, $n, $n, $n, $n, $n, $n, $n, $n);
        return $result['ok'] ? $result['rows'] : [];
    }

    public function verificar($id_usuario, $contrasena) {
        $n      = null;
        $result = $this->ejecutarSP('VERIFICAR', $id_usuario, $n, $n, $n, $n, $n, $contrasena, $n, $n, $n, $n, $n);
        return (!$result['ok'] || empty($result['rows'])) ? null : $result['rows'][0];
    }

    public function obtenerFoto($id_usuario) {
        $n      = null;
        $result = $this->ejecutarSP('OBTENER_FOTO', $id_usuario, $n, $n, $n, $n, $n, $n, $n, $n, $n, $n, $n);
        return (!$result['ok'] || empty($result['rows'])) ? null : $result['rows'][0];
    }
}
