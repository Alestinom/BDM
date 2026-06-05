<?php
require_once __DIR__ . '/Conexion.php';

class ComentarioModel {

    private function ejecutarSP(
        $accion,
        $id_comentario        = null,
        $id_siniestro         = null,
        $id_usuario           = null,
        $id_comentario_padre  = null,
        $mensaje              = null,
        $id_multimedia        = null
    ) {
        $conn = Conexion::getInstance()->getConexion();
        $sql  = "CALL SP_Comentarios(?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);

        if (!$stmt) {
            error_log("ComentarioModel::ejecutarSP prepare() falló [accion=$accion]: " . $conn->error);
            return ['ok' => false, 'rows' => []];
        }

        $stmt->bind_param(
            "sssssss",
            $accion, $id_comentario, $id_siniestro,
            $id_usuario, $id_comentario_padre, $mensaje, $id_multimedia
        );

        if (!$stmt->execute()) {
            error_log("ComentarioModel::ejecutarSP execute() falló [accion=$accion]: " . $stmt->error);
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

    public function listarPorSiniestro($id_siniestro) {
        $n      = null;
        $result = $this->ejecutarSP('LISTAR_POR_SINIESTRO', $n, $id_siniestro, $n, $n, $n);
        return $result['ok'] ? $result['rows'] : [];
    }

    public function alta($id_siniestro, $id_usuario, $id_comentario_padre, $mensaje, $id_multimedia = null) {
        $n      = null;
        $result = $this->ejecutarSP('ALTA', $n, $id_siniestro, $id_usuario, $id_comentario_padre, $mensaje, $id_multimedia);
        return (!$result['ok'] || empty($result['rows'])) ? null : $result['rows'][0];
    }

    public function eliminar($id_comentario) {
        $n      = null;
        $result = $this->ejecutarSP('ELIMINAR', $id_comentario, $n, $n, $n, $n);
        return (!$result['ok'] || empty($result['rows'])) ? null : $result['rows'][0];
    }
}
