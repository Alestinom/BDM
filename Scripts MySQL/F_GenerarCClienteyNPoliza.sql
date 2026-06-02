USE BDM;

-- Function 1: Generar número de cliente único
DROP FUNCTION IF EXISTS F_GenerarNumeroCliente;

DELIMITER $$

CREATE FUNCTION F_GenerarNumeroCliente()
RETURNS VARCHAR(20)
DETERMINISTIC
BEGIN
    DECLARE v_numero INT;
    DECLARE v_codigo VARCHAR(20);
    
    -- Obtener el siguiente número disponible
    SELECT IFNULL(MAX(CAST(SUBSTRING(numero_cliente, 5) AS UNSIGNED)), 0) + 1
    INTO v_numero
    FROM Asegurados
    WHERE numero_cliente LIKE 'CLI-%';
    
    -- Formatear con ceros a la izquierda
    SET v_codigo = CONCAT('CLI-', LPAD(v_numero, 4, '0'));
    
    -- Verificar que no exista (por si hay gaps)
    WHILE EXISTS (SELECT 1 FROM Asegurados WHERE numero_cliente = v_codigo) DO
        SET v_numero = v_numero + 1;
        SET v_codigo = CONCAT('CLI-', LPAD(v_numero, 4, '0'));
    END WHILE;
    
    RETURN v_codigo;
END$$

DELIMITER ;

-- Function 2: Generar número de póliza único por compañía
DROP FUNCTION IF EXISTS F_GenerarNumeroPoliza;

DELIMITER $$

CREATE FUNCTION F_GenerarNumeroPoliza(p_id_compania INT)
RETURNS VARCHAR(50)
DETERMINISTIC
BEGIN
    DECLARE v_numero INT;
    DECLARE v_prefijo VARCHAR(10);
    DECLARE v_codigo VARCHAR(50);
    
    -- Obtener prefijo de la compañía (primeras 3 letras)
    SELECT UPPER(SUBSTRING(nombre, 1, 3))
    INTO v_prefijo
    FROM Companias
    WHERE id_compania = p_id_compania;
    
    -- Obtener el siguiente número disponible para esa compañía
    SELECT IFNULL(MAX(CAST(SUBSTRING(numero_poliza, 
           LENGTH(v_prefijo) + 6) AS UNSIGNED)), 0) + 1
    INTO v_numero
    FROM Polizas
    WHERE id_compania = p_id_compania
    AND numero_poliza LIKE CONCAT('POL-', v_prefijo, '-%');
    
    -- Formatear
    SET v_codigo = CONCAT('POL-', v_prefijo, '-', LPAD(v_numero, 4, '0'));
    
    -- Verificar que no exista
    WHILE EXISTS (SELECT 1 FROM Polizas 
                  WHERE numero_poliza = v_codigo 
                  AND id_compania = p_id_compania) DO
        SET v_numero = v_numero + 1;
        SET v_codigo = CONCAT('POL-', v_prefijo, '-', LPAD(v_numero, 4, '0'));
    END WHILE;
    
    RETURN v_codigo;
END$$

DELIMITER ;
