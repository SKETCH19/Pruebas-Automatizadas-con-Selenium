<?php
class Database {
    private $db_file = __DIR__ . "/scm_ferreteria.db";
    public $conn;

    public function getConnection() {
        $this->conn = null;
        try {
            // Usar SQLite en lugar de MySQL
            $this->conn = new PDO("sqlite:" . $this->db_file);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // Crear tabla si no existe
            $this->createTables();
        } catch(PDOException $exception) {
            echo "Error de conexión: " . $exception->getMessage();
        }
        return $this->conn;
    }
    
    private function createTables() {
        // Tabla de productos
        $sql = "CREATE TABLE IF NOT EXISTS productos (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            codigo VARCHAR(50) UNIQUE NOT NULL,
            nombre VARCHAR(100) NOT NULL,
            descripcion TEXT,
            precio DECIMAL(10,2) NOT NULL,
            stock INTEGER NOT NULL DEFAULT 0,
            categoria VARCHAR(50),
            proveedor VARCHAR(100),
            ubicacion VARCHAR(100),
            fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";
        $this->conn->exec($sql);
        
        // Tabla de clientes
        $sql = "CREATE TABLE IF NOT EXISTS clientes (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            codigo VARCHAR(50) UNIQUE NOT NULL,
            nombre VARCHAR(100) NOT NULL,
            telefono VARCHAR(20),
            email VARCHAR(100),
            direccion TEXT,
            ciudad VARCHAR(100),
            fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";
        $this->conn->exec($sql);
        
        // Tabla de proveedores
        $sql = "CREATE TABLE IF NOT EXISTS proveedores (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            codigo VARCHAR(50) UNIQUE NOT NULL,
            nombre VARCHAR(100) NOT NULL,
            contacto VARCHAR(100),
            telefono VARCHAR(20),
            email VARCHAR(100),
            direccion TEXT,
            fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";
        $this->conn->exec($sql);
        
        // Tabla de ventas
        $sql = "CREATE TABLE IF NOT EXISTS ventas (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            numero_factura VARCHAR(50) UNIQUE NOT NULL,
            cliente_id INTEGER,
            fecha_venta TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            subtotal DECIMAL(10,2) NOT NULL,
            impuesto DECIMAL(10,2) DEFAULT 0,
            total DECIMAL(10,2) NOT NULL,
            metodo_pago VARCHAR(50),
            estado VARCHAR(20) DEFAULT 'completada',
            FOREIGN KEY (cliente_id) REFERENCES clientes(id)
        )";
        $this->conn->exec($sql);
        
