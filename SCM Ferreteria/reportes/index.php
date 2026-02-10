<?php
include '../database/database.php';
$database = new Database();
$db = $database->getConnection();

// Estadísticas generales
$stats = [
    'total_productos' => $db->query("SELECT COUNT(*) FROM productos")->fetchColumn(),
    'total_ventas_mes' => $db->query("SELECT COALESCE(SUM(total), 0) FROM ventas WHERE strftime('%Y-%m', fecha_venta) = strftime('%Y-%m', 'now')")->fetchColumn(),
    'total_compras_mes' => $db->query("SELECT COALESCE(SUM(total), 0) FROM compras WHERE strftime('%Y-%m', fecha_compra) = strftime('%Y-%m', 'now')")->fetchColumn(),
    'total_clientes' => $db->query("SELECT COUNT(*) FROM clientes")->fetchColumn(),
    'inventario_valorizado' => $db->query("SELECT COALESCE(SUM(precio * stock), 0) FROM productos")->fetchColumn(),
];

$utilidad_bruta = $stats['total_ventas_mes'] - $stats['total_compras_mes'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reportes y Análisis - SCM</title>
    <link rel="stylesheet" href="../css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <style>
        .chart-container { background: white; padding: 20px; border-radius: 15px; margin: 20px 0; box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
        .chart-wrapper { max-width: 600px; margin: 0 auto; }
        .report-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; margin: 20px 0; }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>📊 Reportes y Análisis</h1>
            <p>Panel de Control y Estadísticas</p>
        </header>

        <nav>
            <ul>
                <li><a href="../productos/index.php">Inicio</a></li>
                <li><a href="../productos/listar.php">Gestión de Productos</a></li>
                <li><a href="../ventas/index.php">Módulo Ventas</a></li>
                <li><a href="../compras/index.php">Módulo Compras</a></li>
                <li><a href="index.php">Reportes</a></li>
            </ul>
        </nav>

        <main>
            <div class="dashboard">
                <div class="card">
                    <h3>📦 Total Productos</h3>
                    <p class="number"><?php echo $stats['total_productos']; ?></p>
                </div>
                <div class="card">
                    <h3>💰 Ventas del Mes</h3>
                    <p class="number">$<?php echo number_format($stats['total_ventas_mes'], 2); ?></p>
                </div>
                <div class="card">
                    <h3>🛒 Compras del Mes</h3>
                    <p class="number">$<?php echo number_format($stats['total_compras_mes'], 2); ?></p>
                </div>
                <div class="card">
                    <h3>📈 Utilidad Bruta</h3>
                    <p class="number" style="color: <?php echo $utilidad_bruta >= 0 ? '#27ae60' : '#e74c3c'; ?>">
                        $<?php echo number_format($utilidad_bruta, 2); ?>
                    </p>
                </div>
                <div class="card">
                    <h3>👥 Clientes Activos</h3>
                    <p class="number"><?php echo $stats['total_clientes']; ?></p>
                </div>
                <div class="card">
                    <h3>💎 Inventario Total</h3>
                    <p class="number">$<?php echo number_format($stats['inventario_valorizado'], 2); ?></p>
                </div>
            </div>

            <div class="report-grid">
                <div class="chart-container">
                    <h3>📊 Productos más Vendidos</h3>
                    <div class="chart-wrapper">
                        <canvas id="topProductosChart"></canvas>
                    </div>
                </div>

                <div class="chart-container">
                    <h3>🏷️ Productos por Categoría</h3>
                    <div class="chart-wrapper">
                        <canvas id="categoriasPieChart"></canvas>
                    </div>
                </div>
            </div>

            <div class="chart-container">
                <h3>📈 Ventas vs Compras (Últimos 7 Días)</h3>
                <canvas id="ventasComprasChart"></canvas>
            </div>

            <section class="content-section">
                <h2>🔴 Productos con Stock Bajo (menos de 10 unidades)</h2>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Código</th>
                            <th>Producto</th>
                            <th>Stock Actual</th>
                            <th>Precio</th>
                            <th>Categoría</th>
                            <th>Proveedor</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $query = "SELECT * FROM productos WHERE stock < 10 ORDER BY stock ASC";
                        $stmt = $db->prepare($query);
                        $stmt->execute();
                        
                        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                            echo "<tr>";
                            echo "<td><strong>{$row['codigo']}</strong></td>";
                            echo "<td>{$row['nombre']}</td>";
                            echo "<td><span class='badge badge-warning'>{$row['stock']}</span></td>";
                            echo "<td>$" . number_format($row['precio'], 2) . "</td>";
                            echo "<td>{$row['categoria']}</td>";
                            echo "<td>{$row['proveedor']}</td>";
                            echo "</tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </section>

            <section class="content-section">
                <h2>⭐ Mejores Clientes del Mes</h2>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Cliente</th>
                            <th>Total Compras</th>
                            <th>Número de Ventas</th>
                            <th>Promedio por Venta</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $query = "SELECT c.nombre, 
                                         COUNT(v.id) as num_ventas,
                                         SUM(v.total) as total_comprado,
                                         AVG(v.total) as promedio
                                  FROM clientes c
                                  LEFT JOIN ventas v ON c.id = v.cliente_id
                                  WHERE strftime('%Y-%m', v.fecha_venta) = strftime('%Y-%m', 'now')
                                  GROUP BY c.id
                                  HAVING num_ventas > 0
                                  ORDER BY total_comprado DESC
                                  LIMIT 5";
                        $stmt = $db->prepare($query);
                        $stmt->execute();
                        
                        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                            echo "<tr>";
                            echo "<td><strong>{$row['nombre']}</strong></td>";
                            echo "<td class='text-success'>$" . number_format($row['total_comprado'], 2) . "</td>";
                            echo "<td>{$row['num_ventas']}</td>";
                            echo "<td>$" . number_format($row['promedio'], 2) . "</td>";
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

    <script>
    // Productos más vendidos
    fetch('api_reportes.php?reporte=top_productos')
        .then(res => res.json())
        .then(data => {
            new Chart(document.getElementById('topProductosChart'), {
                type: 'bar',
                data: {
                    labels: data.labels,
                    datasets: [{
                        label: 'Cantidad Vendida',
                        data: data.values,
                        backgroundColor: 'rgba(52, 152, 219, 0.8)',
                        borderColor: 'rgba(52, 152, 219, 1)',
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        y: { beginAtZero: true }
                    }
                }
            });
        });

    // Productos por categoría
    fetch('api_reportes.php?reporte=categorias')
        .then(res => res.json())
        .then(data => {
            new Chart(document.getElementById('categoriasPieChart'), {
                type: 'doughnut',
                data: {
                    labels: data.labels,
                    datasets: [{
                        data: data.values,
                        backgroundColor: [
                            'rgba(255, 99, 132, 0.8)',
                            'rgba(54, 162, 235, 0.8)',
                            'rgba(255, 206, 86, 0.8)',
                            'rgba(75, 192, 192, 0.8)',
                            'rgba(153, 102, 255, 0.8)'
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { position: 'bottom' }
                    }
                }
            });
        });

    // Ventas vs Compras
    fetch('api_reportes.php?reporte=ventas_compras')
        .then(res => res.json())
        .then(data => {
            new Chart(document.getElementById('ventasComprasChart'), {
                type: 'line',
                data: {
                    labels: data.labels,
                    datasets: [{
                        label: 'Ventas',
                        data: data.ventas,
                        borderColor: 'rgba(75, 192, 192, 1)',
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        tension: 0.4
                    }, {
                        label: 'Compras',
                        data: data.compras,
                        borderColor: 'rgba(255, 99, 132, 1)',
                        backgroundColor: 'rgba(255, 99, 132, 0.2)',
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { position: 'top' }
                    },
                    scales: {
                        y: { beginAtZero: true }
                    }
                }
            });
        });
    </script>
</body>
</html>
