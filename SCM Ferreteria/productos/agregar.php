<?php
include '../database/database.php';
$database = new Database();
$db = $database->getConnection();

if ($_POST) {
    $codigo = $_POST['codigo'];
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $precio = $_POST['precio'];
    $stock = $_POST['stock'];
    $categoria = $_POST['categoria'];
    $proveedor = $_POST['proveedor'];
    $ubicacion = $_POST['ubicacion'];

    $query = "INSERT INTO productos (codigo, nombre, descripcion, precio, stock, categoria, proveedor, ubicacion) 
              VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $db->prepare($query);
    
    if ($stmt->execute([$codigo, $nombre, $descripcion, $precio, $stock, $categoria, $proveedor, $ubicacion])) {
        header("Location: listar.php?success=1");
        exit();
    } else {
        $error = "Error al agregar producto";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Producto</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>➕ Agregar Nuevo Producto</h1>
            <a href="listar.php" class="btn btn-secondary">← Volver a Productos</a>
        </header>

        <?php if (isset($error)): ?>
            <div class="alert error"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST" class="product-form">
            <div class="form-group">
                <label for="codigo">Código del Producto:</label>
                <input type="text" id="codigo" name="codigo" required>
            </div>

            <div class="form-group">
                <label for="nombre">Nombre del Producto:</label>
                <input type="text" id="nombre" name="nombre" required>
            </div>

            <div class="form-group">
                <label for="descripcion">Descripción:</label>
                <textarea id="descripcion" name="descripcion" rows="3"></textarea>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="precio">Precio (RD$):</label>
                    <input type="number" id="precio" name="precio" step="0.01" required>
                </div>

                <div class="form-group">
                    <label for="stock">Stock Inicial:</label>
                    <input type="number" id="stock" name="stock" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="categoria">Categoría:</label>
                    <select id="categoria" name="categoria" required>
                        <option value="">Seleccionar...</option>
                        <option value="Herramientas">Herramientas</option>
                        <option value="Fijaciones">Fijaciones</option>
                        <option value="Pinturas">Pinturas</option>
                        <option value="Materiales">Materiales Construcción</option>
                        <option value="Eléctricos">Materiales Eléctricos</option>
                        <option value="Plomería">Plomería</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="proveedor">Proveedor:</label>
                    <input type="text" id="proveedor" name="proveedor">
                </div>
            </div>

            <div class="form-group">
                <label for="ubicacion">Ubicación en Bodega:</label>
                <input type="text" id="ubicacion" name="ubicacion" placeholder="Ej: Bodega A - Estante 2">
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">💾 Guardar Producto</button>
                <a href="listar.php" class="btn btn-secondary">❌ Cancelar</a>
            </div>
        </form>
    </div>
</body>
</html>