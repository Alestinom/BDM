USE BDM;

DROP PROCEDURE IF EXISTS SP_Usuarios;

DELIMITER $$

CREATE PROCEDURE SP_Usuarios(
    IN p_accion          VARCHAR(20),
    IN p_id_usuario      INT,
    IN p_nombre          VARCHAR(100),
    IN p_apellidos       VARCHAR(100),
    IN p_fecha_nacimiento DATE,
    IN p_genero          VARCHAR(20),
    IN p_correo          VARCHAR(100),
    IN p_contrasena      VARCHAR(100),
    IN p_alias           VARCHAR(50),
    IN p_foto            LONGBLOB,
    IN p_tipo_usuario    VARCHAR(50),
    IN p_numero_cliente  VARCHAR(50),
    IN p_telefono        VARCHAR(20)
)
BEGIN
    DECLARE v_id_usuario  INT;
    DECLARE v_id_asegurado INT;

    CASE p_accion

        -- --------------------------------------------------------
        -- LOGIN: autenticar por correo + contraseña
        -- --------------------------------------------------------
        WHEN 'LOGIN' THEN
            SELECT
                u.id_usuario,
                u.nombre,
                u.apellidos,
                u.tipo_usuario,
                u.correo,
                u.alias
            FROM Usuarios u
            WHERE u.correo     = p_correo
              AND u.contrasena = p_contrasena
              AND u.activo     = 1;

        -- --------------------------------------------------------
        -- VERIFICAR: comprobar contraseña actual por id_usuario
        -- Devuelve una fila si coinciden, ninguna si no.
        -- --------------------------------------------------------
        WHEN 'VERIFICAR' THEN
            SELECT id_usuario
            FROM Usuarios
            WHERE id_usuario = p_id_usuario
              AND contrasena = p_contrasena;

        -- --------------------------------------------------------
        -- ALTA: registrar usuario nuevo
        -- --------------------------------------------------------
        WHEN 'ALTA' THEN
            IF EXISTS (SELECT 1 FROM Usuarios WHERE correo = p_correo) THEN
                SELECT 'ERROR' AS resultado,
                       'El correo ya está registrado' AS mensaje,
                       NULL AS id_usuario,
                       NULL AS id_asegurado;
            ELSEIF EXISTS (SELECT 1 FROM Usuarios WHERE alias = p_alias) THEN
                SELECT 'ERROR' AS resultado,
                       'El alias ya está en uso' AS mensaje,
                       NULL AS id_usuario,
                       NULL AS id_asegurado;
            ELSE
                INSERT INTO Usuarios(
                    nombre, apellidos, fecha_nacimiento, genero,
                    correo, contrasena, alias, foto, tipo_usuario, activo
                ) VALUES (
                    p_nombre, p_apellidos, p_fecha_nacimiento, p_genero,
                    p_correo, p_contrasena, p_alias, p_foto, p_tipo_usuario, 1
                );

                SET v_id_usuario = LAST_INSERT_ID();

                IF p_tipo_usuario = 'Asegurado' THEN
                    INSERT INTO Asegurados(id_usuario, numero_cliente, telefono)
                    VALUES (v_id_usuario, F_GenerarNumeroCliente(), p_telefono);

                    SET v_id_asegurado = LAST_INSERT_ID();
                END IF;

                SELECT 'OK' AS resultado,
                       'Usuario registrado correctamente' AS mensaje,
                       v_id_usuario  AS id_usuario,
                       v_id_asegurado AS id_asegurado;
            END IF;

        -- --------------------------------------------------------
        -- BAJA: desactivar usuario (baja lógica)
        -- --------------------------------------------------------
        WHEN 'BAJA' THEN
            IF NOT EXISTS (SELECT 1 FROM Usuarios WHERE id_usuario = p_id_usuario) THEN
                SELECT 'ERROR' AS resultado,
                       'Usuario no encontrado' AS mensaje,
                       NULL AS id_usuario,
                       NULL AS id_asegurado;
            ELSE
                UPDATE Usuarios SET activo = 0 WHERE id_usuario = p_id_usuario;
                SELECT 'OK' AS resultado,
                       'Usuario dado de baja correctamente' AS mensaje,
                       p_id_usuario AS id_usuario,
                       NULL AS id_asegurado;
            END IF;

        -- --------------------------------------------------------
        -- MODIFICAR: actualizar datos; NULL conserva el valor actual
        -- --------------------------------------------------------
        WHEN 'MODIFICAR' THEN
            IF NOT EXISTS (SELECT 1 FROM Usuarios WHERE id_usuario = p_id_usuario) THEN
                SELECT 'ERROR' AS resultado,
                       'Usuario no encontrado' AS mensaje,
                       NULL AS id_usuario,
                       NULL AS id_asegurado;
            ELSE
                UPDATE Usuarios
                SET
                    nombre           = IFNULL(p_nombre,           nombre),
                    apellidos        = IFNULL(p_apellidos,        apellidos),
                    fecha_nacimiento = IFNULL(p_fecha_nacimiento, fecha_nacimiento),
                    genero           = IFNULL(p_genero,           genero),
                    correo           = IFNULL(p_correo,           correo),
                    contrasena       = IFNULL(p_contrasena,       contrasena),
                    alias            = IFNULL(p_alias,            alias),
                    foto             = IFNULL(p_foto,             foto),
                    tipo_usuario     = IFNULL(p_tipo_usuario,     tipo_usuario)
                WHERE id_usuario = p_id_usuario;

                IF p_tipo_usuario = 'Asegurado' THEN
                    UPDATE Asegurados
                    SET telefono = IFNULL(p_telefono, telefono)
                    WHERE id_usuario = p_id_usuario;
                END IF;

                SELECT 'OK' AS resultado,
                       'Usuario modificado correctamente' AS mensaje,
                       p_id_usuario AS id_usuario,
                       NULL AS id_asegurado;
            END IF;

        -- --------------------------------------------------------
        -- CONSULTAR: datos completos de un usuario + Asegurado
        -- --------------------------------------------------------
        WHEN 'CONSULTAR' THEN
            SELECT
                u.id_usuario,
                u.nombre,
                u.apellidos,
                u.fecha_nacimiento,
                u.genero,
                u.correo,
                u.alias,
                u.foto,
                u.tipo_usuario,
                u.activo,
                a.id_asegurado,
                a.numero_cliente,
                a.telefono
            FROM Usuarios u
            LEFT JOIN Asegurados a ON a.id_usuario = u.id_usuario
            WHERE u.id_usuario = p_id_usuario;

        -- --------------------------------------------------------
        -- OBTENER_FOTO: devuelve solo el BLOB de foto
        -- --------------------------------------------------------
        WHEN 'OBTENER_FOTO' THEN
            SELECT foto
            FROM Usuarios
            WHERE id_usuario = p_id_usuario
              AND foto IS NOT NULL;

        -- --------------------------------------------------------
        -- LISTAR: todos los usuarios activos con datos de Asegurado
        -- --------------------------------------------------------
        WHEN 'LISTAR' THEN
            SELECT
                u.id_usuario,
                u.nombre,
                u.apellidos,
                u.correo,
                u.alias,
                u.tipo_usuario,
                u.activo,
                a.id_asegurado,
                a.numero_cliente,
                a.telefono
            FROM Usuarios u
            LEFT JOIN Asegurados a ON a.id_usuario = u.id_usuario
            WHERE u.activo = 1
            ORDER BY u.tipo_usuario, u.nombre;

        ELSE
            SELECT 'ERROR' AS resultado,
                   'Acción no reconocida' AS mensaje,
                   NULL AS id_usuario,
                   NULL AS id_asegurado;

    END CASE;
END$$

DELIMITER ;

-- ============================================================
-- Datos de prueba (ejecutar solo en base limpia)
-- ============================================================

-- Alta Supervisor
CALL SP_Usuarios('ALTA', NULL, 'Roberto', 'Soto', '1990-01-15',
'Masculino', 'supervisor@demo.com', 'Super1@', 'rsoto', NULL,
'Supervisor', NULL, NULL);

-- Alta Ajustador
CALL SP_Usuarios('ALTA', NULL, 'Luis', 'Garza', '1992-05-10',
'Masculino', 'ajustador@demo.com', 'Ajust1@', 'lgarza', NULL,
'Ajustador', NULL, NULL);

-- Alta Asegurado
CALL SP_Usuarios('ALTA', NULL, 'Juan', 'Perez', '1995-03-20',
'Masculino', 'asegurado@demo.com', 'Asegu1@', 'jperez', NULL,
'Asegurado', 'CLI-0001', '81 1234 5678');


SELECT id_multimedia, nombre_archivo, tipo, 
       LENGTH(archivo) as tamanio_bytes
FROM Multimedia
ORDER BY id_multimedia DESC;