<?php
include '../database/database.php';
$database = new Database();
$db = $database->getConnection();

$query = "SELECT * FROM productos ORDER BY id DESC";
$stmt = $db->prepare($query);
$stmt->execute();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Producto</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>📦 Gestión de Productos</h1>
            <a href="index.php" class="btn btn-secondary">← Volver al Inicio</a>
        </header>

        <div class="actions">
            <a href="agregar.php" class="btn btn-primary">➕ Agregar Nuevo Producto</a>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Nombre</th>
                        <th>Precio</th>
                        <th>Stock</th>
                        <th>Categoría</th>
                        <th>Ubicación</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['codigo']); ?></td>
                        <td><?php echo htmlspecialchars($row['nombre']); ?></td>
                        <td>RD$ <?php echo number_format($row['precio'], 2); ?></td>
                        <td>
                            <span class="stock <?php echo $row['stock'] < 10 ? 'low' : ''; ?>">
                                <?php echo $row['stock']; ?>
                            </span>
                        </td>
                        <td><?php echo htmlspecialchars($row['categoria']); ?></td>
                        <td><?php echo htmlspecialchars($row['ubicacion']); ?></td>
                        <td class="actions">
                            <a href="editar.php?id=<?php echo $row['id']; ?>" class="btn btn-edit">✏️ Editar</a>
                            <a href="eliminar.php?id=<?php echo $row['id']; ?>" class="btn btn-delete" onclick="return confirm('¿Estás seguro?')">🗑️ Eliminar</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>