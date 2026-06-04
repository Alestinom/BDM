USE BDM;

DROP PROCEDURE IF EXISTS SP_Aprobaciones;

DELIMITER $$

CREATE PROCEDURE SP_Aprobaciones(
    IN p_accion VARCHAR(20),
    IN p_id_aprobacion INT,
    IN p_id_siniestro INT,
    IN p_id_supervisor INT,
    IN p_tipo_resolucion VARCHAR(100),
    IN p_monto_pago DECIMAL(10,2),
    IN p_monto_deducible_aplicado DECIMAL(10,2),
    IN p_fecha_resolucion DATE,
    IN p_fecha_compromiso DATE,
    IN p_observaciones TEXT
)
BEGIN
    CASE p_accion

        -- ALTA
        WHEN 'ALTA' THEN
            IF EXISTS (SELECT 1 FROM Aprobaciones 
                      WHERE id_siniestro = p_id_siniestro) THEN
                SELECT 'ERROR' AS resultado,
                       'Este siniestro ya tiene una resolución' AS mensaje,
                       NULL AS id_aprobacion;
            ELSE
                INSERT INTO Aprobaciones(
                    id_siniestro, id_supervisor, tipo_resolucion,
                    monto_pago, monto_deducible_aplicado,
                    fecha_resolucion, fecha_compromiso, observaciones
                ) VALUES (
                    p_id_siniestro, p_id_supervisor, p_tipo_resolucion,
                    p_monto_pago, p_monto_deducible_aplicado,
                    p_fecha_resolucion, p_fecha_compromiso, p_observaciones
                );
                -- El Trigger TR_AprobacionSiniestro se dispara aquí
                -- automáticamente actualizando HistorialEstados
                SELECT 'OK' AS resultado,
                       'Resolución guardada correctamente' AS mensaje,
                       LAST_INSERT_ID() AS id_aprobacion;
            END IF;

        -- MODIFICAR
        WHEN 'MODIFICAR' THEN
            IF NOT EXISTS (SELECT 1 FROM Aprobaciones 
                          WHERE id_aprobacion = p_id_aprobacion) THEN
                SELECT 'ERROR' AS resultado,
                       'Aprobación no encontrada' AS mensaje,
                       NULL AS id_aprobacion;
            ELSE
                UPDATE Aprobaciones
                SET
                    tipo_resolucion          = IFNULL(p_tipo_resolucion, tipo_resolucion),
                    monto_pago               = IFNULL(p_monto_pago, monto_pago),
                    monto_deducible_aplicado = IFNULL(p_monto_deducible_aplicado, monto_deducible_aplicado),
                    fecha_resolucion         = IFNULL(p_fecha_resolucion, fecha_resolucion),
                    fecha_compromiso         = IFNULL(p_fecha_compromiso, fecha_compromiso),
                    observaciones            = IFNULL(p_observaciones, observaciones)
                WHERE id_aprobacion = p_id_aprobacion;
                SELECT 'OK' AS resultado,
                       'Resolución actualizada correctamente' AS mensaje,
                       p_id_aprobacion AS id_aprobacion;
            END IF;

        -- CONSULTAR
        WHEN 'CONSULTAR' THEN
            SELECT 
                ap.id_aprobacion,
                ap.id_siniestro,
                ap.id_supervisor,
                ap.tipo_resolucion,
                ap.monto_pago,
                ap.monto_deducible_aplicado,
                ap.fecha_resolucion,
                ap.fecha_compromiso,
                ap.observaciones,
                CONCAT(u.nombre, ' ', u.apellidos) AS nombre_supervisor
            FROM Aprobaciones ap
            JOIN Usuarios u ON u.id_usuario = ap.id_supervisor
            WHERE ap.id_siniestro = p_id_siniestro;

        -- LISTAR PENDIENTES (siniestros sin aprobación)
        WHEN 'LISTAR_PENDIENTES' THEN
            SELECT 
                s.id_siniestro,
                s.fecha,
                s.ubicacion,
                s.descripcion,
                CONCAT(u_aj.nombre, ' ', u_aj.apellidos) AS nombre_ajustador,
                CONCAT(u_as.nombre, ' ', u_as.apellidos) AS nombre_asegurado,
                un.marca, un.modelo, un.placas,
                p.numero_poliza,
                c.nombre AS nombre_compania
            FROM Siniestros s
            JOIN Usuarios u_aj ON u_aj.id_usuario = s.id_ajustador
            JOIN Polizas p ON p.id_poliza = s.id_poliza
            JOIN Companias c ON c.id_compania = p.id_compania
            JOIN Unidades un ON un.id_unidad = s.id_unidad
            JOIN Asegurados a ON a.id_asegurado = p.id_asegurado
            JOIN Usuarios u_as ON u_as.id_usuario = a.id_usuario
            WHERE s.activo = 1
            AND NOT EXISTS (
                SELECT 1 FROM Aprobaciones ap 
                WHERE ap.id_siniestro = s.id_siniestro
            )
            ORDER BY s.fecha DESC;

        ELSE
            SELECT 'ERROR' AS resultado,
                   'Acción no reconocida' AS mensaje,
                   NULL AS id_aprobacion;

    END CASE;
END$$

DELIMITER ;