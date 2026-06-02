<?php
require_once __DIR__ . '/Conexion.php';

class CompaniaModel {

    private function ejecutarSP(
        $accion,
        $id_compania = null,
        $nombre      = null,
        $telefono    = null,
        $direccion   = null
    ) {
        $conn = Conexion::getInstance()->getConexion();
        $sql  = "CALL SP_Companias(?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);

        if (!$stmt) {
            error_log("CompaniaModel::ejecutarSP prepare() falló [accion=$accion]: " . $conn->error);
            return ['ok' => false, 'rows' => []];
        }

        $stmt->bind_param("sssss", $accion, $id_compania, $nombre, $telefono, $direccion);

        if (!$stmt->execute()) {
            error_log("CompaniaModel::ejecutarSP execute() falló [accion=$accion]: " . $stmt->error);
            $stmt->close();
            return ['ok' => false, 'rows' => []];
        }

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

    public function listar() {
        $n      = null;
        $result = $this->ejecutarSP('LISTAR', $n, $n, $n, $n);
        return $result['ok'] ? $result['rows'] : [];
    }

    public function listarConEstado() {
        $n = null;
        return $this->ejecutarSP('LISTAR', $n, $n, $n, $n);
    }

    public function alta($nombre, $telefono, $direccion) {
        $n      = null;
        $result = $this->ejecutarSP('ALTA', $n, $nombre, $telefono, $direccion);
        return (!$result['ok'] || empty($result['rows'])) ? null : $result['rows'][0];
    }

    public function modificar($id_compania, $nombre, $telefono, $direccion) {
        $result = $this->ejecutarSP('MODIFICAR', $id_compania, $nombre, $telefono, $direccion);
        return (!$result['ok'] || empty($result['rows'])) ? null : $result['rows'][0];
    }

    public function baja($id_compania) {
        $n      = null;
        $result = $this->ejecutarSP('BAJA', $id_compania, $n, $n, $n);
        return (!$result['ok'] || empty($result['rows'])) ? null : $result['rows'][0];
    }

    public function consultar($id_compania) {
        $n      = null;
        $result = $this->ejecutarSP('CONSULTAR', $id_compania, $n, $n, $n);
        return (!$result['ok'] || empty($result['rows'])) ? null : $result['rows'][0];
    }
}
