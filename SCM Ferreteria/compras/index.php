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
    <title>Módulo de Compras - SCM</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>🛒 Módulo de Compras</h1>
            <p>Gestión de Órdenes de Compra y Proveedores</p>
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
            <div class="dashboard">
                <div class="card">
                    <h3>📦 Compras del Mes</h3>
                    <?php
                    $query = "SELECT COUNT(*) as total FROM compras 
                              WHERE strftime('%Y-%m', fecha_compra) = strftime('%Y-%m', 'now')";
                    $stmt = $db->prepare($query);
                    $stmt->execute();
                    $row = $stmt->fetch(PDO::FETCH_ASSOC);
                    echo "<p class='number'>" . $row['total'] . "</p>";
                    ?>
                </div>

                <div class="card">
                    <h3>💵 Total Gastado (Mes)</h3>
                    <?php
                    $query = "SELECT COALESCE(SUM(total), 0) as total FROM compras 
                              WHERE strftime('%Y-%m', fecha_compra) = strftime('%Y-%m', 'now')";
                    $stmt = $db->prepare($query);
                    $stmt->execute();
                    $row = $stmt->fetch(PDO::FETCH_ASSOC);
                    echo "<p class='number'>$" . number_format($row['total'], 2) . "</p>";
                    ?>
                </div>

                <div class="card">
                    <h3>🏢 Proveedores Activos</h3>
                    <?php
                    $query = "SELECT COUNT(*) as total FROM proveedores";
                    $stmt = $db->prepare($query);
                    $stmt->execute();
                    $row = $stmt->fetch(PDO::FETCH_ASSOC);
                    echo "<p class='number'>" . $row['total'] . "</p>";
                    ?>
                </div>

                <div class="card">
                    <h3>⏳ Órdenes Pendientes</h3>
                    <?php
                    $query = "SELECT COUNT(*) as total FROM compras WHERE estado = 'pendiente'";
                    $stmt = $db->prepare($query);
                    $stmt->execute();
                    $row = $stmt->fetch(PDO::FETCH_ASSOC);
                    echo "<p class='number'>" . $row['total'] . "</p>";
                    ?>
                </div>
            </div>

            <div class="actions">
                <a href="nueva_compra.php" class="btn btn-primary">📝 Nueva Orden de Compra</a>
                <a href="listar_compras.php" class="btn btn-secondary">📋 Ver Todas las Compras</a>
                <a href="proveedores.php" class="btn btn-info">🏢 Gestionar Proveedores</a>
            </div>

            <section class="content-section">
                <h2>📈 Últimas Órdenes de Compra</h2>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Orden</th>
                            <th>Proveedor</th>
                            <th>Fecha</th>
                            <th>Total</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $query = "SELECT c.*, p.nombre as proveedor_nombre 
                                  FROM compras c 
                                  LEFT JOIN proveedores p ON c.proveedor_id = p.id 
                                  ORDER BY c.fecha_compra DESC 
                                  LIMIT 10";
                        $stmt = $db->prepare($query);
                        $stmt->execute();
                        
                        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                            $estado_class = $row['estado'] == 'recibida' ? 'badge-success' : 'badge-warning';
                            echo "<tr>";
                            echo "<td><strong>{$row['numero_orden']}</strong></td>";
                            echo "<td>{$row['proveedor_nombre']}</td>";
                            echo "<td>" . date('d/m/Y H:i', strtotime($row['fecha_compra'])) . "</td>";
                            echo "<td>$" . number_format($row['total'], 2) . "</td>";
                            echo "<td><span class='badge {$estado_class}'>{$row['estado']}</span></td>";
                            echo "<td>";
                            if($row['estado'] == 'pendiente') {
                                echo "<a href='recibir_compra.php?id={$row['id']}' class='btn-small btn-success'>✅ Recibir</a>";
                            }
                            echo "</td>";
                            echo "</tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </section>
        </main>

        <footer>
            <p>&copy; 2026 Sistema SCM - Ferretería | Desarrollado con ❤️</p>
        </footer>
    </div>
</body>
</html>
