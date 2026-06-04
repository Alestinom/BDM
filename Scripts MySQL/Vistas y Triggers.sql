USE BDM;

-- =============================================
-- VISTAS
-- =============================================

-- Vista 1: Siniestros completos (Supervisor ve todos)
CREATE OR REPLACE VIEW V_SiniestrosCompletos AS
SELECT 
    s.id_siniestro,
    s.fecha,
    s.hora,
    s.ubicacion,
    s.descripcion,
    s.otras_unidades,
    s.activo,
    CONCAT(u_aj.nombre, ' ', u_aj.apellidos) AS nombre_ajustador,
    u_aj.id_usuario AS id_ajustador,
    CONCAT(u_as.nombre, ' ', u_as.apellidos) AS nombre_asegurado,
    u_as.id_usuario AS id_asegurado_usuario,
    un.id_unidad,
    un.marca,
    un.modelo,
    un.anio,
    un.placas,
    un.numero_serie,
    un.color,
    p.id_poliza,
    p.numero_poliza,
    p.tipo_cobertura,
    p.monto_deducible,
    c.id_compania,
    c.nombre AS nombre_compania,
    (SELECT he.estado 
     FROM HistorialEstados he 
     WHERE he.id_siniestro = s.id_siniestro 
     ORDER BY he.fecha_cambio DESC 
     LIMIT 1) AS estado_actual,
    (SELECT he.fecha_cambio 
     FROM HistorialEstados he 
     WHERE he.id_siniestro = s.id_siniestro 
     ORDER BY he.fecha_cambio DESC 
     LIMIT 1) AS fecha_ultimo_estado
FROM Siniestros s
JOIN Usuarios u_aj ON u_aj.id_usuario = s.id_ajustador
JOIN Polizas p ON p.id_poliza = s.id_poliza
JOIN Companias c ON c.id_compania = p.id_compania
JOIN Unidades un ON un.id_unidad = s.id_unidad
JOIN Asegurados a ON a.id_asegurado = p.id_asegurado
JOIN Usuarios u_as ON u_as.id_usuario = a.id_usuario
WHERE s.activo = 1;

-- Vista 2: Siniestros por Ajustador
CREATE OR REPLACE VIEW V_SiniestrosPorAjustador AS
SELECT * FROM V_SiniestrosCompletos;

-- Vista 3: Siniestros por Asegurado  
CREATE OR REPLACE VIEW V_SiniestrosPorAsegurado AS
SELECT * FROM V_SiniestrosCompletos;

-- Vista 4: Estado actual de cada siniestro
CREATE OR REPLACE VIEW V_EstadoActualSiniestro AS
SELECT 
    s.id_siniestro,
    he.estado,
    he.fecha_cambio,
    he.observaciones,
    CONCAT(u.nombre, ' ', u.apellidos) AS nombre_usuario,
    u.tipo_usuario
FROM Siniestros s
JOIN HistorialEstados he ON he.id_siniestro = s.id_siniestro
JOIN Usuarios u ON u.id_usuario = he.id_usuario
WHERE he.id_historial = (
    SELECT MAX(h2.id_historial)
    FROM HistorialEstados h2
    WHERE h2.id_siniestro = s.id_siniestro
);

-- Vista 5: Historial completo de un siniestro
CREATE OR REPLACE VIEW V_HistorialSiniestro AS
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
ORDER BY he.fecha_cambio ASC;

-- Vista 6: Multimedia por siniestro (sin BLOB)
CREATE OR REPLACE VIEW V_MultimediaSiniestro AS
SELECT 
    m.id_multimedia,
    m.id_siniestro,
    m.id_usuario,
    m.tipo,
    m.nombre_archivo,
    m.fecha_subida,
    CONCAT(u.nombre, ' ', u.apellidos) AS nombre_usuario
FROM Multimedia m
JOIN Usuarios u ON u.id_usuario = m.id_usuario;

-- Vista 7: Estadísticas del Dashboard
CREATE OR REPLACE VIEW V_EstadisticasDashboard AS
SELECT
    COUNT(*) AS total_siniestros,
    SUM(CASE WHEN estado_actual = 'Registrado' THEN 1 ELSE 0 END) AS registrados,
    SUM(CASE WHEN estado_actual = 'En revisión' THEN 1 ELSE 0 END) AS en_revision,
    SUM(CASE WHEN estado_actual IN ('Aceptado', 'Aceptado con deducible', 
        'Aceptado sin deducible', 'Aplica reparación') THEN 1 ELSE 0 END) AS aprobados,
    SUM(CASE WHEN estado_actual = 'Rechazado' THEN 1 ELSE 0 END) AS rechazados,
    SUM(CASE WHEN estado_actual = 'Pérdida total' THEN 1 ELSE 0 END) AS perdida_total,
    SUM(CASE WHEN estado_actual = 'Cerrado' THEN 1 ELSE 0 END) AS cerrados
FROM V_EstadoActualSiniestro;

-- Vista 8: Chat/Comentarios por siniestro
CREATE OR REPLACE VIEW V_ChatSiniestro AS
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
ORDER BY co.fecha ASC;

-- =============================================
-- TRIGGER 2: Al aprobar/rechazar siniestro
-- =============================================

