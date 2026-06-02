USE BDM;

DROP PROCEDURE IF EXISTS SP_Companias;

DELIMITER $$

CREATE PROCEDURE SP_Companias(
    IN p_accion VARCHAR(20),
    IN p_id_compania INT,
    IN p_nombre VARCHAR(100),
    IN p_telefono VARCHAR(20),
    IN p_direccion VARCHAR(200)
)
BEGIN
    CASE p_accion

        -- ALTA
        WHEN 'ALTA' THEN
            IF EXISTS (SELECT 1 FROM Companias 
                      WHERE nombre = p_nombre AND activo = 1) THEN
                SELECT 'ERROR' AS resultado, 
                       'Ya existe una compañía con ese nombre' AS mensaje;
            ELSE
                INSERT INTO Companias(nombre, telefono, direccion, activo)
                VALUES (p_nombre, p_telefono, p_direccion, 1);
                SELECT 'OK' AS resultado, 
                       'Compañía registrada correctamente' AS mensaje;
            END IF;

        -- MODIFICAR
        WHEN 'MODIFICAR' THEN
            IF NOT EXISTS (SELECT 1 FROM Companias 
                          WHERE id_compania = p_id_compania) THEN
                SELECT 'ERROR' AS resultado, 
                       'Compañía no encontrada' AS mensaje;
            ELSE
                UPDATE Companias
                SET
                    nombre    = IFNULL(p_nombre, nombre),
                    telefono  = IFNULL(p_telefono, telefono),
                    direccion = IFNULL(p_direccion, direccion)
                WHERE id_compania = p_id_compania;
                SELECT 'OK' AS resultado, 
                       'Compañía actualizada correctamente' AS mensaje;
            END IF;

        -- BAJA lógica
        WHEN 'BAJA' THEN
            IF NOT EXISTS (SELECT 1 FROM Companias 
                          WHERE id_compania = p_id_compania) THEN
                SELECT 'ERROR' AS resultado, 
                       'Compañía no encontrada' AS mensaje;
            ELSE
                UPDATE Companias
                SET activo = 0
                WHERE id_compania = p_id_compania;
                SELECT 'OK' AS resultado, 
                       'Compañía dada de baja correctamente' AS mensaje;
            END IF;

        -- CONSULTAR
        WHEN 'CONSULTAR' THEN
            SELECT id_compania, nombre, telefono, direccion, activo
            FROM Companias
            WHERE id_compania = p_id_compania;

        -- LISTAR
        WHEN 'LISTAR' THEN
            SELECT id_compania, nombre, telefono, direccion, activo
            FROM Companias
            WHERE activo = 1
            ORDER BY nombre;

        ELSE
            SELECT 'ERROR' AS resultado, 
                   'Acción no reconocida' AS mensaje;

    END CASE;
END$$

DELIMITER ;