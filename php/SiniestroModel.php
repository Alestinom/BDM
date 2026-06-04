<?php
require_once __DIR__ . '/Conexion.php';

class SiniestroModel {

    private function ejecutarSP(
        $accion,
        $id_siniestro   = null,
        $id_poliza      = null,
        $id_unidad      = null,
        $id_ajustador   = null,
        $fecha          = null,
        $hora           = null,
        $ubicacion      = null,
        $descripcion    = null,
        $otras_unidades = null
    ) {
        $conn = Conexion::getInstance()->getConexion();
        $sql  = "CALL SP_Siniestros(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);

        if (!$stmt) {
            error_log("SiniestroModel::ejecutarSP prepare() falló [accion=$accion]: " . $conn->error);
            return ['ok' => false, 'rows' => []];
        }

        $stmt->bind_param(
            "ssssssssss",
            $accion, $id_siniestro, $id_poliza, $id_unidad, $id_ajustador,
            $fecha, $hora, $ubicacion, $descripcion, $otras_unidades
        );

        if (!$stmt->execute()) {
            error_log("SiniestroModel::ejecutarSP execute() falló [accion=$accion]: " . $stmt->error);
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

    public function alta($id_poliza, $id_unidad, $id_ajustador, $fecha, $hora,
                         $ubicacion, $descripcion, $otras_unidades) {
        $n      = null;
        $result = $this->ejecutarSP(
            'ALTA', $n, $id_poliza, $id_unidad, $id_ajustador,
            $fecha, $hora, $ubicacion, $descripcion, $otras_unidades
        );
        return (!$result['ok'] || empty($result['rows'])) ? null : $result['rows'][0];
    }

    public function baja($id_siniestro) {
        $n      = null;
        $result = $this->ejecutarSP('BAJA', $id_siniestro, $n, $n, $n, $n, $n, $n, $n, $n);
        return (!$result['ok'] || empty($result['rows'])) ? null : $result['rows'][0];
    }

    public function consultar($id_siniestro) {
        $n      = null;
        $result = $this->ejecutarSP('CONSULTAR', $id_siniestro, $n, $n, $n, $n, $n, $n, $n, $n);
        return (!$result['ok'] || empty($result['rows'])) ? null : $result['rows'][0];
    }

    public function listar() {
        $n      = null;
        $result = $this->ejecutarSP('LISTAR', $n, $n, $n, $n, $n, $n, $n, $n, $n);
        return $result['ok'] ? $result['rows'] : [];
    }

    public function listarPorAjustador($id_ajustador) {
        $n      = null;
        $result = $this->ejecutarSP('LISTAR_POR_AJUSTADOR', $n, $n, $n, $id_ajustador, $n, $n, $n, $n, $n);
        return $result['ok'] ? $result['rows'] : [];
    }

    public function listarPorAsegurado($id_asegurado) {
        $n      = null;
        // p_id_poliza (param 3) se reutiliza para pasar id_asegurado en esta acción
        $result = $this->ejecutarSP('LISTAR_POR_ASEGURADO', $n, $id_asegurado, $n, $n, $n, $n, $n, $n, $n);
        return $result['ok'] ? $result['rows'] : [];
    }
}
