-- Esto se tiene que ejecutar en MySQL via Laragon
CREATE DATABASE scm_ferreteria;
USE scm_ferreteria;

CREATE TABLE productos (
    id INT PRIMARY KEY AUTO_INCREMENT,
    codigo VARCHAR(50) UNIQUE NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    precio DECIMAL(10,2) NOT NULL,
    stock INT NOT NULL DEFAULT 0,
    categoria VARCHAR(50),
    proveedor VARCHAR(100),
    ubicacion VARCHAR(100),
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO productos (codigo, nombre, descripcion, precio, stock, categoria, proveedor, ubicacion) VALUES
('HAM001', 'Martillo 16oz', 'Martillo de acero forjado', 450.00, 25, 'Herramientas', 'Herracom', 'Bodega A - Estante 1'),
('TOR025', 'Tornillos 2x100', 'Paquete tornillos madera 2 pulgadas', 120.00, 100, 'Fijaciones', 'Tornillos RD', 'Bodega B - Estante 3'),
('PIN500', 'Pintura Blanca 1L', 'Pintura latex interior/exterior', 680.00, 15, 'Pinturas', 'PinturasPremium', 'Bodega C - Estante 2');