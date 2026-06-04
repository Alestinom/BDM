USE BDM;

-- Agregar columnas faltantes a Siniestros
ALTER TABLE Siniestros
ADD COLUMN IF NOT EXISTS otras_unidades TINYINT(1) NOT NULL DEFAULT 0,
ADD COLUMN IF NOT EXISTS activo TINYINT(1) NOT NULL DEFAULT 1;

-- Trigger: estado inicial al registrar siniestro
DROP TRIGGER IF EXISTS TR_SiniestroRegistrado;

DELIMITER $$

CREATE TRIGGER TR_SiniestroRegistrado
AFTER INSERT ON Siniestros
FOR EACH ROW
BEGIN
    INSERT INTO HistorialEstados(id_siniestro, id_usuario, estado, fecha_cambio, observaciones)
    VALUES (NEW.id_siniestro, NEW.id_ajustador, 'Registrado', NOW(), 'Siniestro registrado en el sistema');
END$$

DROP PROCEDURE IF EXISTS SP_Siniestros
DELIMITER $$
CREATE PROCEDURE SP_Siniestros(
    IN p_accion         VARCHAR(20),
    IN p_id_siniestro   INT,
    IN p_id_poliza      INT,
    IN p_id_unidad      INT,
    IN p_id_ajustador   INT,
    IN p_fecha          DATE,
    IN p_hora           TIME,
    IN p_ubicacion      VARCHAR(255),
    IN p_descripcion    TEXT,
    IN p_otras_unidades TINYINT
)
BEGIN
    CASE p_accion

        WHEN 'ALTA' THEN
            INSERT INTO Siniestros(
                id_poliza, id_unidad, id_ajustador,
                fecha, hora, ubicacion, descripcion, otras_unidades, activo
            ) VALUES (
                p_id_poliza, p_id_unidad, p_id_ajustador,
                p_fecha, p_hora, p_ubicacion, p_descripcion, p_otras_unidades, 1
            );
            SELECT 'OK'                               AS resultado,
                   'Siniestro registrado correctamente' AS mensaje,
                   LAST_INSERT_ID()                   AS id_siniestro;

        WHEN 'BAJA' THEN
            IF NOT EXISTS (SELECT 1 FROM Siniestros WHERE id_siniestro = p_id_siniestro) THEN
                SELECT 'ERROR' AS resultado, 'Siniestro no encontrado' AS mensaje, NULL AS id_siniestro;
            ELSE
                UPDATE Siniestros SET activo = 0 WHERE id_siniestro = p_id_siniestro;
                SELECT 'OK'                                  AS resultado,
                       'Siniestro dado de baja correctamente' AS mensaje,
                       p_id_siniestro                        AS id_siniestro;
            END IF;

        WHEN 'CONSULTAR' THEN
            SELECT
                s.id_siniestro, s.id_poliza, s.id_unidad, s.id_ajustador,
                s.fecha, s.hora, s.ubicacion, s.descripcion, s.otras_unidades,
                pol.numero_poliza,
                un.marca, un.modelo, un.placas,
                CONCAT(ua.nombre,  ' ', ua.apellidos)  AS nombre_ajustador,
                CONCAT(uas.nombre, ' ', uas.apellidos) AS nombre_asegurado,
                (SELECT he.estado FROM HistorialEstados he
                 WHERE he.id_siniestro = s.id_siniestro
                 ORDER BY he.fecha_cambio DESC LIMIT 1) AS estado_actual
            FROM Siniestros s
            JOIN  Polizas  pol ON pol.id_poliza  = s.id_poliza
            JOIN  Unidades un  ON un.id_unidad   = s.id_unidad
            JOIN  Usuarios ua  ON ua.id_usuario  = s.id_ajustador
            LEFT JOIN Asegurados asg ON asg.id_asegurado = pol.id_asegurado
            LEFT JOIN Usuarios  uas ON uas.id_usuario    = asg.id_usuario
            WHERE s.id_siniestro = p_id_siniestro;

        WHEN 'LISTAR' THEN
            SELECT
                s.id_siniestro, s.id_poliza, s.id_unidad, s.id_ajustador,
                s.fecha, s.hora, s.ubicacion, s.descripcion, s.otras_unidades,
                pol.numero_poliza,
                un.marca, un.modelo, un.placas,
                CONCAT(ua.nombre,  ' ', ua.apellidos)  AS nombre_ajustador,
                CONCAT(uas.nombre, ' ', uas.apellidos) AS nombre_asegurado,
                (SELECT he.estado FROM HistorialEstados he
                 WHERE he.id_siniestro = s.id_siniestro
                 ORDER BY he.fecha_cambio DESC LIMIT 1) AS estado_actual
            FROM Siniestros s
            JOIN  Polizas  pol ON pol.id_poliza  = s.id_poliza
            JOIN  Unidades un  ON un.id_unidad   = s.id_unidad
            JOIN  Usuarios ua  ON ua.id_usuario  = s.id_ajustador
            LEFT JOIN Asegurados asg ON asg.id_asegurado = pol.id_asegurado
            LEFT JOIN Usuarios  uas ON uas.id_usuario    = asg.id_usuario
            WHERE s.activo = 1
            ORDER BY s.fecha DESC, s.hora DESC;

        WHEN 'LISTAR_POR_AJUSTADOR' THEN
            SELECT
                s.id_siniestro, s.id_poliza, s.id_unidad, s.id_ajustador,
                s.fecha, s.hora, s.ubicacion, s.descripcion, s.otras_unidades,
                pol.numero_poliza,
                un.marca, un.modelo, un.placas,
                CONCAT(ua.nombre,  ' ', ua.apellidos)  AS nombre_ajustador,
                CONCAT(uas.nombre, ' ', uas.apellidos) AS nombre_asegurado,
                (SELECT he.estado FROM HistorialEstados he
                 WHERE he.id_siniestro = s.id_siniestro
                 ORDER BY he.fecha_cambio DESC LIMIT 1) AS estado_actual
            FROM Siniestros s
            JOIN  Polizas  pol ON pol.id_poliza  = s.id_poliza
            JOIN  Unidades un  ON un.id_unidad   = s.id_unidad
            JOIN  Usuarios ua  ON ua.id_usuario  = s.id_ajustador
            LEFT JOIN Asegurados asg ON asg.id_asegurado = pol.id_asegurado
            LEFT JOIN Usuarios  uas ON uas.id_usuario    = asg.id_usuario
            WHERE s.id_ajustador = p_id_ajustador AND s.activo = 1
            ORDER BY s.fecha DESC, s.hora DESC;

        WHEN 'LISTAR_POR_ASEGURADO' THEN
            -- p_id_poliza se reutiliza para recibir el id_asegurado en esta acción
            SELECT
                s.id_siniestro, s.id_poliza, s.id_unidad, s.id_ajustador,
                s.fecha, s.hora, s.ubicacion, s.descripcion, s.otras_unidades,
                pol.numero_poliza,
                un.marca, un.modelo, un.placas,
                CONCAT(ua.nombre,  ' ', ua.apellidos)  AS nombre_ajustador,
                CONCAT(uas.nombre, ' ', uas.apellidos) AS nombre_asegurado,
                (SELECT he.estado FROM HistorialEstados he
                 WHERE he.id_siniestro = s.id_siniestro
                 ORDER BY he.fecha_cambio DESC LIMIT 1) AS estado_actual
            FROM Siniestros s
            JOIN  Polizas  pol ON pol.id_poliza  = s.id_poliza
            JOIN  Unidades un  ON un.id_unidad   = s.id_unidad
            JOIN  Usuarios ua  ON ua.id_usuario  = s.id_ajustador
            LEFT JOIN Asegurados asg ON asg.id_asegurado = pol.id_asegurado
            LEFT JOIN Usuarios  uas ON uas.id_usuario    = asg.id_usuario
            WHERE pol.id_asegurado = p_id_poliza AND s.activo = 1
            ORDER BY s.fecha DESC, s.hora DESC;

        ELSE
            SELECT 'ERROR' AS resultado, 'Acción no reconocida' AS mensaje, NULL AS id_siniestro;

    END CASE;
END$$

DELIMITER ;

ALTER TABLE Polizas 
ADD COLUMN id_unidad INT NULL,
ADD FOREIGN KEY (id_unidad) REFERENCES Unidades(id_unidad);

USE BDM;

SELECT id_poliza, numero_poliza, id_asegurado, id_unidad 
FROM Polizas WHERE activo = 1;

SELECT id_unidad, id_asegurado, marca, modelo, placas 
FROM Unidades WHERE activo = 1;