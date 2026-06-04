USE BDM;

DROP PROCEDURE IF EXISTS SP_Multimedia;

DELIMITER $$

CREATE PROCEDURE SP_Multimedia(
    IN p_accion         VARCHAR(20),
    IN p_id_multimedia  INT,
    IN p_id_siniestro   INT,
    IN p_id_usuario     INT,
    IN p_tipo           VARCHAR(50),
    IN p_archivo        LONGBLOB,
    IN p_nombre_archivo VARCHAR(255)
)
BEGIN
    CASE p_accion

        WHEN 'ALTA' THEN
            INSERT INTO Multimedia(id_siniestro, id_usuario, tipo, archivo, nombre_archivo, fecha_subida)
            VALUES (p_id_siniestro, p_id_usuario, p_tipo, p_archivo, p_nombre_archivo, NOW());
            SELECT 'OK'                              AS resultado,
                   'Archivo guardado correctamente'  AS mensaje,
                   LAST_INSERT_ID()                  AS id_multimedia;

        WHEN 'LISTAR_POR_SINIESTRO' THEN
            SELECT id_multimedia, tipo, nombre_archivo, fecha_subida
            FROM Multimedia
            WHERE id_siniestro = p_id_siniestro
            ORDER BY fecha_subida;

        ELSE
            SELECT 'ERROR' AS resultado, 'Acción no reconocida' AS mensaje, NULL AS id_multimedia;

    END CASE;
END$$

DELIMITER ;
