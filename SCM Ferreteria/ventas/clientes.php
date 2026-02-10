<?php
include '../database/database.php';
$database = new Database();
$db = $database->getConnection();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['accion'])) {
    if($_POST['accion'] == 'crear') {
        $query = "INSERT INTO clientes (codigo, nombre, telefono, email, direccion, ciudad) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $db->prepare($query);
        $stmt->execute([$_POST['codigo'], $_POST['nombre'], $_POST['telefono'], $_POST['email'], $_POST['direccion'], $_POST['ciudad']]);
        header('Location: clientes.php?success=1');
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Clientes - SCM</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>👥 Gestión de Clientes</h1>
            <p>Administración de Clientes</p>
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
            <?php if(isset($_GET['success'])) echo "<div class='alert alert-success'>✅ Operación exitosa</div>"; ?>
            
            <div class="form-container">
                <h2>➕ Nuevo Cliente</h2>
                <form method="POST">
                    <input type="hidden" name="accion" value="crear">
                    <div class="form-row">
                        <div class="form-group">
                            <label>Código</label>
                            <input type="text" name="codigo" required>
                        </div>
                        <div class="form-group">
                            <label>Nombre</label>
                            <input type="text" name="nombre" required>
                        </div>
                        <div class="form-group">
                            <label>Teléfono</label>
                            <input type="text" name="telefono">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" name="email">
                        </div>
                        <div class="form-group">
                            <label>Ciudad</label>
                            <input type="text" name="ciudad">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Dirección</label>
                        <textarea name="direccion" rows="2"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">💾 Guardar Cliente</button>
                </form>
            </div>

            <section class="content-section">
                <h2>📋 Lista de Clientes</h2>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Código</th>
                            <th>Nombre</th>
                            <th>Teléfono</th>
                            <th>Email</th>
                            <th>Ciudad</th>
                            <th>Fecha Registro</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $query = "SELECT * FROM clientes ORDER BY fecha_registro DESC";
                        $stmt = $db->prepare($query);
                        $stmt->execute();
                        
                        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                            echo "<tr>";
                            echo "<td><strong>{$row['codigo']}</strong></td>";
                            echo "<td>{$row['nombre']}</td>";
                            echo "<td>{$row['telefono']}</td>";
                            echo "<td>{$row['email']}</td>";
                            echo "<td>{$row['ciudad']}</td>";
                            echo "<td>" . date('d/m/Y', strtotime($row['fecha_registro'])) . "</td>";
                            echo "</tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </section>
        </main>
    </div>
</body>
</html>
