<?php
include '../database/database.php';
$database = new Database();
$db = $database->getConnection();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $db->beginTransaction();
        
        $numero_orden = $_POST['numero_orden'];
        $proveedor_id = $_POST['proveedor_id'];
        $estado = $_POST['estado'];
        $subtotal = 0;
        $productos = $_POST['productos'];
        $cantidades = $_POST['cantidades'];
        $precios = $_POST['precios'];
        
        // Calcular subtotal
        foreach($productos as $index => $producto_id) {
            if($producto_id && $cantidades[$index] > 0 && $precios[$index] > 0) {
                $subtotal += $precios[$index] * $cantidades[$index];
            }
        }
        
        $impuesto = $subtotal * 0.18; // 18% IGV
        $total = $subtotal + $impuesto;
        
        // Insertar compra
        $query = "INSERT INTO compras (numero_orden, proveedor_id, subtotal, impuesto, total, estado) 
                  VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $db->prepare($query);
        $stmt->execute([$numero_orden, $proveedor_id, $subtotal, $impuesto, $total, $estado]);
        $compra_id = $db->lastInsertId();
        
        // Insertar detalles y actualizar stock si está recibida
        foreach($productos as $index => $producto_id) {
            if($producto_id && $cantidades[$index] > 0 && $precios[$index] > 0) {
                $precio_unitario = $precios[$index];
                $cantidad = $cantidades[$index];
                $subtotal_linea = $precio_unitario * $cantidad;
                
                // Insertar detalle
                $query = "INSERT INTO compras_detalle (compra_id, producto_id, cantidad, precio_unitario, subtotal) 
                          VALUES (?, ?, ?, ?, ?)";
                $stmt = $db->prepare($query);
                $stmt->execute([$compra_id, $producto_id, $cantidad, $precio_unitario, $subtotal_linea]);
                
                // Si está recibida, actualizar stock
                if($estado == 'recibida') {
                    $query = "SELECT stock FROM productos WHERE id = ?";
                    $stmt = $db->prepare($query);
                    $stmt->execute([$producto_id]);
                    $producto = $stmt->fetch(PDO::FETCH_ASSOC);
                    
                    $nuevo_stock = $producto['stock'] + $cantidad;
                    $query = "UPDATE productos SET stock = ? WHERE id = ?";
                    $stmt = $db->prepare($query);
                    $stmt->execute([$nuevo_stock, $producto_id]);
                }
            }
        }
        
        $db->commit();
        header('Location: index.php?success=1');
        exit();
    } catch(Exception $e) {
        $db->rollBack();
        $error = "Error al registrar la compra: " . $e->getMessage();
    }
}

// Generar número de orden automático
$query = "SELECT numero_orden FROM compras ORDER BY id DESC LIMIT 1";
$stmt = $db->prepare($query);
$stmt->execute();
$ultima_orden = $stmt->fetch(PDO::FETCH_ASSOC);
if($ultima_orden) {
    $numero = intval(substr($ultima_orden['numero_orden'], 2, 3)) + 1;
    $numero_orden = 'OC' . str_pad($numero, 3, '0', STR_PAD_LEFT) . '-2026';
} else {
    $numero_orden = 'OC001-2026';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nueva Orden de Compra - SCM</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .product-line { margin: 10px 0; padding: 10px; background: #f8f9fa; border-radius: 5px; display: flex; gap: 10px; }
        .product-line select, .product-line input { flex: 1; padding: 8px; }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>📝 Nueva Orden de Compra</h1>
            <p>Registro de Orden de Compra a Proveedor</p>
        </header>

        <nav>
            <ul>
                <li><a href="../productos/index.php">Inicio</a></li>
                <li><a href="../productos/listar.php">Gestión de Productos</a></li>
                <li><a href="../ventas/index.php">Módulo Ventas</a></li>
                <li><a href="index.php">Módulo Compras</a></li>
                <li><a href="../reportes/index.php">Reportes</a></li>
            </ul>
        </nav>

        <main>
            <?php if(isset($error)) echo "<div class='alert alert-error'>$error</div>"; ?>
            
            <div class="form-container">
                <form method="POST" id="compraForm">
                    <div class="form-row">
                        <div class="form-group">
                            <label>Número de Orden</label>
                            <input type="text" name="numero_orden" value="<?php echo $numero_orden; ?>" required readonly>
                        </div>
                        
                        <div class="form-group">
                            <label>Proveedor</label>
                            <select name="proveedor_id" required>
                                <option value="">Seleccione un proveedor</option>
                                <?php
                                $query = "SELECT * FROM proveedores ORDER BY nombre";
                                $stmt = $db->prepare($query);
                                $stmt->execute();
                                while($proveedor = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                    echo "<option value='{$proveedor['id']}'>{$proveedor['nombre']}</option>";
                                }
                                ?>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label>Estado</label>
                            <select name="estado" required>
                                <option value="pendiente">Pendiente</option>
                                <option value="recibida">Recibida</option>
                            </select>
                        </div>
                    </div>
                    
                    <h3>Productos de la Orden</h3>
                    <div id="productos-container">
                        <div class="product-line">
                            <select name="productos[]" class="producto-select">
                                <option value="">Seleccione un producto</option>
                                <?php
                                $query = "SELECT * FROM productos ORDER BY nombre";
                                $stmt = $db->prepare($query);
                                $stmt->execute();
                                while($producto = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                    echo "<option value='{$producto['id']}'>{$producto['nombre']}</option>";
                                }
                                ?>
                            </select>
                            <input type="number" name="cantidades[]" placeholder="Cantidad" min="1" value="1">
                            <input type="number" name="precios[]" placeholder="Precio Unitario" min="0" step="0.01" value="0">
                        </div>
                    </div>
                    
                    <button type="button" onclick="agregarProducto()" class="btn btn-info">➕ Agregar Producto</button>
                    
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">💾 Registrar Compra</button>
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
            newLine.querySelectorAll('select, input').forEach(el => el.value = el.type === 'number' ? (el.name.includes('cantidades') ? '1' : '0') : '');
            container.appendChild(newLine);
        }
    </script>
</body>
</html>