        // Tabla de detalle de ventas
        $sql = "CREATE TABLE IF NOT EXISTS ventas_detalle (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            venta_id INTEGER,
            producto_id INTEGER,
            cantidad INTEGER NOT NULL,
            precio_unitario DECIMAL(10,2) NOT NULL,
            subtotal DECIMAL(10,2) NOT NULL,
            FOREIGN KEY (venta_id) REFERENCES ventas(id),
            FOREIGN KEY (producto_id) REFERENCES productos(id)
        )";
        $this->conn->exec($sql);
        
        // Tabla de compras
        $sql = "CREATE TABLE IF NOT EXISTS compras (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            numero_orden VARCHAR(50) UNIQUE NOT NULL,
            proveedor_id INTEGER,
            fecha_compra TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            subtotal DECIMAL(10,2) NOT NULL,
            impuesto DECIMAL(10,2) DEFAULT 0,
            total DECIMAL(10,2) NOT NULL,
            estado VARCHAR(20) DEFAULT 'pendiente',
            FOREIGN KEY (proveedor_id) REFERENCES proveedores(id)
        )";
        $this->conn->exec($sql);
        
        // Tabla de detalle de compras
        $sql = "CREATE TABLE IF NOT EXISTS compras_detalle (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            compra_id INTEGER,
            producto_id INTEGER,
            cantidad INTEGER NOT NULL,
            precio_unitario DECIMAL(10,2) NOT NULL,
            subtotal DECIMAL(10,2) NOT NULL,
            FOREIGN KEY (compra_id) REFERENCES compras(id),
            FOREIGN KEY (producto_id) REFERENCES productos(id)
        )";
        $this->conn->exec($sql);
        
        // Insertar datos de ejemplo
        $this->insertSampleData();
    }
    
    private function insertSampleData() {
        // Productos
        $count = $this->conn->query("SELECT COUNT(*) FROM productos")->fetchColumn();
        if ($count == 0) {
            $insert = "INSERT INTO productos (codigo, nombre, descripcion, precio, stock, categoria, proveedor, ubicacion) VALUES
                ('HAM001', 'Martillo 16oz', 'Martillo de acero forjado', 450.00, 25, 'Herramientas', 'Herracom', 'Bodega A - Estante 1'),
                ('TOR025', 'Tornillos 2x100', 'Paquete tornillos madera 2 pulgadas', 120.00, 100, 'Fijaciones', 'Tornillos RD', 'Bodega B - Estante 3'),
                ('PIN500', 'Pintura Blanca 1L', 'Pintura latex interior/exterior', 680.00, 15, 'Pinturas', 'PinturasPremium', 'Bodega C - Estante 2'),
                ('TAL001', 'Taladro Eléctrico', 'Taladro 500W con accesorios', 2500.00, 8, 'Herramientas', 'PowerTools', 'Bodega A - Estante 2'),
                ('CEM100', 'Cemento 50kg', 'Cemento gris uso general', 350.00, 50, 'Construcción', 'CementosPremier', 'Bodega D - Piso')";
            $this->conn->exec($insert);
        }
        
        // Clientes
        $count = $this->conn->query("SELECT COUNT(*) FROM clientes")->fetchColumn();
        if ($count == 0) {
            $insert = "INSERT INTO clientes (codigo, nombre, telefono, email, direccion, ciudad) VALUES
                ('CLI001', 'Constructora El Sol SAC', '555-1234', 'contacto@elsol.com', 'Av. Principal 123', 'Lima'),
                ('CLI002', 'Juan Pérez García', '555-5678', 'juan.perez@email.com', 'Calle Los Alamos 456', 'Arequipa'),
                ('CLI003', 'Arquitectos Unidos', '555-9012', 'info@arqunidos.com', 'Jr. Comercio 789', 'Trujillo'),
                ('CLI004', 'María Torres López', '555-3456', 'maria.torres@email.com', 'Av. Industrial 321', 'Lima')";
            $this->conn->exec($insert);
        }
        
        // Proveedores
        $count = $this->conn->query("SELECT COUNT(*) FROM proveedores")->fetchColumn();
        if ($count == 0) {
            $insert = "INSERT INTO proveedores (codigo, nombre, contacto, telefono, email, direccion) VALUES
                ('PROV001', 'Herracom SAC', 'Carlos Ruiz', '555-1111', 'ventas@herracom.com', 'Av. Industrial 100'),
                ('PROV002', 'Tornillos RD', 'Ana García', '555-2222', 'info@tornillosrd.com', 'Jr. Ferretero 200'),
                ('PROV003', 'PinturasPremium', 'Luis Martínez', '555-3333', 'contacto@pinturaspremium.com', 'Calle Pintores 300'),
                ('PROV004', 'PowerTools', 'Roberto Díaz', '555-4444', 'ventas@powertools.com', 'Av. Maquinaria 400')";
            $this->conn->exec($insert);
        }
        
        // Ventas de ejemplo
        $count = $this->conn->query("SELECT COUNT(*) FROM ventas")->fetchColumn();
        if ($count == 0) {
            $insert = "INSERT INTO ventas (numero_factura, cliente_id, subtotal, impuesto, total, metodo_pago, estado) VALUES
                ('F001-2026', 1, 5400.00, 972.00, 6372.00, 'Transferencia', 'completada'),
                ('F002-2026', 2, 1350.00, 243.00, 1593.00, 'Efectivo', 'completada'),
                ('F003-2026', 3, 10200.00, 1836.00, 12036.00, 'Crédito', 'completada')";
            $this->conn->exec($insert);
            
            // Detalles de ventas
            $insert = "INSERT INTO ventas_detalle (venta_id, producto_id, cantidad, precio_unitario, subtotal) VALUES
                (1, 1, 12, 450.00, 5400.00),
                (2, 2, 10, 120.00, 1200.00),
                (2, 3, 1, 680.00, 680.00),
                (3, 4, 4, 2500.00, 10000.00),
                (3, 1, 1, 450.00, 450.00)";
            $this->conn->exec($insert);
        }
        
        // Compras de ejemplo
        $count = $this->conn->query ("SELECT COUNT(*) FROM compras")->fetchColumn();
        if ($count == 0) {
            $insert = "INSERT INTO compras (numero_orden, proveedor_id, subtotal, impuesto, total, estado) VALUES
                ('OC001-2026', 1, 13500.00, 2430.00, 15930.00, 'recibida'),
                ('OC002-2026', 2, 12000.00, 2160.00, 14160.00, 'recibida'),
                ('OC003-2026', 4, 20000.00, 3600.00, 23600.00, 'pendiente')";
            $this->conn->exec($insert);
            
            // Detalles de compras
            $insert = "INSERT INTO compras_detalle (compra_id, producto_id, cantidad, precio_unitario, subtotal) VALUES
                (1, 1, 30, 450.00, 13500.00),
                (2, 2, 100, 120.00, 12000.00),
                (3, 4, 8, 2500.00, 20000.00)";
            $this->conn->exec($insert);
        }
    }
}
?>