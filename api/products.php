<?php
// products.php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, GET, PUT, DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require_once __DIR__ . '/database.php';

// Instanciar base de datos y conectar
$database = new Database();
$pdo = $database->getConnection();

$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents('php://input'), true);

// GET handlers: list, stats, search, single
if($method === 'GET'){
    if(isset($_GET['action']) && $_GET['action'] === 'stats'){
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM products");
        $total = (int)$stmt->fetch(PDO::FETCH_ASSOC)['total'];

        $stmt = $pdo->query("SELECT COUNT(*) as low FROM products WHERE cantidad <= stock_minimo");
        $low = (int)$stmt->fetch(PDO::FETCH_ASSOC)['low'];

        $stmt = $pdo->query("SELECT SUM(cantidad * precio) as valor FROM products");
        $valor = $stmt->fetch(PDO::FETCH_ASSOC)['valor'] ?? 0;
        echo json_encode(['total_productos'=>$total, 'stock_bajo'=>$low, 'valor_total'=>floatval($valor)]);
        exit;
    }

    if(isset($_GET['action']) && $_GET['action'] === 'search'){
        $q = $_GET['q'] ?? '';
        $cat = $_GET['categoria'] ?? '';
        // SQL Ajustado para MySQL (Concatenación y lógica estándar)
        $sql = "SELECT p.*, c.nombre as categoria_nombre FROM products p LEFT JOIN categories c ON c.id = p.categoria_id WHERE 1=1";
        $params = [];
        if($q !== ''){
            $sql .= " AND (p.nombre LIKE :q OR p.descripcion LIKE :q)";
            $params[':q'] = "%$q%";
        }
        if($cat !== ''){
            $sql .= " AND p.categoria_id = :cat";
            $params[':cat'] = $cat;
        }
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
        exit;
    }
    
    // Listar productos por defecto
    $stmt = $pdo->query("SELECT p.*, c.nombre as categoria_nombre FROM products p LEFT JOIN categories c ON c.id = p.categoria_id ORDER BY p.created_at DESC");
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    exit;
}

if($method === 'POST'){
    $sql = "INSERT INTO products (nombre, descripcion, categoria_id, cantidad, precio, stock_minimo) VALUES (:nombre, :descripcion, :categoria_id, :cantidad, :precio, :stock_minimo)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':nombre'=>$input['nombre'] ?? '',
        ':descripcion'=>$input['descripcion'] ?? '',
        ':categoria_id'=>!empty($input['categoria_id']) ? $input['categoria_id'] : null,
        ':cantidad'=>intval($input['cantidad'] ?? 0),
        ':precio'=>floatval($input['precio'] ?? 0),
        ':stock_minimo'=>intval($input['stock_minimo'] ?? 10)
    ]);
    echo json_encode(['success'=>true, 'id'=>$pdo->lastInsertId()]);
    exit;
}

if($method === 'PUT'){
    // MySQL usa CURRENT_TIMESTAMP, igual que SQLite, esto es compatible
    $sql = "UPDATE products SET nombre=:nombre, descripcion=:descripcion, categoria_id=:categoria_id, cantidad=:cantidad, precio=:precio, stock_minimo=:stock_minimo WHERE id=:id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':id'=>$input['id'],
        ':nombre'=>$input['nombre'] ?? '',
        ':descripcion'=>$input['descripcion'] ?? '',
        ':categoria_id'=>!empty($input['categoria_id']) ? $input['categoria_id'] : null,
        ':cantidad'=>intval($input['cantidad'] ?? 0),
        ':precio'=>floatval($input['precio'] ?? 0),
        ':stock_minimo'=>intval($input['stock_minimo'] ?? 10)
    ]);
    echo json_encode(['success'=>true]);
    exit;
}

if($method === 'DELETE'){
    if(!isset($_GET['id'])) { echo json_encode(['success'=>false,'error'=>'Falta ID']); exit; }
    $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    echo json_encode(['success'=>true]);
    exit;
}
?>