<?php
require_once __DIR__ . '/Conexion.php';

class AprobacionModel {

    private function ejecutarSP(
        $accion,
        $id_aprobacion            = null,
        $id_siniestro             = null,
        $id_supervisor            = null,
        $tipo_resolucion          = null,
        $monto_pago               = null,
        $monto_deducible_aplicado = null,
        $fecha_resolucion         = null,
        $fecha_compromiso         = null,
        $observaciones            = null
    ) {
        $conn = Conexion::getInstance()->getConexion();
        $sql  = "CALL SP_Aprobaciones(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);

        if (!$stmt) {
            error_log("AprobacionModel::ejecutarSP prepare() falló [accion=$accion]: " . $conn->error);
            return ['ok' => false, 'rows' => []];
        }

        $stmt->bind_param(
            "ssssssssss",
            $accion, $id_aprobacion, $id_siniestro, $id_supervisor,
            $tipo_resolucion, $monto_pago, $monto_deducible_aplicado,
            $fecha_resolucion, $fecha_compromiso, $observaciones
        );

        if (!$stmt->execute()) {
            error_log("AprobacionModel::ejecutarSP execute() falló [accion=$accion]: " . $stmt->error);
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

    public function listarPendientes() {
        $n      = null;
        $result = $this->ejecutarSP('LISTAR_PENDIENTES', $n, $n, $n, $n, $n, $n, $n, $n, $n);
        return $result['ok'] ? $result['rows'] : [];
    }

    public function alta($id_siniestro, $id_supervisor, $tipo_resolucion,
                         $monto_pago, $monto_deducible_aplicado,
                         $fecha_resolucion, $fecha_compromiso, $observaciones) {
        $n      = null;
        $result = $this->ejecutarSP(
            'ALTA', $n, $id_siniestro, $id_supervisor,
            $tipo_resolucion, $monto_pago, $monto_deducible_aplicado,
            $fecha_resolucion, $fecha_compromiso, $observaciones
        );
        return (!$result['ok'] || empty($result['rows'])) ? null : $result['rows'][0];
    }

    public function consultar($id_siniestro) {
        $n      = null;
        $result = $this->ejecutarSP('CONSULTAR', $n, $id_siniestro, $n, $n, $n, $n, $n, $n, $n);
        return (!$result['ok'] || empty($result['rows'])) ? null : $result['rows'][0];
    }

    public function modificar($id_aprobacion, $tipo_resolucion, $monto_pago,
                               $monto_deducible_aplicado, $fecha_resolucion,
                               $fecha_compromiso, $observaciones) {
        $n      = null;
        $result = $this->ejecutarSP(
            'MODIFICAR', $id_aprobacion, $n, $n,
            $tipo_resolucion, $monto_pago, $monto_deducible_aplicado,
            $fecha_resolucion, $fecha_compromiso, $observaciones
        );
        return (!$result['ok'] || empty($result['rows'])) ? null : $result['rows'][0];
    }
}
