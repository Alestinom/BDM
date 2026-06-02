USE BDM;

DROP PROCEDURE IF EXISTS SP_Polizas;

DELIMITER $$

CREATE PROCEDURE SP_Polizas(
    IN p_accion VARCHAR(20),
    IN p_id_poliza INT,
    IN p_id_asegurado INT,
    IN p_id_compania INT,
    IN p_numero_poliza VARCHAR(50),
    IN p_fecha_inicio DATE,
    IN p_fecha_vencimiento DATE,
    IN p_tipo_cobertura VARCHAR(100),
    IN p_monto_deducible DECIMAL(10,2)
)
BEGIN
    CASE p_accion

        WHEN 'ALTA' THEN
            IF EXISTS (SELECT 1 FROM Polizas 
                      WHERE numero_poliza = p_numero_poliza 
                      AND id_compania = p_id_compania
                      AND activo = 1) THEN
                SELECT 'ERROR' AS resultado, 
                       'Ya existe una póliza con ese número en esta compañía' AS mensaje;
            ELSE
                -- Cambiar el INSERT para usar la Function:
					INSERT INTO Polizas(
						id_asegurado, id_compania, numero_poliza,
						fecha_inicio, fecha_vencimiento, 
						tipo_cobertura, monto_deducible, activo
					) VALUES (
						p_id_asegurado, p_id_compania, 
						F_GenerarNumeroPoliza(p_id_compania), -- generado automáticamente
						p_fecha_inicio, p_fecha_vencimiento,
						p_tipo_cobertura, p_monto_deducible, 1
					);
                SELECT 'OK' AS resultado, 
                       'Póliza registrada correctamente' AS mensaje;
            END IF;

        WHEN 'MODIFICAR' THEN
            IF NOT EXISTS (SELECT 1 FROM Polizas 
                          WHERE id_poliza = p_id_poliza) THEN
                SELECT 'ERROR' AS resultado, 
                       'Póliza no encontrada' AS mensaje;
            ELSE
                UPDATE Polizas
                SET
                    id_asegurado      = IFNULL(p_id_asegurado, id_asegurado),
                    id_compania       = IFNULL(p_id_compania, id_compania),
                    numero_poliza     = IFNULL(p_numero_poliza, numero_poliza),
                    fecha_inicio      = IFNULL(p_fecha_inicio, fecha_inicio),
                    fecha_vencimiento = IFNULL(p_fecha_vencimiento, fecha_vencimiento),
                    tipo_cobertura    = IFNULL(p_tipo_cobertura, tipo_cobertura),
                    monto_deducible   = IFNULL(p_monto_deducible, monto_deducible)
                WHERE id_poliza = p_id_poliza;
                SELECT 'OK' AS resultado, 
                       'Póliza actualizada correctamente' AS mensaje;
            END IF;

        WHEN 'BAJA' THEN
            IF NOT EXISTS (SELECT 1 FROM Polizas 
                          WHERE id_poliza = p_id_poliza) THEN
                SELECT 'ERROR' AS resultado, 
                       'Póliza no encontrada' AS mensaje;
            ELSE
                UPDATE Polizas
                SET activo = 0
                WHERE id_poliza = p_id_poliza;
                SELECT 'OK' AS resultado, 
                       'Póliza dada de baja correctamente' AS mensaje;
            END IF;

        WHEN 'CONSULTAR' THEN
            SELECT 
                p.id_poliza, p.id_asegurado, p.id_compania,
                p.numero_poliza, p.fecha_inicio, p.fecha_vencimiento,
                p.tipo_cobertura, p.monto_deducible, p.activo,
                c.nombre AS nombre_compania,
                CONCAT(u.nombre, ' ', u.apellidos) AS nombre_asegurado
            FROM Polizas p
            JOIN Companias c ON c.id_compania = p.id_compania
            LEFT JOIN Asegurados a ON a.id_asegurado = p.id_asegurado
            LEFT JOIN Usuarios u ON u.id_usuario = a.id_usuario
            WHERE p.id_poliza = p_id_poliza;

        WHEN 'LISTAR' THEN
            SELECT 
                p.id_poliza, p.id_asegurado, p.id_compania,
                p.numero_poliza, p.fecha_inicio, p.fecha_vencimiento,
                p.tipo_cobertura, p.monto_deducible, p.activo,
                c.nombre AS nombre_compania,
                CONCAT(u.nombre, ' ', u.apellidos) AS nombre_asegurado
            FROM Polizas p
            JOIN Companias c ON c.id_compania = p.id_compania
            LEFT JOIN Asegurados a ON a.id_asegurado = p.id_asegurado
            LEFT JOIN Usuarios u ON u.id_usuario = a.id_usuario
            WHERE p.activo = 1
            ORDER BY c.nombre, p.numero_poliza;

        WHEN 'LISTAR_POR_COMPANIA' THEN
            SELECT 
                p.id_poliza, p.id_asegurado, p.id_compania,
                p.numero_poliza, p.fecha_inicio, p.fecha_vencimiento,
                p.tipo_cobertura, p.monto_deducible, p.activo,
                c.nombre AS nombre_compania,
                CONCAT(u.nombre, ' ', u.apellidos) AS nombre_asegurado
            FROM Polizas p
            JOIN Companias c ON c.id_compania = p.id_compania
            LEFT JOIN Asegurados a ON a.id_asegurado = p.id_asegurado
            LEFT JOIN Usuarios u ON u.id_usuario = a.id_usuario
            WHERE p.id_compania = p_id_compania
            AND p.activo = 1
            ORDER BY p.numero_poliza;

        WHEN 'LISTAR_POR_ASEGURADO' THEN
            SELECT 
                p.id_poliza, p.id_asegurado, p.id_compania,
                p.numero_poliza, p.fecha_inicio, p.fecha_vencimiento,
                p.tipo_cobertura, p.monto_deducible, p.activo,
                c.nombre AS nombre_compania,
                CONCAT(u.nombre, ' ', u.apellidos) AS nombre_asegurado
            FROM Polizas p
            JOIN Companias c ON c.id_compania = p.id_compania
            LEFT JOIN Asegurados a ON a.id_asegurado = p.id_asegurado
            LEFT JOIN Usuarios u ON u.id_usuario = a.id_usuario
            WHERE p.id_asegurado = p_id_asegurado
            AND p.activo = 1
            ORDER BY p.numero_poliza;

        ELSE
            SELECT 'ERROR' AS resultado, 
                   'Acción no reconocida' AS mensaje;

    END CASE;
END$$

DELIMITER ;