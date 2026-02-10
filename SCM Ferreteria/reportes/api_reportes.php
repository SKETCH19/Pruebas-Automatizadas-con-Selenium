<?php
header('Content-Type: application/json');
include '../database/database.php';
$database = new Database();
$db = $database->getConnection();

$reporte = $_GET['reporte'] ?? '';

switch($reporte) {
    case 'top_productos':
        $query = "SELECT p.nombre, SUM(vd.cantidad) as total
                  FROM ventas_detalle vd
                  JOIN productos p ON vd.producto_id = p.id
                  GROUP BY vd.producto_id
                  ORDER BY total DESC
                  LIMIT 5";
        $stmt = $db->prepare($query);
        $stmt->execute();
        $data = ['labels' => [], 'values' => []];
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $data['labels'][] = $row['nombre'];
            $data['values'][] = (int)$row['total'];
        }
        echo json_encode($data);
        break;
        
    case 'categorias':
        $query = "SELECT categoria, COUNT(*) as total
                  FROM productos
                  GROUP BY categoria";
        $stmt = $db->prepare($query);
        $stmt->execute();
        $data = ['labels' => [], 'values' => []];
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $data['labels'][] = $row['categoria'] ?: 'Sin categoría';
            $data['values'][] = (int)$row['total'];
        }
        echo json_encode($data);
        break;
        
    case 'ventas_compras':
        $data = ['labels' => [], 'ventas' => [], 'compras' => []];
        for($i = 6; $i >= 0; $i--) {
            $fecha = date('Y-m-d', strtotime("-$i days"));
            $data['labels'][] = date('d/m', strtotime($fecha));
            
            // Ventas del día
            $query = "SELECT COALESCE(SUM(total), 0) as total FROM ventas WHERE DATE(fecha_venta) = ?";
            $stmt = $db->prepare($query);
            $stmt->execute([$fecha]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $data['ventas'][] = (float)$row['total'];
            
            // Compras del día
            $query = "SELECT COALESCE(SUM(total), 0) as total FROM compras WHERE DATE(fecha_compra) = ?";
            $stmt = $db->prepare($query);
            $stmt->execute([$fecha]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $data['compras'][] = (float)$row['total'];
        }
        echo json_encode($data);
        break;
        
    default:
        echo json_encode(['error' => 'Reporte no válido']);
}
?>
