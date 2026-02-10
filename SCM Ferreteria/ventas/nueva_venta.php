<?php
include '../database/database.php';
$database = new Database();
$db = $database->getConnection();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $db->beginTransaction();
        
        $numero_factura = $_POST['numero_factura'];
        $cliente_id = $_POST['cliente_id'];
        $metodo_pago = $_POST['metodo_pago'];
        $subtotal = 0;
        $productos = $_POST['productos'];
        $cantidades = $_POST['cantidades'];
        
        // Calcular subtotal
        foreach($productos as $index => $producto_id) {
            if($producto_id && $cantidades[$index] > 0) {
                $query = "SELECT precio FROM productos WHERE id = ?";
                $stmt = $db->prepare($query);
                $stmt->execute([$producto_id]);
                $producto = $stmt->fetch(PDO::FETCH_ASSOC);
                $subtotal += $producto['precio'] * $cantidades[$index];
            }
        }
        
        $impuesto = $subtotal * 0.18; // 18% IGV
        $total = $subtotal + $impuesto;
        
        // Insertar venta
        $query = "INSERT INTO ventas (numero_factura, cliente_id, subtotal, impuesto, total, metodo_pago, estado) 
                  VALUES (?, ?, ?, ?, ?, ?, 'completada')";
        $stmt = $db->prepare($query);
        $stmt->execute([$numero_factura, $cliente_id, $subtotal, $impuesto, $total, $metodo_pago]);
        $venta_id = $db->lastInsertId();
        
        // Insertar detalles y actualizar stock
        foreach($productos as $index => $producto_id) {
            if($producto_id && $cantidades[$index] > 0) {
                $query = "SELECT precio, stock FROM productos WHERE id = ?";
                $stmt = $db->prepare($query);
                $stmt->execute([$producto_id]);
                $producto = $stmt->fetch(PDO::FETCH_ASSOC);
                
                $precio_unitario = $producto['precio'];
                $cantidad = $cantidades[$index];
                $subtotal_linea = $precio_unitario * $cantidad;
                
                // Insertar detalle
                $query = "INSERT INTO ventas_detalle (venta_id, producto_id, cantidad, precio_unitario, subtotal) 
                          VALUES (?, ?, ?, ?, ?)";
                $stmt = $db->prepare($query);
                $stmt->execute([$venta_id, $producto_id, $cantidad, $precio_unitario, $subtotal_linea]);
                
                // Actualizar stock
                $nuevo_stock = $producto['stock'] - $cantidad;
                $query = "UPDATE productos SET stock = ? WHERE id = ?";
                $stmt = $db->prepare($query);
                $stmt->execute([$nuevo_stock, $producto_id]);
            }
        }
        
        $db->commit();
        header('Location: index.php?success=1');
        exit();
    } catch(Exception $e) {
        $db->rollBack();
        $error = "Error al registrar la venta: " . $e->getMessage();
    }
}

// Generar número de factura automático
$query = "SELECT numero_factura FROM ventas ORDER BY id DESC LIMIT 1";
$stmt = $db->prepare($query);
$stmt->execute();
$ultima_factura = $stmt->fetch(PDO::FETCH_ASSOC);
if($ultima_factura) {
    $numero = intval(substr($ultima_factura['numero_factura'], 1, 3)) + 1;
    $numero_factura = 'F' . str_pad($numero, 3, '0', STR_PAD_LEFT) . '-2026';
} else {
    $numero_factura = 'F001-2026';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nueva Venta - SCM</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .product-line { margin: 10px 0; padding: 10px; background: #f8f9fa; border-radius: 5px; }
        .product-line select, .product-line input { margin: 5px; padding: 8px; }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>🛒 Nueva Venta</h1>
            <p>Registro de Venta y Facturación</p>
        </header>

        <nav>
            <ul>
                <li><a href="../productos/index.php">Inicio</a></li>
                <li><a href="../productos/listar.php">Gestión de Productos</a></li>
                <li><a href="index.php">Módulo Ventas</a></li>
                <li><a href="../compras/index.php">Módulo Compras</a></li>
                <li><a href="../reportes/index.php">Reportes</a></li>
            </ul>
        </nav>

        <main>
            <?php if(isset($error)) echo "<div class='alert alert-error'>$error</div>"; ?>
            
            <div class="form-container">
                <form method="POST" id="ventaForm">
                    <div class="form-row">
                        <div class="form-group">
                            <label>Número de Factura</label>
                            <input type="text" name="numero_factura" value="<?php echo $numero_factura; ?>" required readonly>
                        </div>
                        
                        <div class="form-group">
                            <label>Cliente</label>
                            <select name="cliente_id" required>
                                <option value="">Seleccione un cliente</option>
                                <?php
                                $query = "SELECT * FROM clientes ORDER BY nombre";
                                $stmt = $db->prepare($query);
                                $stmt->execute();
                                while($cliente = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                    echo "<option value='{$cliente['id']}'>{$cliente['nombre']}</option>";
                                }
                                ?>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label>Método de Pago</label>
                            <select name="metodo_pago" required>
                                <option value="Efectivo">Efectivo</option>
                                <option value="Transferencia">Transferencia</option>
                                <option value="Tarjeta">Tarjeta</option>
                                <option value="Crédito">Crédito</option>
                            </select>
                        </div>
                    </div>
                    
                    <h3>Productos</h3>
                    <div id="productos-container">
                        <div class="product-line">
                            <select name="productos[]" class="producto-select">
                                <option value="">Seleccione un producto</option>
                                <?php
                                $query = "SELECT * FROM productos WHERE stock > 0 ORDER BY nombre";
                                $stmt = $db->prepare($query);
                                $stmt->execute();
                                while($producto = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                    echo "<option value='{$producto['id']}' data-precio='{$producto['precio']}' data-stock='{$producto['stock']}'>";
                                    echo "{$producto['nombre']} - Stock: {$producto['stock']} - $" . number_format($producto['precio'], 2);
                                    echo "</option>";
                                }
                                ?>
                            </select>
                            <input type="number" name="cantidades[]" placeholder="Cantidad" min="1" value="1">
                        </div>
                    </div>
                    
                    <button type="button" onclick="agregarProducto()" class="btn btn-info">➕ Agregar Producto</button>
                    
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">💾 Registrar Venta</button>
                        <a href="index.php" class="btn btn-secondary">❌ Cancelar</a>
                    </div>
                </form>
            </div>
        </main>
    </div>
    
    <script>
        function agregarProducto() {
            const container = document.getElementById('productos-container');
            const firstLine = container.querySelector('.product-line');
            const newLine = firstLine.cloneNode(true);
            newLine.querySelectorAll('select, input').forEach(el => el.value = '');
            container.appendChild(newLine);
        }
    </script>
</body>
</html>
