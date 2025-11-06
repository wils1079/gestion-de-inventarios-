<?php
require_once 'database.php';

$database = new Database();
$db = $database->getConnection();

$method = $_SERVER['REQUEST_METHOD'];
$action = isset($_GET['action']) ? $_GET['action'] : '';

try {
    switch($method) {
        case 'GET':
            if ($action === 'stats') {
                getStats($db);
            } elseif ($action === 'search') {
                searchProducts($db);
            } else {
                getProducts($db);
            }
            break;
        
        case 'POST':
            createProduct($db);
            break;
        
        case 'PUT':
            updateProduct($db);
            break;
        
        case 'DELETE':
            deleteProduct($db);
            break;
        
        default:
            echo json_encode(['error' => 'Método no permitido']);
    }
} catch(Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}

function getProducts($db) {
    $stmt = $db->query("
        SELECT p.*, c.nombre as categoria_nombre 
        FROM products p 
        LEFT JOIN categories c ON p.categoria_id = c.id 
        ORDER BY p.created_at DESC
    ");
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($products);
}

function getStats($db) {
    $totalProducts = $db->query("SELECT COUNT(*) FROM products")->fetchColumn();
    $lowStock = $db->query("SELECT COUNT(*) FROM products WHERE cantidad <= stock_minimo")->fetchColumn();
    $totalValue = $db->query("SELECT SUM(cantidad * precio) FROM products")->fetchColumn();
    
    echo json_encode([
        'total_productos' => $totalProducts,
        'stock_bajo' => $lowStock,
        'valor_total' => round($totalValue, 2)
    ]);
}

function searchProducts($db) {
    $search = isset($_GET['q']) ? $_GET['q'] : '';
    $categoria = isset($_GET['categoria']) ? $_GET['categoria'] : '';
    
    $query = "SELECT p.*, c.nombre as categoria_nombre 
              FROM products p 
              LEFT JOIN categories c ON p.categoria_id = c.id 
              WHERE 1=1";
    
    $params = [];
    
    if (!empty($search)) {
        $query .= " AND (p.nombre LIKE ? OR p.descripcion LIKE ?)";
        $params[] = "%$search%";
        $params[] = "%$search%";
    }
    
    if (!empty($categoria)) {
        $query .= " AND p.categoria_id = ?";
        $params[] = $categoria;
    }
    
    $query .= " ORDER BY p.created_at DESC";
    
    $stmt = $db->prepare($query);
    $stmt->execute($params);
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($products);
}

function createProduct($db) {
    $data = json_decode(file_get_contents("php://input"), true);
    
    if (!isset($data['nombre']) || !isset($data['precio']) || !isset($data['cantidad'])) {
        echo json_encode(['error' => 'Datos incompletos']);
        return;
    }
    
    $stmt = $db->prepare("INSERT INTO products (nombre, descripcion, categoria_id, cantidad, precio, stock_minimo) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        $data['nombre'],
        $data['descripcion'] ?? '',
        $data['categoria_id'] ?? null,
        $data['cantidad'],
        $data['precio'],
        $data['stock_minimo'] ?? 10
    ]);
    
    echo json_encode(['success' => true, 'id' => $db->lastInsertId()]);
}

function updateProduct($db) {
    $data = json_decode(file_get_contents("php://input"), true);
    
    if (!isset($data['id'])) {
        echo json_encode(['error' => 'ID no proporcionado']);
        return;
    }
    
    $stmt = $db->prepare("UPDATE products SET nombre = ?, descripcion = ?, categoria_id = ?, cantidad = ?, precio = ?, stock_minimo = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?");
    $stmt->execute([
        $data['nombre'],
        $data['descripcion'] ?? '',
        $data['categoria_id'] ?? null,
        $data['cantidad'],
        $data['precio'],
        $data['stock_minimo'] ?? 10,
        $data['id']
    ]);
    
    echo json_encode(['success' => true]);
}

function deleteProduct($db) {
    $id = isset($_GET['id']) ? $_GET['id'] : '';
    
    if (empty($id)) {
        echo json_encode(['error' => 'ID no proporcionado']);
        return;
    }
    
    $stmt = $db->prepare("DELETE FROM products WHERE id = ?");
    $stmt->execute([$id]);
    
    echo json_encode(['success' => true]);
}