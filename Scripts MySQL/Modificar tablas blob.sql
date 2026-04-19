create database BDM;
use BDM;

-- Usuarios
CREATE TABLE Usuarios(
    id_usuario INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100),
    apellidos VARCHAR(100),
    fecha_nacimiento DATE,
    genero VARCHAR(20),
    correo VARCHAR(100),
    contrasena VARCHAR(100),
    alias VARCHAR(50),
    foto VARCHAR(255),
    tipo_usuario VARCHAR(50),
    activo BOOLEAN
);

select * FROM Usuarios;

-- Asegurados
CREATE TABLE Asegurados(
    id_asegurado INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT,
    numero_cliente VARCHAR(50),
    telefono VARCHAR(20),
    
    FOREIGN KEY(id_usuario) REFERENCES Usuarios(id_usuario)
);

-- Compañias
CREATE TABLE Companias(
    id_compania INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100),
    telefono VARCHAR(20),
    direccion VARCHAR(200)
);

-- Polizas
CREATE TABLE Polizas(
    id_poliza INT AUTO_INCREMENT PRIMARY KEY,
    id_asegurado INT,
    id_compania INT,
    numero_poliza VARCHAR(50),
    fecha_inicio DATE,
    fecha_vencimiento DATE,
    tipo_cobertura VARCHAR(100),
    monto_deducible DECIMAL(10,2),

    FOREIGN KEY(id_asegurado) REFERENCES Asegurados(id_asegurado),
    FOREIGN KEY(id_compania) REFERENCES Companias(id_compania)
);

-- Unidades
CREATE TABLE Unidades(
    id_unidad INT AUTO_INCREMENT PRIMARY KEY,
    id_asegurado INT,
    marca VARCHAR(50),
    modelo VARCHAR(50),
    anio INT,
    placas VARCHAR(20),
    numero_serie VARCHAR(50),
    color VARCHAR(30),

    FOREIGN KEY(id_asegurado) REFERENCES Asegurados(id_asegurado)
);

-- Siniestros
CREATE TABLE Siniestros(
    id_siniestro INT AUTO_INCREMENT PRIMARY KEY,
    id_poliza INT,
    id_unidad INT,
    id_ajustador INT,
    fecha DATE,
    hora TIME,
    ubicacion VARCHAR(200),
    descripcion TEXT,

    FOREIGN KEY(id_poliza) REFERENCES Polizas(id_poliza),
    FOREIGN KEY(id_unidad) REFERENCES Unidades(id_unidad),
    FOREIGN KEY(id_ajustador) REFERENCES Usuarios(id_usuario)
);

-- Historial Estados
CREATE TABLE HistorialEstados(
    id_historial INT AUTO_INCREMENT PRIMARY KEY,
    id_siniestro INT,
    id_usuario INT,
    estado VARCHAR(50),
    fecha_cambio DATETIME,
    observaciones TEXT,

    FOREIGN KEY(id_siniestro) REFERENCES Siniestros(id_siniestro),
    FOREIGN KEY(id_usuario) REFERENCES Usuarios(id_usuario)
);

-- Multimedia
CREATE TABLE Multimedia(
    id_multimedia INT AUTO_INCREMENT PRIMARY KEY,
    id_siniestro INT,
    id_usuario INT,
    tipo VARCHAR(50),
    archivo VARCHAR(255),
    fecha_subida DATETIME,

    FOREIGN KEY(id_siniestro) REFERENCES Siniestros(id_siniestro),
    FOREIGN KEY(id_usuario) REFERENCES Usuarios(id_usuario)
);

-- Aprobaciones
CREATE TABLE Aprobaciones(
    id_aprobacion INT AUTO_INCREMENT PRIMARY KEY,
    id_siniestro INT,
    id_supervisor INT,
    tipo_resolucion VARCHAR(100),
    monto_pago DECIMAL(10,2),
    monto_deducible_aplicado DECIMAL(10,2),
    fecha_resolucion DATE,

    FOREIGN KEY(id_siniestro) REFERENCES Siniestros(id_siniestro),
    FOREIGN KEY(id_supervisor) REFERENCES Usuarios(id_usuario)
);

-- Comentarios
CREATE TABLE Comentarios(
    id_comentario INT AUTO_INCREMENT PRIMARY KEY,
    id_siniestro INT,
    id_usuario INT,
    id_comentario_padre INT,
    mensaje TEXT,
    fecha DATETIME,

    FOREIGN KEY(id_siniestro) REFERENCES Siniestros(id_siniestro),
    FOREIGN KEY(id_usuario) REFERENCES Usuarios(id_usuario),
    FOREIGN KEY(id_comentario_padre) REFERENCES Comentarios(id_comentario)
);



-- Foto de perfil de usuario (BLOB obligatorio)
ALTER TABLE Usuarios 
MODIFY COLUMN foto LONGBLOB;

-- Archivo multimedia del siniestro (BLOB obligatorio para imágenes)
ALTER TABLE Multimedia 
MODIFY COLUMN archivo LONGBLOB;

-- Agregar columna para el nombre original del archivo
-- (útil para saber si es jpg, mp4, etc al recuperarlo)
ALTER TABLE Multimedia
ADD COLUMN nombre_archivo VARCHAR(255) AFTER tipo;
