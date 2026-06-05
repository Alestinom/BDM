USE BDM;

SET FOREIGN_KEY_CHECKS = 0;
SET SQL_SAFE_UPDATES = 0;

select * from Multimedia;

TRUNCATE TABLE Comentarios;
TRUNCATE TABLE Multimedia;
TRUNCATE TABLE Aprobaciones;
TRUNCATE TABLE HistorialEstados;
TRUNCATE TABLE Siniestros;
TRUNCATE TABLE Unidades;
TRUNCATE TABLE Polizas;
TRUNCATE TABLE Asegurados;
TRUNCATE TABLE Companias;

-- Borrar solo usuarios que NO son Supervisor ni Ajustador
DELETE FROM Usuarios 
WHERE id_usuario IN (
    SELECT id FROM (
        SELECT id_usuario as id 
        FROM Usuarios 
        WHERE tipo_usuario = 'Asegurado'
    ) tmp
);

SET SQL_SAFE_UPDATES = 1;
SET FOREIGN_KEY_CHECKS = 1;

-- Verificar que quedaron solo Supervisores y Ajustadores
SELECT id_usuario, nombre, apellidos, tipo_usuario, activo 
FROM Usuarios;

DESCRIBE Polizas;

ALTER TABLE Aprobaciones
ADD COLUMN fecha_compromiso DATE NULL,
ADD COLUMN observaciones TEXT NULL;