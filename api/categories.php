<?php
require_once 'database.php';

$database = new Database();
$db = $database->getConnection();

$method = $_SERVER['REQUEST_METHOD'];

try {
    switch($method) {
        case 'GET':
            getCategories($db);
            break;
        
        case 'POST':
            createCategory($db);
            break;
        
        case 'PUT':
            updateCategory($db);
            break;
        
        case 'DELETE':
            deleteCategory($db);
            break;
        
        default:
            echo json_encode(['error' => 'Método no permitido']);
    }
} catch(Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}

function getCategories($db) {
    $stmt = $db->query("SELECT * FROM categories ORDER BY nombre ASC");
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($categories);
}

function createCategory($db) {
    $data = json_decode(file_get_contents("php://input"), true);
    
    if (!isset($data['nombre'])) {
        echo json_encode(['error' => 'Nombre de categoría requerido']);
        return;
    }
    
    $stmt = $db->prepare("INSERT INTO categories (nombre, descripcion) VALUES (?, ?)");
    $stmt->execute([
        $data['nombre'],
        $data['descripcion'] ?? ''
    ]);
    
    echo json_encode(['success' => true, 'id' => $db->lastInsertId()]);
}

function updateCategory($db) {
    $data = json_decode(file_get_contents("php://input"), true);
    
    if (!isset($data['id']) || !isset($data['nombre'])) {
        echo json_encode(['error' => 'ID y nombre de categoría requeridos']);
        return;
    }
    
    $stmt = $db->prepare("UPDATE categories SET nombre = ?, descripcion = ? WHERE id = ?");
    $stmt->execute([
        $data['nombre'],
        $data['descripcion'] ?? '',
        $data['id']
    ]);
    
    echo json_encode(['success' => true]);
}

function deleteCategory($db) {
    $id = isset($_GET['id']) ? $_GET['id'] : '';
    
    if (empty($id)) {
        echo json_encode(['error' => 'ID no proporcionado']);
        return;
    }
    
    $stmt = $db->prepare("DELETE FROM categories WHERE id = ?");
    $stmt->execute([$id]);
    
    echo json_encode(['success' => true]);
}