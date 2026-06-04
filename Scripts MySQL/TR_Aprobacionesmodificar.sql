USE BDM;

DROP TRIGGER IF EXISTS TR_AprobacionModificada;

DELIMITER $$

CREATE TRIGGER TR_AprobacionModificada
AFTER UPDATE ON Aprobaciones
FOR EACH ROW
BEGIN
    DECLARE v_estado VARCHAR(50);
    
    SET v_estado = CASE NEW.tipo_resolucion
        WHEN 'rechazado'     THEN 'Rechazado'
        WHEN 'aceptado'      THEN 'Aceptado'
        WHEN 'con_deducible' THEN 'Aceptado con deducible'
        WHEN 'sin_deducible' THEN 'Aceptado sin deducible'
        WHEN 'reparacion'    THEN 'Aplica reparación'
        WHEN 'perdida_total' THEN 'Pérdida total'
        ELSE 'En revisión'
    END;
    
    INSERT INTO HistorialEstados(
        id_siniestro, id_usuario, estado, 
        fecha_cambio, observaciones
    ) VALUES (
        NEW.id_siniestro, NEW.id_supervisor, v_estado,
        NOW(), CONCAT('Resolución modificada: ', NEW.tipo_resolucion)
    );
END$$

DELIMITER ;