<?php
require_once __DIR__ . '/database.php';
$pdo = Database::get();

$method = $_SERVER['REQUEST_METHOD'];
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
        $sql .= " ORDER BY p.id ASC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($rows);
        exit;
    }

    if(isset($_GET['id'])){
        $stmt = $pdo->prepare("SELECT p.*, c.nombre as categoria_nombre FROM products p LEFT JOIN categories c ON c.id = p.categoria_id WHERE p.id = :id");
        $stmt->execute([':id'=>$_GET['id']]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        echo json_encode($row ?: (object)[]);
        exit;
    }

    // default: return all
    $stmt = $pdo->query("SELECT p.*, c.nombre as categoria_nombre FROM products p LEFT JOIN categories c ON c.id = p.categoria_id ORDER BY p.id ASC");
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    exit;
}

// read JSON body
$input = json_decode(file_get_contents('php://input'), true);

if($method === 'POST'){
    // create
    $sql = "INSERT INTO products (nombre, descripcion, categoria_id, cantidad, precio, stock_minimo) VALUES (:nombre, :descripcion, :categoria_id, :cantidad, :precio, :stock_minimo)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':nombre'=>$input['nombre'] ?? '',
        ':descripcion'=>$input['descripcion'] ?? '',
        ':categoria_id'=>$input['categoria_id'] ?: null,
        ':cantidad'=>intval($input['cantidad'] ?? 0),
        ':precio'=>floatval($input['precio'] ?? 0),
        ':stock_minimo'=>intval($input['stock_minimo'] ?? 10)
    ]);
    echo json_encode(['success'=>true, 'id'=>$pdo->lastInsertId()]);
    exit;
}

if($method === 'PUT'){
    $sql = "UPDATE products SET nombre=:nombre, descripcion=:descripcion, categoria_id=:categoria_id, cantidad=:cantidad, precio=:precio, stock_minimo=:stock_minimo, updated_at=CURRENT_TIMESTAMP WHERE id=:id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':id'=>$input['id'],
        ':nombre'=>$input['nombre'] ?? '',
        ':descripcion'=>$input['descripcion'] ?? '',
        ':categoria_id'=>$input['categoria_id'] ?: null,
        ':cantidad'=>intval($input['cantidad'] ?? 0),
        ':precio'=>floatval($input['precio'] ?? 0),
        ':stock_minimo'=>intval($input['stock_minimo'] ?? 10)
    ]);
    echo json_encode(['success'=>true]);
    exit;
}

if($method === 'DELETE'){
    if(!isset($_GET['id'])) { echo json_encode(['success'=>false,'error'=>'id required']); exit; }
    $stmt = $pdo->prepare("DELETE FROM products WHERE id=:id");
    $stmt->execute([':id'=>$_GET['id']]);
    echo json_encode(['success'=>true]);
    exit;
}

echo json_encode(['success'=>false]);