<?php
require_once __DIR__ . '/Conexion.php';

class VehiculoModel {

    private function ejecutarSP(
        $accion,
        $id_unidad    = null,
        $id_asegurado = null,
        $marca        = null,
        $modelo       = null,
        $anio         = null,
        $placas       = null,
        $numero_serie = null,
        $color        = null
    ) {
        $conn = Conexion::getInstance()->getConexion();
        $sql  = "CALL SP_Unidades(?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);

        if (!$stmt) {
            error_log("VehiculoModel::ejecutarSP prepare() falló [accion=$accion]: " . $conn->error);
            return ['ok' => false, 'rows' => []];
        }

        $stmt->bind_param(
            "ssissssss",
            $accion, $id_unidad, $id_asegurado, $marca, $modelo,
            $anio, $placas, $numero_serie, $color
        );

        if (!$stmt->execute()) {
            error_log("VehiculoModel::ejecutarSP execute() falló [accion=$accion]: " . $stmt->error);
            $stmt->close();
            return ['ok' => false, 'rows' => []];
        }

        $result = $stmt->get_result();
        $rows   = [];
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $rows[] = $row;
            }
        }

        while ($conn->more_results() && $conn->next_result()) {}
        $stmt->close();

        return ['ok' => true, 'rows' => $rows];
    }

    public function alta($id_asegurado, $marca, $modelo, $anio, $placas, $numero_serie, $color) {
        $n      = null;
        $result = $this->ejecutarSP('ALTA', $n, $id_asegurado, $marca, $modelo, $anio, $placas, $numero_serie, $color);
        return (!$result['ok'] || empty($result['rows'])) ? null : $result['rows'][0];
    }

    public function modificar($id_unidad, $marca, $modelo, $anio, $placas, $numero_serie, $color) {
        $n      = null;
        $result = $this->ejecutarSP('MODIFICAR', $id_unidad, $n, $marca, $modelo, $anio, $placas, $numero_serie, $color);
        return (!$result['ok'] || empty($result['rows'])) ? null : $result['rows'][0];
    }

    public function baja($id_unidad) {
        $n      = null;
        $result = $this->ejecutarSP('BAJA', $id_unidad, $n, $n, $n, $n, $n, $n, $n);
        return (!$result['ok'] || empty($result['rows'])) ? null : $result['rows'][0];
    }

    public function listar() {
        $n      = null;
        $result = $this->ejecutarSP('LISTAR', $n, $n, $n, $n, $n, $n, $n, $n);
        return $result['ok'] ? $result['rows'] : [];
    }

    public function listarPorAsegurado($id_asegurado) {
        $n      = null;
        $result = $this->ejecutarSP('LISTAR_POR_ASEGURADO', $n, $id_asegurado, $n, $n, $n, $n, $n, $n);
        return $result['ok'] ? $result['rows'] : [];
    }
}
