USE BDM;

-- =========================
-- TABLA USUARIOS
-- =========================
ALTER TABLE Usuarios
CHANGE id_usuario id_usuario INT AUTO_INCREMENT
COMMENT 'Identificador unico del usuario';

ALTER TABLE Usuarios
CHANGE nombre nombre VARCHAR(100)
COMMENT 'Nombre del usuario';

ALTER TABLE Usuarios
CHANGE apellidos apellidos VARCHAR(100)
COMMENT 'Apellidos del usuario';

ALTER TABLE Usuarios
CHANGE fecha_nacimiento fecha_nacimiento DATE
COMMENT 'Fecha de nacimiento del usuario';

ALTER TABLE Usuarios
CHANGE genero genero VARCHAR(20)
COMMENT 'Genero del usuario';

ALTER TABLE Usuarios
CHANGE correo correo VARCHAR(100)
COMMENT 'Correo electronico del usuario';

ALTER TABLE Usuarios
CHANGE contrasena contrasena VARCHAR(100)
COMMENT 'Contrasena cifrada de acceso';

ALTER TABLE Usuarios
CHANGE alias alias VARCHAR(50)
COMMENT 'Nombre corto o alias del usuario';

ALTER TABLE Usuarios
CHANGE foto foto VARCHAR(255)
COMMENT 'Ruta o nombre del archivo de fotografia';

ALTER TABLE Usuarios
CHANGE tipo_usuario tipo_usuario VARCHAR(50)
COMMENT 'Rol del usuario: Ajustador, Supervisor o Asegurado';

ALTER TABLE Usuarios
CHANGE activo activo BOOLEAN
COMMENT 'Indica si el usuario esta activo';

-- =========================
-- TABLA ASEGURADOS
-- =========================
ALTER TABLE Asegurados
CHANGE id_asegurado id_asegurado INT AUTO_INCREMENT
COMMENT 'Identificador unico del asegurado';

ALTER TABLE Asegurados
CHANGE id_usuario id_usuario INT
COMMENT 'Referencia al usuario asegurado';

ALTER TABLE Asegurados
CHANGE numero_cliente numero_cliente VARCHAR(50)
COMMENT 'Numero de cliente asignado por la aseguradora';

ALTER TABLE Asegurados
CHANGE telefono telefono VARCHAR(20)
COMMENT 'Telefono del asegurado';

-- =========================
-- TABLA COMPANIAS
-- =========================
ALTER TABLE Companias
CHANGE id_compania id_compania INT AUTO_INCREMENT
COMMENT 'Identificador unico de la compania';

ALTER TABLE Companias
CHANGE nombre nombre VARCHAR(100)
COMMENT 'Nombre de la compania aseguradora';

ALTER TABLE Companias
CHANGE telefono telefono VARCHAR(20)
COMMENT 'Telefono de contacto de la compania';

ALTER TABLE Companias
CHANGE direccion direccion VARCHAR(200)
COMMENT 'Direccion fisica de la compania';

-- =========================
-- TABLA POLIZAS
-- =========================
ALTER TABLE Polizas
CHANGE id_poliza id_poliza INT AUTO_INCREMENT
COMMENT 'Identificador unico de la poliza';

ALTER TABLE Polizas
CHANGE id_asegurado id_asegurado INT
COMMENT 'Referencia al asegurado';

ALTER TABLE Polizas
CHANGE id_compania id_compania INT
COMMENT 'Referencia a la compania aseguradora';

ALTER TABLE Polizas
CHANGE numero_poliza numero_poliza VARCHAR(50)
COMMENT 'Numero unico de poliza';

ALTER TABLE Polizas
CHANGE fecha_inicio fecha_inicio DATE
COMMENT 'Fecha de inicio de vigencia';

ALTER TABLE Polizas
CHANGE fecha_vencimiento fecha_vencimiento DATE
COMMENT 'Fecha de vencimiento de la poliza';

ALTER TABLE Polizas
CHANGE tipo_cobertura tipo_cobertura VARCHAR(100)
COMMENT 'Tipo de cobertura contratada';

ALTER TABLE Polizas
CHANGE monto_deducible monto_deducible DECIMAL(10,2)
COMMENT 'Monto del deducible aplicable';

-- =========================
-- TABLA UNIDADES
-- =========================
ALTER TABLE Unidades
CHANGE id_unidad id_unidad INT AUTO_INCREMENT
COMMENT 'Identificador unico de la unidad';

ALTER TABLE Unidades
CHANGE id_asegurado id_asegurado INT
COMMENT 'Referencia al dueno asegurado';

ALTER TABLE Unidades
CHANGE marca marca VARCHAR(50)
COMMENT 'Marca del vehiculo';

ALTER TABLE Unidades
CHANGE modelo modelo VARCHAR(50)
COMMENT 'Modelo del vehiculo';

ALTER TABLE Unidades
CHANGE anio anio INT
COMMENT 'Anio del vehiculo';

ALTER TABLE Unidades
CHANGE placas placas VARCHAR(20)
COMMENT 'Numero de placas';

ALTER TABLE Unidades
CHANGE numero_serie numero_serie VARCHAR(50)
COMMENT 'Numero de serie o VIN';

ALTER TABLE Unidades
CHANGE color color VARCHAR(30)
COMMENT 'Color del vehiculo';

-- =========================
-- TABLA SINIESTROS
-- =========================
ALTER TABLE Siniestros
CHANGE id_siniestro id_siniestro INT AUTO_INCREMENT
COMMENT 'Identificador unico del siniestro';

