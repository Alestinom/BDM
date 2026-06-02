<?php
require_once __DIR__ . '/Conexion.php';

class PolizaModel {

    private function ejecutarSP(
        $accion,
        $id_poliza         = null,
        $id_asegurado      = null,
        $id_compania       = null,
        $numero_poliza     = null,
        $fecha_inicio      = null,
        $fecha_vencimiento = null,
        $tipo_cobertura    = null,
        $monto_deducible   = null
    ) {
        $conn = Conexion::getInstance()->getConexion();
        $sql  = "CALL SP_Polizas(?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);

        if (!$stmt) {
            error_log("PolizaModel::ejecutarSP prepare() falló [accion=$accion]: " . $conn->error);
            return ['ok' => false, 'rows' => []];
        }

        $stmt->bind_param(
            "sssssssss",
            $accion, $id_poliza, $id_asegurado, $id_compania,
            $numero_poliza, $fecha_inicio, $fecha_vencimiento,
            $tipo_cobertura, $monto_deducible
        );

        if (!$stmt->execute()) {
            error_log("PolizaModel::ejecutarSP execute() falló [accion=$accion]: " . $stmt->error);
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
        $result = $this->ejecutarSP('LISTAR', $n, $n, $n, $n, $n, $n, $n, $n);
        return $result['ok'] ? $result['rows'] : [];
    }

    public function listarPorCompania($id_compania) {
        $n      = null;
        $result = $this->ejecutarSP('LISTAR_POR_COMPANIA', $n, $n, $id_compania, $n, $n, $n, $n, $n);
        return $result['ok'] ? $result['rows'] : [];
    }

    public function listarPorAsegurado($id_asegurado) {
        $n      = null;
        $result = $this->ejecutarSP('LISTAR_POR_ASEGURADO', $n, $id_asegurado, $n, $n, $n, $n, $n, $n);
        return $result['ok'] ? $result['rows'] : [];
    }

    public function alta($id_asegurado, $id_compania, $numero_poliza, $fecha_inicio,
                         $fecha_vencimiento, $tipo_cobertura, $monto_deducible) {
        $n      = null;
        $result = $this->ejecutarSP(
            'ALTA', $n, $id_asegurado, $id_compania,
            $numero_poliza, $fecha_inicio, $fecha_vencimiento,
            $tipo_cobertura, $monto_deducible
        );
        return (!$result['ok'] || empty($result['rows'])) ? null : $result['rows'][0];
    }

    public function modificar($id_poliza, $id_asegurado, $id_compania, $numero_poliza,
                               $fecha_inicio, $fecha_vencimiento, $tipo_cobertura, $monto_deducible) {
        $result = $this->ejecutarSP(
            'MODIFICAR', $id_poliza, $id_asegurado, $id_compania,
            $numero_poliza, $fecha_inicio, $fecha_vencimiento,
            $tipo_cobertura, $monto_deducible
        );
        return (!$result['ok'] || empty($result['rows'])) ? null : $result['rows'][0];
    }

    public function baja($id_poliza) {
        $n      = null;
        $result = $this->ejecutarSP('BAJA', $id_poliza, $n, $n, $n, $n, $n, $n, $n);
        return (!$result['ok'] || empty($result['rows'])) ? null : $result['rows'][0];
    }

    public function consultar($id_poliza) {
        $n      = null;
        $result = $this->ejecutarSP('CONSULTAR', $id_poliza, $n, $n, $n, $n, $n, $n, $n);
        return (!$result['ok'] || empty($result['rows'])) ? null : $result['rows'][0];
    }
}
