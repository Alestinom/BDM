USE BDM;

-- ============================================================
-- Prerequisito: estas columnas deben existir antes de crear el SP
-- (ejecutar solo si aún no existen)
-- ============================================================
ALTER TABLE Siniestros
    ADD COLUMN IF NOT EXISTS otras_unidades TINYINT(1) NOT NULL DEFAULT 0,
    ADD COLUMN IF NOT EXISTS activo         TINYINT(1) NOT NULL DEFAULT 1;

-- ============================================================
-- Trigger: estado inicial al registrar siniestro
-- (se crea aquí para que quede junto al SP)
-- ============================================================
DROP TRIGGER IF EXISTS TR_SiniestroRegistrado;

DELIMITER $$

CREATE TRIGGER TR_SiniestroRegistrado
AFTER INSERT ON Siniestros
FOR EACH ROW
BEGIN
    INSERT INTO HistorialEstados(id_siniestro, id_usuario, estado, fecha_cambio, observaciones)
    VALUES (NEW.id_siniestro, NEW.id_ajustador, 'Registrado', NOW(),
            'Siniestro registrado en el sistema');
END$$

DELIMITER ;

-- ============================================================
-- SP_Siniestros
-- ============================================================
DROP PROCEDURE IF EXISTS SP_Siniestros;

DELIMITER $$

CREATE PROCEDURE SP_Siniestros(
    IN p_accion         VARCHAR(30),
    IN p_id_siniestro   INT,
    IN p_id_poliza      INT,      -- se reutiliza como id_asegurado_usuario en LISTAR_POR_ASEGURADO
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

        -- --------------------------------------------------------
        -- ALTA: registrar nuevo siniestro
        -- El trigger TR_SiniestroRegistrado inserta el primer estado
        -- --------------------------------------------------------
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
            SELECT 'OK'                                AS resultado,
                   'Siniestro registrado correctamente' AS mensaje,
                   LAST_INSERT_ID()                    AS id_siniestro;

        -- --------------------------------------------------------
        -- BAJA: baja lógica del siniestro
        -- --------------------------------------------------------
        WHEN 'BAJA' THEN
            IF NOT EXISTS (SELECT 1 FROM Siniestros WHERE id_siniestro = p_id_siniestro) THEN
                SELECT 'ERROR' AS resultado,
                       'Siniestro no encontrado' AS mensaje,
                       NULL AS id_siniestro;
            ELSE
                UPDATE Siniestros SET activo = 0 WHERE id_siniestro = p_id_siniestro;
                SELECT 'OK'                                   AS resultado,
                       'Siniestro dado de baja correctamente' AS mensaje,
                       p_id_siniestro                         AS id_siniestro;
            END IF;

        -- --------------------------------------------------------
        -- CONSULTAR: datos completos de un siniestro (usa la vista)
        -- --------------------------------------------------------
        WHEN 'CONSULTAR' THEN
            SELECT *
            FROM V_SiniestrosCompletos
            WHERE id_siniestro = p_id_siniestro;

        -- --------------------------------------------------------
        -- LISTAR: todos los siniestros activos
        -- --------------------------------------------------------
        WHEN 'LISTAR' THEN
            SELECT *
            FROM V_SiniestrosCompletos
            ORDER BY fecha DESC, hora DESC;

        -- --------------------------------------------------------
        -- LISTAR_POR_AJUSTADOR: siniestros asignados a un ajustador
        -- --------------------------------------------------------
        WHEN 'LISTAR_POR_AJUSTADOR' THEN
            SELECT *
            FROM V_SiniestrosCompletos
            WHERE id_ajustador = p_id_ajustador
            ORDER BY fecha DESC, hora DESC;

        -- --------------------------------------------------------
        -- LISTAR_POR_ASEGURADO: siniestros de un asegurado
        -- NOTA: p_id_poliza lleva el id_usuario del asegurado
        -- --------------------------------------------------------
        WHEN 'LISTAR_POR_ASEGURADO' THEN
            SELECT *
            FROM V_SiniestrosCompletos
            WHERE id_asegurado_usuario = p_id_poliza
            ORDER BY fecha DESC, hora DESC;

        -- --------------------------------------------------------
        -- HISTORIAL: historial completo de estados de un siniestro
        -- Devuelve los mismos campos que V_HistorialSiniestro
        -- --------------------------------------------------------
        WHEN 'HISTORIAL' THEN
            SELECT
                he.id_historial,
                he.id_siniestro,
                he.estado,
                he.fecha_cambio,
                he.observaciones,
                CONCAT(u.nombre, ' ', u.apellidos) AS nombre_usuario,
                u.tipo_usuario
            FROM HistorialEstados he
            JOIN Usuarios u ON u.id_usuario = he.id_usuario
            WHERE he.id_siniestro = p_id_siniestro
            ORDER BY he.fecha_cambio ASC;

        -- --------------------------------------------------------
        -- ESTADISTICAS_GLOBALES: conteos para el dashboard (Supervisor)
        -- Equivale a SELECT * FROM V_EstadisticasDashboard
        -- --------------------------------------------------------
        WHEN 'ESTADISTICAS_GLOBALES' THEN
            SELECT
                COUNT(*)                                                           AS total_siniestros,
                SUM(CASE WHEN estado_actual = 'Registrado'     THEN 1 ELSE 0 END) AS registrados,
                SUM(CASE WHEN estado_actual = 'En revisión'    THEN 1 ELSE 0 END) AS en_revision,
                SUM(CASE WHEN estado_actual IN (
                    'Aceptado', 'Aceptado con deducible',
                    'Aceptado sin deducible', 'Aplica reparación'
                ) THEN 1 ELSE 0 END)                                               AS aprobados,
                SUM(CASE WHEN estado_actual = 'Rechazado'      THEN 1 ELSE 0 END) AS rechazados,
                SUM(CASE WHEN estado_actual = 'Pérdida total'  THEN 1 ELSE 0 END) AS perdida_total,
                SUM(CASE WHEN estado_actual = 'Cerrado'        THEN 1 ELSE 0 END) AS cerrados
            FROM V_EstadoActualSiniestro;

        -- --------------------------------------------------------
        -- ESTADISTICAS_AJUSTADOR: conteos filtrados por ajustador
        -- --------------------------------------------------------
        WHEN 'ESTADISTICAS_AJUSTADOR' THEN
            SELECT
                COUNT(*)                                                           AS total_siniestros,
                SUM(CASE WHEN estado_actual = 'Registrado'     THEN 1 ELSE 0 END) AS registrados,
                SUM(CASE WHEN estado_actual = 'En revisión'    THEN 1 ELSE 0 END) AS en_revision,
                SUM(CASE WHEN estado_actual IN (
                    'Aceptado', 'Aceptado con deducible',
                    'Aceptado sin deducible', 'Aplica reparación'
                ) THEN 1 ELSE 0 END)                                               AS aprobados,
                SUM(CASE WHEN estado_actual = 'Rechazado'      THEN 1 ELSE 0 END) AS rechazados,
                SUM(CASE WHEN estado_actual = 'Pérdida total'  THEN 1 ELSE 0 END) AS perdida_total,
                SUM(CASE WHEN estado_actual = 'Cerrado'        THEN 1 ELSE 0 END) AS cerrados
            FROM V_SiniestrosCompletos
            WHERE id_ajustador = p_id_ajustador;

        ELSE
            SELECT 'ERROR' AS resultado,
                   'Acción no reconocida' AS mensaje,
                   NULL AS id_siniestro;

    END CASE;
END$$

DELIMITER ;
