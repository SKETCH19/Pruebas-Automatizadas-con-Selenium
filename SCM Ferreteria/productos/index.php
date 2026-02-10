<?php
include '../database/database.php';
$database = new Database();
$db = $database->getConnection();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema SCM - Ferreterías</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>🔧 Sistema SCM - Ferretería</h1>
            <p>Sistema de Gestión de Cadena de Suministro</p>
        </header>

        <nav>
            <ul>
                <li><a href="index.php">Inicio</a></li>
                <li><a href="listar.php">Gestión de Productos</a></li>
                <li><a href="../ventas/index.php">Módulo Ventas</a></li> 
                <li><a href="../compras/index.php">Módulo Compras</a></li>
                <li><a href="../reportes/index.php">Reportes</a></li>
            </ul>
        </nav>

        <main>
            <div class="dashboard">
                <div class="card">
                    <h3>📦 Total Productos</h3>
                    <?php
                    $query = "SELECT COUNT(*) as total FROM productos";
                    $stmt = $db->prepare($query);
                    $stmt->execute();
                    $row = $stmt->fetch(PDO::FETCH_ASSOC);
                    echo "<p class='number'>" . $row['total'] . "</p>";
                    ?>
                </div>

                <div class="card">
                    <h3>💰 Inventario Valorizado</h3>
                    <?php
                    $query = "SELECT SUM(precio * stock) as total FROM productos";
                    $stmt = $db->prepare($query);
                    $stmt->execute();
                    $row = $stmt->fetch(PDO::FETCH_ASSOC);
                    echo "<p class='number'>RD$ " . number_format($row['total'], 2) . "</p>";
                    ?>
                </div>

                <div class="card">
                    <h3>⚠️ Stock Bajo</h3>
                    <?php
                    $query = "SELECT COUNT(*) as bajos FROM productos WHERE stock < 10";
                    $stmt = $db->prepare($query);
                    $stmt->execute();
                    $row = $stmt->fetch(PDO::FETCH_ASSOC);
                    echo "<p class='number warning'>" . $row['bajos'] . "</p>";
                    ?>
                </div>
            </div>

            <div class="actions">
                <a href="agregar.php" class="btn btn-primary">➕ Agregar Producto</a>
                <a href="listar.php" class="btn btn-secondary">📋 Ver Todos los Productos</a>
            </div>
        </main>
    </div>
</body>
</html>