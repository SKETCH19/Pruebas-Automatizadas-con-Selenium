<?php
include '../database/database.php';
$database = new Database();
$db = $database->getConnection();

$id = $_GET['id'];

// Esto verifica que el producto existe
$query = "SELECT * FROM productos WHERE id = ?";
$stmt = $db->prepare($query);
$stmt->execute([$id]);
$producto = $stmt->fetch(PDO::FETCH_ASSOC);

if ($producto) {
    $deleteQuery = "DELETE FROM productos WHERE id = ?";
    $deleteStmt = $db->prepare($deleteQuery);
    $deleteStmt->execute([$id]);
}

header("Location: listar.php?deleted=1");
exit();
?>