<?php
require_once __DIR__ . '/Conexion.php';

class MultimediaModel {

    private function ejecutarSP(
        $accion,
        $id_multimedia  = null,
        $id_siniestro   = null,
        $id_usuario     = null,
        $tipo           = null,
        $archivo        = null,
        $nombre_archivo = null
    ) {
        $conn = Conexion::getInstance()->getConexion();
        $sql  = "CALL SP_Multimedia(?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);

        if (!$stmt) {
            error_log("MultimediaModel::ejecutarSP prepare() falló [accion=$accion]: " . $conn->error);
            return ['ok' => false, 'rows' => []];
        }

        $stmt->bind_param(
            "sssssss",
            $accion, $id_multimedia, $id_siniestro, $id_usuario,
            $tipo, $archivo, $nombre_archivo
        );

        if (!$stmt->execute()) {
            error_log("MultimediaModel::ejecutarSP execute() falló [accion=$accion]: " . $stmt->error);
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

    public function alta($id_siniestro, $id_usuario, $tipo, $archivo, $nombre_archivo) {
        $n      = null;
        $result = $this->ejecutarSP('ALTA', $n, $id_siniestro, $id_usuario, $tipo, $archivo, $nombre_archivo);
        return (!$result['ok'] || empty($result['rows'])) ? null : $result['rows'][0];
    }

    public function listarPorSiniestro($id_siniestro) {
        $n      = null;
        $result = $this->ejecutarSP('LISTAR_POR_SINIESTRO', $n, $id_siniestro, $n, $n, $n, $n);
        return $result['ok'] ? $result['rows'] : [];
    }

    public function obtener($id_multimedia) {
        $n      = null;
        $result = $this->ejecutarSP('OBTENER', $id_multimedia, $n, $n, $n, $n, $n);
        return (!$result['ok'] || empty($result['rows'])) ? null : $result['rows'][0];
    }
}
