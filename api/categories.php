<?php
// categories.php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, GET, PUT, DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require_once 'database.php';

$database = new Database();
$db = $database->getConnection();

$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents('php://input'), true);

try {
    switch($method) {
        case 'GET':
            $stmt = $db->query("SELECT * FROM categories ORDER BY nombre ASC");
            $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($categories);
            break;
        
        case 'POST':
            if (!isset($input['nombre'])) {
                echo json_encode(['error' => 'Nombre de categoría requerido']);
                break;
            }
            $stmt = $db->prepare("INSERT INTO categories (nombre, descripcion) VALUES (?, ?)");
            $stmt->execute([
                $input['nombre'],
                $input['descripcion'] ?? ''
            ]);
            echo json_encode(['success' => true, 'id' => $db->lastInsertId()]);
            break;
        
        case 'PUT':
            if (!isset($input['id']) || !isset($input['nombre'])) {
                echo json_encode(['error' => 'ID y nombre requeridos']);
                break;
            }
            $stmt = $db->prepare("UPDATE categories SET nombre = ?, descripcion = ? WHERE id = ?");
            $stmt->execute([
                $input['nombre'],
                $input['descripcion'] ?? '',
                $input['id']
            ]);
            echo json_encode(['success' => true]);
            break;
        
        case 'DELETE':
            $id = isset($_GET['id']) ? $_GET['id'] : '';
            if (empty($id)) {
                echo json_encode(['error' => 'ID requerido']);
                break;
            }
            $stmt = $db->prepare("DELETE FROM categories WHERE id = ?");
            $stmt->execute([$id]);
            echo json_encode(['success' => true]);
            break;
            
        default:
            echo json_encode(['error' => 'Método no permitido']);
    }
} catch(Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>