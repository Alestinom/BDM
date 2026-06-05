USE BDM;

DROP PROCEDURE IF EXISTS SP_Comentarios;

DELIMITER $$

CREATE PROCEDURE SP_Comentarios(
    IN p_accion VARCHAR(20),
    IN p_id_comentario INT,
    IN p_id_siniestro INT,
    IN p_id_usuario INT,
    IN p_id_comentario_padre INT,
    IN p_mensaje TEXT
)
BEGIN
    CASE p_accion

        -- ALTA
        WHEN 'ALTA' THEN
            INSERT INTO Comentarios(
                id_siniestro, id_usuario, 
                id_comentario_padre, mensaje, fecha
            ) VALUES (
                p_id_siniestro, p_id_usuario,
                p_id_comentario_padre, p_mensaje, NOW()
            );
            SELECT 'OK' AS resultado,
                   'Comentario enviado correctamente' AS mensaje,
                   LAST_INSERT_ID() AS id_comentario;

        -- LISTAR POR SINIESTRO
        WHEN 'LISTAR_POR_SINIESTRO' THEN
            SELECT 
                co.id_comentario,
                co.id_siniestro,
                co.id_usuario,
                co.id_comentario_padre,
                co.mensaje,
                co.fecha,
                CONCAT(u.nombre, ' ', u.apellidos) AS nombre_usuario,
                u.tipo_usuario,
                u.alias
            FROM Comentarios co
            JOIN Usuarios u ON u.id_usuario = co.id_usuario
            WHERE co.id_siniestro = p_id_siniestro
            ORDER BY co.fecha ASC;

        -- ELIMINAR
        WHEN 'ELIMINAR' THEN
            IF NOT EXISTS (SELECT 1 FROM Comentarios 
                          WHERE id_comentario = p_id_comentario) THEN
                SELECT 'ERROR' AS resultado,
                       'Comentario no encontrado' AS mensaje,
                       NULL AS id_comentario;
            ELSE
                DELETE FROM Comentarios
                WHERE id_comentario = p_id_comentario;
                SELECT 'OK' AS resultado,
                       'Comentario eliminado' AS mensaje,
                       p_id_comentario AS id_comentario;
            END IF;

        ELSE
            SELECT 'ERROR' AS resultado,
                   'Acción no reconocida' AS mensaje,
                   NULL AS id_comentario;

    END CASE;
END$$

DELIMITER ;