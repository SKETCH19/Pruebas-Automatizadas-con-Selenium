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
    <title>Módulo de Ventas - SCM</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>💰 Módulo de Ventas</h1>
            <p>Gestión de Ventas y Facturación</p>
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
            <div class="dashboard">
                <div class="card">
                    <h3>📊 Ventas Hoy</h3>
                    <?php
                    $query = "SELECT COUNT(*) as total FROM ventas WHERE DATE(fecha_venta) = DATE('now')";
                    $stmt = $db->prepare($query);
                    $stmt->execute();
                    $row = $stmt->fetch(PDO::FETCH_ASSOC);
                    echo "<p class='number'>" . $row['total'] . "</p>";
                    ?>
                </div>

                <div class="card">
                    <h3>💵 Total Vendido (Mes)</h3>
                    <?php
                    $query = "SELECT COALESCE(SUM(total), 0) as total FROM ventas 
                              WHERE strftime('%Y-%m', fecha_venta) = strftime('%Y-%m', 'now')";
                    $stmt = $db->prepare($query);
                    $stmt->execute();
                    $row = $stmt->fetch(PDO::FETCH_ASSOC);
                    echo "<p class='number'>$" . number_format($row['total'], 2) . "</p>";
                    ?>
                </div>

                <div class="card">
                    <h3>👥 Clientes Activos</h3>
                    <?php
                    $query = "SELECT COUNT(*) as total FROM clientes";
                    $stmt = $db->prepare($query);
                    $stmt->execute();
                    $row = $stmt->fetch(PDO::FETCH_ASSOC);
                    echo "<p class='number'>" . $row['total'] . "</p>";
                    ?>
                </div>

                <div class="card">
                    <h3>✅ Ventas Completadas</h3>
                    <?php
                    $query = "SELECT COUNT(*) as total FROM ventas WHERE estado = 'completada'";
                    $stmt = $db->prepare($query);
                    $stmt->execute();
                    $row = $stmt->fetch(PDO::FETCH_ASSOC);
                    echo "<p class='number'>" . $row['total'] . "</p>";
                    ?>
                </div>
            </div>

            <div class="actions">
                <a href="nueva_venta.php" class="btn btn-primary">🛒 Nueva Venta</a>
                <a href="listar_ventas.php" class="btn btn-secondary">📋 Ver Todas las Ventas</a>
                <a href="clientes.php" class="btn btn-info">👤 Gestionar Clientes</a>
            </div>

            <section class="content-section">
                <h2>📈 Últimas Ventas</h2>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Factura</th>
                            <th>Cliente</th>
                            <th>Fecha</th>
                            <th>Total</th>
                            <th>Método Pago</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $query = "SELECT v.*, c.nombre as cliente_nombre 
                                  FROM ventas v 
                                  LEFT JOIN clientes c ON v.cliente_id = c.id 
                                  ORDER BY v.fecha_venta DESC 
                                  LIMIT 10";
                        $stmt = $db->prepare($query);
                        $stmt->execute();
                        
                        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                            $estado_class = $row['estado'] == 'completada' ? 'badge-success' : 'badge-warning';
                            echo "<tr>";
                            echo "<td><strong>{$row['numero_factura']}</strong></td>";
                            echo "<td>{$row['cliente_nombre']}</td>";
                            echo "<td>" . date('d/m/Y H:i', strtotime($row['fecha_venta'])) . "</td>";
                            echo "<td>$" . number_format($row['total'], 2) . "</td>";
                            echo "<td>{$row['metodo_pago']}</td>";
                            echo "<td><span class='badge {$estado_class}'>{$row['estado']}</span></td>";
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
