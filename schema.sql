DROP TABLE IF EXISTS movimientos_inventario;
DROP TABLE IF EXISTS partidas;
DROP TABLE IF EXISTS notas;
DROP TABLE IF EXISTS micas;
DROP TABLE IF EXISTS graduaciones;
DROP TABLE IF EXISTS clientes;
DROP TABLE IF EXISTS marcas;
DROP TABLE IF EXISTS materiales;
DROP TABLE IF EXISTS tratamientos;

CREATE TABLE marcas (id_marca INT AUTO_INCREMENT PRIMARY KEY, nombre VARCHAR(50));
CREATE TABLE materiales (id_material INT AUTO_INCREMENT PRIMARY KEY, nombre VARCHAR(50));
CREATE TABLE tratamientos (id_tratamiento INT AUTO_INCREMENT PRIMARY KEY, nombre VARCHAR(50));
CREATE TABLE graduaciones (id_grad INT AUTO_INCREMENT PRIMARY KEY, esf DECIMAL(6,2), cil DECIMAL(5,2), adi DECIMAL(5,2));
CREATE TABLE clientes (id_cliente INT AUTO_INCREMENT PRIMARY KEY, nombre VARCHAR(100), telefono VARCHAR(20));

CREATE TABLE micas (
    id_mica INT AUTO_INCREMENT PRIMARY KEY,
    id_grad INT, id_marca INT, id_material INT, id_tratamiento INT,
    vision ENUM('Mono', 'Bifocal', 'Progresivo'),
    serie VARCHAR(50), ubicacion VARCHAR(50),
    stock_actual INT DEFAULT 0, stock_minimo INT DEFAULT 2,
    precio_costo DECIMAL(11,2), precio_venta DECIMAL(11,2),
    FOREIGN KEY (id_grad) REFERENCES graduaciones(id_grad)
);

CREATE TABLE notas (
    id_nota INT AUTO_INCREMENT PRIMARY KEY,
    fecha_apertura DATETIME,
    id_cliente INT,
    estatus ENUM('abierta', 'liquidada', 'cancelada'),
    FOREIGN KEY (id_cliente) REFERENCES clientes(id_cliente)
);

CREATE TABLE partidas (
    id_partida INT AUTO_INCREMENT PRIMARY KEY,
    id_nota INT,
    tipo_partida ENUM('mica', 'servicio'),
    id_mica INT NULL,
    cantidad INT,
    descripcion VARCHAR(100),
    FOREIGN KEY (id_nota) REFERENCES notas(id_nota)
);

CREATE TABLE movimientos_inventario (
    id_movimiento INT AUTO_INCREMENT PRIMARY KEY,
    id_mica INT,
    tipo_movimiento ENUM('entrada', 'salida_venta', 'ajuste_fisico'),
    cantidad INT,
    fecha DATETIME,
    comentarios VARCHAR(255),
    FOREIGN KEY (id_mica) REFERENCES micas(id_mica)
);