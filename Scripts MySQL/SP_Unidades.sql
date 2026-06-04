USE BDM;

-- Agregar columna activo a Unidades si no existe
ALTER TABLE Unidades ADD COLUMN IF NOT EXISTS activo TINYINT(1) NOT NULL DEFAULT 1;

DROP PROCEDURE IF EXISTS SP_Unidades;

DELIMITER $$

CREATE PROCEDURE SP_Unidades(
    IN p_accion VARCHAR(20),
    IN p_id_unidad INT,
    IN p_id_asegurado INT,
    IN p_marca VARCHAR(50),
    IN p_modelo VARCHAR(50),
    IN p_anio INT,
    IN p_placas VARCHAR(20),
    IN p_numero_serie VARCHAR(50),
    IN p_color VARCHAR(30)
)
BEGIN
    CASE p_accion

        WHEN 'ALTA' THEN
            IF EXISTS (SELECT 1 FROM Unidades WHERE placas = p_placas AND activo = 1) THEN
                SELECT 'ERROR' AS resultado,
                       'Ya existe un vehículo con esas placas' AS mensaje,
                       NULL AS id_unidad;
            ELSEIF EXISTS (SELECT 1 FROM Unidades WHERE numero_serie = p_numero_serie AND activo = 1) THEN
                SELECT 'ERROR' AS resultado,
                       'Ya existe un vehículo con ese número de serie' AS mensaje,
                       NULL AS id_unidad;
            ELSE
                INSERT INTO Unidades(id_asegurado, marca, modelo, anio, placas, numero_serie, color, activo)
                VALUES (p_id_asegurado, p_marca, p_modelo, p_anio, p_placas, p_numero_serie, p_color, 1);
                SELECT 'OK' AS resultado,
                       'Vehículo registrado correctamente' AS mensaje,
                       LAST_INSERT_ID() AS id_unidad;
            END IF;

        WHEN 'MODIFICAR' THEN
            IF NOT EXISTS (SELECT 1 FROM Unidades WHERE id_unidad = p_id_unidad) THEN
                SELECT 'ERROR' AS resultado, 'Vehículo no encontrado' AS mensaje;
            ELSE
                UPDATE Unidades
                SET
                    marca        = IFNULL(p_marca, marca),
                    modelo       = IFNULL(p_modelo, modelo),
                    anio         = IFNULL(p_anio, anio),
                    placas       = IFNULL(p_placas, placas),
                    numero_serie = IFNULL(p_numero_serie, numero_serie),
                    color        = IFNULL(p_color, color)
                WHERE id_unidad = p_id_unidad;
                SELECT 'OK' AS resultado, 'Vehículo actualizado correctamente' AS mensaje;
            END IF;

        WHEN 'BAJA' THEN
            IF NOT EXISTS (SELECT 1 FROM Unidades WHERE id_unidad = p_id_unidad) THEN
                SELECT 'ERROR' AS resultado, 'Vehículo no encontrado' AS mensaje;
            ELSE
                UPDATE Unidades SET activo = 0 WHERE id_unidad = p_id_unidad;
                SELECT 'OK' AS resultado, 'Vehículo dado de baja correctamente' AS mensaje;
            END IF;

        WHEN 'LISTAR' THEN
            SELECT
                u.id_unidad, u.id_asegurado, u.marca, u.modelo, u.anio,
                u.placas, u.numero_serie, u.color,
                CONCAT(us.nombre, ' ', us.apellidos) AS nombre_asegurado
            FROM Unidades u
            LEFT JOIN Asegurados a ON a.id_asegurado = u.id_asegurado
            LEFT JOIN Usuarios us ON us.id_usuario = a.id_usuario
            WHERE u.activo = 1
            ORDER BY u.id_unidad;

        WHEN 'LISTAR_POR_ASEGURADO' THEN
            SELECT id_unidad, id_asegurado, marca, modelo, anio, placas, numero_serie, color
            FROM Unidades
            WHERE id_asegurado = p_id_asegurado AND activo = 1
            ORDER BY id_unidad;

        ELSE
            SELECT 'ERROR' AS resultado, 'Acción no reconocida' AS mensaje;

    END CASE;
END$$

DELIMITER ;

CALL SP_Unidades(
    'ALTA',     -- accion
    NULL,       -- id_unidad
    5,          -- id_asegurado (usa uno que exista)
    'Toyota',   -- marca
    'Corolla',  -- modelo
    2023,       -- anio
    'ABC-123',  -- placas
    '12345678901234567', -- numero_serie (17 chars)
    'Blanco'    -- color
);