ALTER TABLE Siniestros
CHANGE id_poliza id_poliza INT
COMMENT 'Referencia a la poliza';

ALTER TABLE Siniestros
CHANGE id_unidad id_unidad INT
COMMENT 'Referencia a la unidad afectada';

ALTER TABLE Siniestros
CHANGE id_ajustador id_ajustador INT
COMMENT 'Usuario ajustador responsable';

ALTER TABLE Siniestros
CHANGE fecha fecha DATE
COMMENT 'Fecha del siniestro';

ALTER TABLE Siniestros
CHANGE hora hora TIME
COMMENT 'Hora del siniestro';

ALTER TABLE Siniestros
CHANGE ubicacion ubicacion VARCHAR(200)
COMMENT 'Lugar donde ocurrio el siniestro';

ALTER TABLE Siniestros
CHANGE descripcion descripcion TEXT
COMMENT 'Descripcion de lo sucedido';

-- =========================
-- TABLA HISTORIALESTADOS
-- =========================
ALTER TABLE HistorialEstados
CHANGE id_historial id_historial INT AUTO_INCREMENT
COMMENT 'Identificador unico del historial';

ALTER TABLE HistorialEstados
CHANGE id_siniestro id_siniestro INT
COMMENT 'Referencia al siniestro';

ALTER TABLE HistorialEstados
CHANGE id_usuario id_usuario INT
COMMENT 'Usuario que realizo el cambio';

ALTER TABLE HistorialEstados
CHANGE estado estado VARCHAR(50)
COMMENT 'Estado asignado al siniestro';

ALTER TABLE HistorialEstados
CHANGE fecha_cambio fecha_cambio DATETIME
COMMENT 'Fecha y hora del cambio de estado';

ALTER TABLE HistorialEstados
CHANGE observaciones observaciones TEXT
COMMENT 'Comentarios sobre el cambio';

-- =========================
-- TABLA MULTIMEDIA
-- =========================
ALTER TABLE Multimedia
CHANGE id_multimedia id_multimedia INT AUTO_INCREMENT
COMMENT 'Identificador unico del archivo';

ALTER TABLE Multimedia
CHANGE id_siniestro id_siniestro INT
COMMENT 'Referencia al siniestro';

ALTER TABLE Multimedia
CHANGE id_usuario id_usuario INT
COMMENT 'Usuario que subio el archivo';

ALTER TABLE Multimedia
CHANGE tipo tipo VARCHAR(50)
COMMENT 'Tipo de archivo: imagen o video';

ALTER TABLE Multimedia
CHANGE archivo archivo VARCHAR(255)
COMMENT 'Ruta o nombre del archivo multimedia';

ALTER TABLE Multimedia
CHANGE fecha_subida fecha_subida DATETIME
COMMENT 'Fecha de carga del archivo';

-- =========================
-- TABLA APROBACIONES
-- =========================
ALTER TABLE Aprobaciones
CHANGE id_aprobacion id_aprobacion INT AUTO_INCREMENT
COMMENT 'Identificador unico de aprobacion';

ALTER TABLE Aprobaciones
CHANGE id_siniestro id_siniestro INT
COMMENT 'Referencia al siniestro';

ALTER TABLE Aprobaciones
CHANGE id_supervisor id_supervisor INT
COMMENT 'Supervisor que resuelve';

ALTER TABLE Aprobaciones
CHANGE tipo_resolucion tipo_resolucion VARCHAR(100)
COMMENT 'Resultado de la resolucion';

ALTER TABLE Aprobaciones
CHANGE monto_pago monto_pago DECIMAL(10,2)
COMMENT 'Monto autorizado de pago';

ALTER TABLE Aprobaciones
CHANGE monto_deducible_aplicado monto_deducible_aplicado DECIMAL(10,2)
COMMENT 'Deducible aplicado';

ALTER TABLE Aprobaciones
CHANGE fecha_resolucion fecha_resolucion DATE
COMMENT 'Fecha de resolucion';

-- =========================
-- TABLA COMENTARIOS
-- =========================
ALTER TABLE Comentarios
CHANGE id_comentario id_comentario INT AUTO_INCREMENT
COMMENT 'Identificador unico del comentario';

ALTER TABLE Comentarios
CHANGE id_siniestro id_siniestro INT
COMMENT 'Referencia al siniestro';

ALTER TABLE Comentarios
CHANGE id_usuario id_usuario INT
COMMENT 'Usuario que escribio el comentario';

ALTER TABLE Comentarios
CHANGE id_comentario_padre id_comentario_padre INT
COMMENT 'Comentario padre para respuestas';

ALTER TABLE Comentarios
CHANGE mensaje mensaje TEXT
COMMENT 'Contenido del comentario';

ALTER TABLE Comentarios
CHANGE fecha fecha DATETIME
COMMENT 'Fecha y hora del comentario';

-- Foto de perfil de usuario (BLOB obligatorio)
ALTER TABLE Usuarios
MODIFY COLUMN foto LONGBLOB
COMMENT 'Fotografia de perfil almacenada en formato binario';

-- Archivo multimedia del siniestro (BLOB obligatorio para imagenes)
ALTER TABLE Multimedia
MODIFY COLUMN archivo LONGBLOB
COMMENT 'Archivo multimedia almacenado en formato binario';

-- Nombre original del archivo
ALTER TABLE Multimedia
modify COLUMN nombre_archivo VARCHAR(255)
COMMENT 'Nombre original del archivo cargado';