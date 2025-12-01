<?php
include '../database/database.php';
$database = new Database();
$db = $database->getConnection();

$id = $_GET['id'];
$query = "SELECT * FROM productos WHERE id = ?";
$stmt = $db->prepare($query);
$stmt->execute([$id]);
$producto = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$producto) {
    header("Location: listar.php");
    exit();
}

if ($_POST) {
    $codigo = $_POST['codigo'];
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $precio = $_POST['precio'];
    $stock = $_POST['stock'];
    $categoria = $_POST['categoria'];
    $proveedor = $_POST['proveedor'];
    $ubicacion = $_POST['ubicacion'];

    $query = "UPDATE productos SET codigo=?, nombre=?, descripcion=?, precio=?, stock=?, categoria=?, proveedor=?, ubicacion=? WHERE id=?";
    $stmt = $db->prepare($query);
    
    if ($stmt->execute([$codigo, $nombre, $descripcion, $precio, $stock, $categoria, $proveedor, $ubicacion, $id])) {
        header("Location: listar.php?success=1");
        exit();
    } else {
        $error = "Error al actualizar producto";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Producto</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>✏️ Editar Producto</h1>
            <a href="listar.php" class="btn btn-secondary">← Volver a Productos</a>
        </header>

        <?php if (isset($error)): ?>
            <div class="alert error"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST" class="product-form">
            <div class="form-group">
                <label for="codigo">Código del Producto:</label>
                <input type="text" id="codigo" name="codigo" value="<?php echo htmlspecialchars($producto['codigo']); ?>" required>
            </div>

            <div class="form-group">
                <label for="nombre">Nombre del Producto:</label>
                <input type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($producto['nombre']); ?>" required>
            </div>

            <div class="form-group">
                <label for="descripcion">Descripción:</label>
                <textarea id="descripcion" name="descripcion" rows="3"><?php echo htmlspecialchars($producto['descripcion']); ?></textarea>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="precio">Precio (RD$):</label>
                    <input type="number" id="precio" name="precio" step="0.01" value="<?php echo $producto['precio']; ?>" required>
                </div>

                <div class="form-group">
                    <label for="stock">Stock:</label>
                    <input type="number" id="stock" name="stock" value="<?php echo $producto['stock']; ?>" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="categoria">Categoría:</label>
                    <select id="categoria" name="categoria" required>
                        <option value="Herramientas" <?php echo $producto['categoria'] == 'Herramientas' ? 'selected' : ''; ?>>Herramientas</option>
                        <option value="Fijaciones" <?php echo $producto['categoria'] == 'Fijaciones' ? 'selected' : ''; ?>>Fijaciones</option>
                        <option value="Pinturas" <?php echo $producto['categoria'] == 'Pinturas' ? 'selected' : ''; ?>>Pinturas</option>
                        <option value="Materiales" <?php echo $producto['categoria'] == 'Materiales' ? 'selected' : ''; ?>>Materiales Construcción</option>
                        <option value="Eléctricos" <?php echo $producto['categoria'] == 'Eléctricos' ? 'selected' : ''; ?>>Materiales Eléctricos</option>
                        <option value="Plomería" <?php echo $producto['categoria'] == 'Plomería' ? 'selected' : ''; ?>>Plomería</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="proveedor">Proveedor:</label>
                    <input type="text" id="proveedor" name="proveedor" value="<?php echo htmlspecialchars($producto['proveedor']); ?>">
                </div>
            </div>

            <div class="form-group">
                <label for="ubicacion">Ubicación en Bodega:</label>
                <input type="text" id="ubicacion" name="ubicacion" value="<?php echo htmlspecialchars($producto['ubicacion']); ?>">
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">💾 Actualizar Producto</button>
                <a href="listar.php" class="btn btn-secondary">❌ Cancelar</a>
            </div>
        </form>
    </div>
</body>
</html>