DROP TRIGGER IF EXISTS TR_AprobacionSiniestro;

DELIMITER $$

CREATE TRIGGER TR_AprobacionSiniestro
AFTER INSERT ON Aprobaciones
FOR EACH ROW
BEGIN
    DECLARE v_estado VARCHAR(50);
    
    -- Determinar el estado según el tipo de resolución
    SET v_estado = CASE NEW.tipo_resolucion
        WHEN 'rechazado'      THEN 'Rechazado'
        WHEN 'aceptado'       THEN 'Aceptado'
        WHEN 'con_deducible'  THEN 'Aceptado con deducible'
        WHEN 'sin_deducible'  THEN 'Aceptado sin deducible'
        WHEN 'reparacion'     THEN 'Aplica reparación'
        WHEN 'perdida_total'  THEN 'Pérdida total'
        ELSE 'En revisión'
    END;
    
    INSERT INTO HistorialEstados(
        id_siniestro,
        id_usuario,
        estado,
        fecha_cambio,
        observaciones
    ) VALUES (
        NEW.id_siniestro,
        NEW.id_supervisor,
        v_estado,
        NOW(),
        CONCAT('Resolución: ', NEW.tipo_resolucion)
    );
END$$

DELIMITER ;


-- Primero recrear V_EstadoActualSiniestro con el alias correcto
CREATE OR REPLACE VIEW V_EstadoActualSiniestro AS
SELECT 
    s.id_siniestro,
    he.estado AS estado_actual,
    he.fecha_cambio,
    he.observaciones,
    CONCAT(u.nombre, ' ', u.apellidos) AS nombre_usuario,
    u.tipo_usuario
FROM Siniestros s
JOIN HistorialEstados he ON he.id_siniestro = s.id_siniestro
JOIN Usuarios u ON u.id_usuario = he.id_usuario
WHERE he.id_historial = (
    SELECT MAX(h2.id_historial)
    FROM HistorialEstados h2
    WHERE h2.id_siniestro = s.id_siniestro
);

-- Ahora recrear V_EstadisticasDashboard
CREATE OR REPLACE VIEW V_EstadisticasDashboard AS
SELECT
    COUNT(*) AS total_siniestros,
    SUM(CASE WHEN estado_actual = 'Registrado' THEN 1 ELSE 0 END) AS registrados,
    SUM(CASE WHEN estado_actual = 'En revisión' THEN 1 ELSE 0 END) AS en_revision,
    SUM(CASE WHEN estado_actual IN ('Aceptado', 'Aceptado con deducible', 
        'Aceptado sin deducible', 'Aplica reparación') THEN 1 ELSE 0 END) AS aprobados,
    SUM(CASE WHEN estado_actual = 'Rechazado' THEN 1 ELSE 0 END) AS rechazados,
    SUM(CASE WHEN estado_actual = 'Pérdida total' THEN 1 ELSE 0 END) AS perdida_total,
    SUM(CASE WHEN estado_actual = 'Cerrado' THEN 1 ELSE 0 END) AS cerrados
FROM V_EstadoActualSiniestro;



-- Trigger 1
DROP TRIGGER IF EXISTS TR_SiniestroRegistrado;

DELIMITER $$

CREATE TRIGGER TR_SiniestroRegistrado
AFTER INSERT ON Siniestros
FOR EACH ROW
BEGIN
    INSERT INTO HistorialEstados(id_siniestro, id_usuario, estado, fecha_cambio, observaciones)
    VALUES (NEW.id_siniestro, NEW.id_ajustador, 'Registrado', NOW(), 'Siniestro registrado en el sistema');
END$$

DELIMITER ;


USE BDM;

CREATE OR REPLACE VIEW V_ChatSiniestro AS
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
ORDER BY co.fecha ASC;


DROP TRIGGER IF EXISTS TR_AprobacionSiniestro;

DELIMITER $$

CREATE TRIGGER TR_AprobacionSiniestro
AFTER INSERT ON Aprobaciones
FOR EACH ROW
BEGIN
    DECLARE v_estado VARCHAR(50);
    
    SET v_estado = CASE NEW.tipo_resolucion
        WHEN 'rechazado'      THEN 'Rechazado'
        WHEN 'aceptado'       THEN 'Aceptado'
        WHEN 'con_deducible'  THEN 'Aceptado con deducible'
        WHEN 'sin_deducible'  THEN 'Aceptado sin deducible'
        WHEN 'reparacion'     THEN 'Aplica reparación'
        WHEN 'perdida_total'  THEN 'Pérdida total'
        ELSE 'En revisión'
    END;
    
    INSERT INTO HistorialEstados(
        id_siniestro, id_usuario, estado, fecha_cambio, observaciones
    ) VALUES (
        NEW.id_siniestro, NEW.id_supervisor, v_estado, NOW(),
        CONCAT('Resolución: ', NEW.tipo_resolucion)
    );
END$$

DELIMITER ;

USE BDM;

-- Ver todas las vistas
DESCRIBE Aprobaciones;

SHOW FULL TABLES WHERE Table_type = 'VIEW';

-- Ver los triggers
SHOW TRIGGERS FROM BDM;
