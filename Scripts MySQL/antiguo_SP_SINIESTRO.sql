USE BDM;

ALTER TABLE Siniestros
ADD COLUMN otras_unidades BOOLEAN DEFAULT 0,
ADD COLUMN activo BOOLEAN DEFAULT 1;

UPDATE Siniestros SET activo = 1 WHERE id_siniestro > 0;

USE BDM;

DROP PROCEDURE IF EXISTS SP_Siniestros;

DELIMITER $$

CREATE PROCEDURE SP_Siniestros(
    IN p_accion VARCHAR(20),
    IN p_id_siniestro INT,
    IN p_id_poliza INT,
    IN p_id_unidad INT,
    IN p_id_ajustador INT,
    IN p_fecha DATE,
    IN p_hora TIME,
    IN p_ubicacion VARCHAR(200),
    IN p_descripcion TEXT,
    IN p_otras_unidades BOOLEAN
)
BEGIN
    CASE p_accion

        -- ALTA
        WHEN 'ALTA' THEN
            INSERT INTO Siniestros(
                id_poliza, id_unidad, id_ajustador,
                fecha, hora, ubicacion, descripcion,
                otras_unidades, activo
            ) VALUES (
                p_id_poliza, p_id_unidad, p_id_ajustador,
                p_fecha, p_hora, p_ubicacion, p_descripcion,
                p_otras_unidades, 1
            );
            SELECT 'OK' AS resultado,
                   'Siniestro registrado correctamente' AS mensaje,
                   LAST_INSERT_ID() AS id_siniestro;

        -- BAJA lógica
        WHEN 'BAJA' THEN
            IF NOT EXISTS (SELECT 1 FROM Siniestros 
                          WHERE id_siniestro = p_id_siniestro) THEN
                SELECT 'ERROR' AS resultado,
                       'Siniestro no encontrado' AS mensaje,
                       NULL AS id_siniestro;
            ELSE
                UPDATE Siniestros
                SET activo = 0
                WHERE id_siniestro = p_id_siniestro;
                SELECT 'OK' AS resultado,
                       'Siniestro dado de baja correctamente' AS mensaje,
                       p_id_siniestro AS id_siniestro;
            END IF;

        -- CONSULTAR
        WHEN 'CONSULTAR' THEN
            SELECT 
                s.id_siniestro,
                s.id_poliza,
                s.id_unidad,
                s.id_ajustador,
                s.fecha,
                s.hora,
                s.ubicacion,
                s.descripcion,
                s.otras_unidades,
                s.activo,
                CONCAT(u_aj.nombre, ' ', u_aj.apellidos) AS nombre_ajustador,
                CONCAT(u_as.nombre, ' ', u_as.apellidos) AS nombre_asegurado,
                un.marca, un.modelo, un.anio, un.placas, un.numero_serie,
                p.numero_poliza,
                c.nombre AS nombre_compania
            FROM Siniestros s
            JOIN Usuarios u_aj ON u_aj.id_usuario = s.id_ajustador
            JOIN Polizas p ON p.id_poliza = s.id_poliza
            JOIN Companias c ON c.id_compania = p.id_compania
            JOIN Unidades un ON un.id_unidad = s.id_unidad
            JOIN Asegurados a ON a.id_asegurado = p.id_asegurado
            JOIN Usuarios u_as ON u_as.id_usuario = a.id_usuario
            WHERE s.id_siniestro = p_id_siniestro;

        -- LISTAR (Supervisor ve todos)
        WHEN 'LISTAR' THEN
            SELECT 
                s.id_siniestro,
                s.id_poliza,
                s.id_unidad,
                s.id_ajustador,
                s.fecha,
                s.hora,
                s.ubicacion,
                s.descripcion,
                s.otras_unidades,
                s.activo,
                CONCAT(u_aj.nombre, ' ', u_aj.apellidos) AS nombre_ajustador,
                CONCAT(u_as.nombre, ' ', u_as.apellidos) AS nombre_asegurado,
                un.marca, un.modelo, un.anio, un.placas,
                p.numero_poliza,
                c.nombre AS nombre_compania,
                he.estado AS estado_actual
            FROM Siniestros s
            JOIN Usuarios u_aj ON u_aj.id_usuario = s.id_ajustador
            JOIN Polizas p ON p.id_poliza = s.id_poliza
            JOIN Companias c ON c.id_compania = p.id_compania
            JOIN Unidades un ON un.id_unidad = s.id_unidad
            JOIN Asegurados a ON a.id_asegurado = p.id_asegurado
            JOIN Usuarios u_as ON u_as.id_usuario = a.id_usuario
            LEFT JOIN (
                SELECT id_siniestro, estado
                FROM HistorialEstados
                WHERE id_historial = (
                    SELECT MAX(id_historial) 
                    FROM HistorialEstados h2 
                    WHERE h2.id_siniestro = HistorialEstados.id_siniestro
                )
            ) he ON he.id_siniestro = s.id_siniestro
            WHERE s.activo = 1
            ORDER BY s.fecha DESC, s.hora DESC;

        -- LISTAR POR AJUSTADOR
        WHEN 'LISTAR_POR_AJUSTADOR' THEN
            SELECT 
                s.id_siniestro,
                s.id_poliza,
                s.id_unidad,
                s.id_ajustador,
                s.fecha,
                s.hora,
                s.ubicacion,
                s.descripcion,
                s.otras_unidades,
                s.activo,
                CONCAT(u_as.nombre, ' ', u_as.apellidos) AS nombre_asegurado,
                un.marca, un.modelo, un.anio, un.placas,
                p.numero_poliza,
                c.nombre AS nombre_compania,
                he.estado AS estado_actual
            FROM Siniestros s
            JOIN Polizas p ON p.id_poliza = s.id_poliza
            JOIN Companias c ON c.id_compania = p.id_compania
            JOIN Unidades un ON un.id_unidad = s.id_unidad
            JOIN Asegurados a ON a.id_asegurado = p.id_asegurado
            JOIN Usuarios u_as ON u_as.id_usuario = a.id_usuario
            LEFT JOIN (
                SELECT id_siniestro, estado
                FROM HistorialEstados
                WHERE id_historial = (
                    SELECT MAX(id_historial) 
                    FROM HistorialEstados h2 
                    WHERE h2.id_siniestro = HistorialEstados.id_siniestro
                )
            ) he ON he.id_siniestro = s.id_siniestro
            WHERE s.id_ajustador = p_id_ajustador
            AND s.activo = 1
            ORDER BY s.fecha DESC, s.hora DESC;

        -- LISTAR POR ASEGURADO
        WHEN 'LISTAR_POR_ASEGURADO' THEN
            SELECT 
                s.id_siniestro,
                s.id_poliza,
                s.id_unidad,
                s.id_ajustador,
                s.fecha,
                s.hora,
                s.ubicacion,
                s.descripcion,
                s.otras_unidades,
                s.activo,
                CONCAT(u_aj.nombre, ' ', u_aj.apellidos) AS nombre_ajustador,
                un.marca, un.modelo, un.anio, un.placas,
                p.numero_poliza,
                c.nombre AS nombre_compania,
                he.estado AS estado_actual
            FROM Siniestros s
            JOIN Usuarios u_aj ON u_aj.id_usuario = s.id_ajustador
            JOIN Polizas p ON p.id_poliza = s.id_poliza
            JOIN Companias c ON c.id_compania = p.id_compania
            JOIN Unidades un ON un.id_unidad = s.id_unidad
            JOIN Asegurados a ON a.id_asegurado = p.id_asegurado
            WHERE a.id_asegurado = p_id_ajustador
            AND s.activo = 1
            ORDER BY s.fecha DESC;

        ELSE
            SELECT 'ERROR' AS resultado,
                   'Acción no reconocida' AS mensaje,
                   NULL AS id_siniestro;

    END CASE;
END$$

DELIMITER ;

-- TRIGGER 1: Al insertar siniestro, registrar estado inicial
DROP TRIGGER IF EXISTS TR_SiniestroRegistrado;

DELIMITER $$

CREATE TRIGGER TR_SiniestroRegistrado
AFTER INSERT ON Siniestros
FOR EACH ROW
BEGIN
    INSERT INTO HistorialEstados(
        id_siniestro,
        id_usuario,
        estado,
        fecha_cambio,
        observaciones
    ) VALUES (
        NEW.id_siniestro,
        NEW.id_ajustador,
        'Registrado',
        NOW(),
        'Siniestro registrado en el sistema'
    );
END$$

DELIMITER ;


-- Verificar que el SP y Trigger existen
SHOW PROCEDURE STATUS WHERE Name = 'SP_Siniestros';
