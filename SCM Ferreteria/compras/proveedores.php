<?php
include '../database/database.php';
$database = new Database();
$db = $database->getConnection();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['accion'])) {
    if($_POST['accion'] == 'crear') {
        $query = "INSERT INTO proveedores (codigo, nombre, contacto, telefono, email, direccion) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $db->prepare($query);
        $stmt->execute([$_POST['codigo'], $_POST['nombre'], $_POST['contacto'], $_POST['telefono'], $_POST['email'], $_POST['direccion']]);
        header('Location: proveedores.php?success=1');
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Proveedores - SCM</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>🏢 Gestión de Proveedores</h1>
            <p>Administración de Proveedores</p>
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
            <?php if(isset($_GET['success'])) echo "<div class='alert alert-success'>✅ Operación exitosa</div>"; ?>
            
            <div class="form-container">
                <h2>➕ Nuevo Proveedor</h2>
                <form method="POST">
                    <input type="hidden" name="accion" value="crear">
                    <div class="form-row">
                        <div class="form-group">
                            <label>Código</label>
                            <input type="text" name="codigo" required>
                        </div>
                        <div class="form-group">
                            <label>Nombre Empresa</label>
                            <input type="text" name="nombre" required>
                        </div>
                        <div class="form-group">
                            <label>Persona de Contacto</label>
                            <input type="text" name="contacto">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Teléfono</label>
                            <input type="text" name="telefono">
                        </div>
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" name="email">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Dirección</label>
                        <textarea name="direccion" rows="2"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">💾 Guardar Proveedor</button>
                </form>
            </div>

            <section class="content-section">
                <h2>📋 Lista de Proveedores</h2>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Código</th>
                            <th>Nombre</th>
                            <th>Contacto</th>
                            <th>Teléfono</th>
                            <th>Email</th>
                            <th>Fecha Registro</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $query = "SELECT * FROM proveedores ORDER BY fecha_registro DESC";
                        $stmt = $db->prepare($query);
                        $stmt->execute();
                        
                        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                            echo "<tr>";
                            echo "<td><strong>{$row['codigo']}</strong></td>";
                            echo "<td>{$row['nombre']}</td>";
                            echo "<td>{$row['contacto']}</td>";
                            echo "<td>{$row['telefono']}</td>";
                            echo "<td>{$row['email']}</td>";
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
