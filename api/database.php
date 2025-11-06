<?php
require_once 'config.php';

class Database {
    private $conn;

    public function __construct() {
        try {
            $this->conn = new PDO('sqlite:' . DB_FILE);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->createTables();
        } catch(PDOException $e) {
            die(json_encode(['error' => 'Error de conexión: ' . $e->getMessage()]));
        }
    }

    private function createTables() {
        $categoriesTable = "CREATE TABLE IF NOT EXISTS categories (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            nombre VARCHAR(100) NOT NULL UNIQUE,
            descripcion TEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )";

        $productsTable = "CREATE TABLE IF NOT EXISTS products (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            nombre VARCHAR(200) NOT NULL,
            descripcion TEXT,
            categoria_id INTEGER,
            cantidad INTEGER NOT NULL DEFAULT 0,
            precio DECIMAL(10,2) NOT NULL DEFAULT 0.00,
            stock_minimo INTEGER DEFAULT 10,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (categoria_id) REFERENCES categories(id) ON DELETE SET NULL
        )";

        $this->conn->exec($categoriesTable);
        $this->conn->exec($productsTable);
        
        $this->insertDefaultData();
    }

    private function insertDefaultData() {
        $count = $this->conn->query("SELECT COUNT(*) FROM categories")->fetchColumn();
        
        if ($count == 0) {
            $defaultCategories = [
                ['Electrónica', 'Productos electrónicos y tecnología'],
                ['Alimentos', 'Productos alimenticios'],
                ['Ropa', 'Prendas de vestir'],
                ['Herramientas', 'Herramientas y equipos'],
                ['Oficina', 'Artículos de oficina']
            ];

            $stmt = $this->conn->prepare("INSERT INTO categories (nombre, descripcion) VALUES (?, ?)");
            foreach ($defaultCategories as $cat) {
                $stmt->execute($cat);
            }

            $defaultProducts = [
                ['Laptop Dell', 'Laptop Dell Inspiron 15', 1, 5, 899.99, 3],
                ['Mouse Inalámbrico', 'Mouse Logitech M185', 1, 25, 15.99, 10],
                ['Arroz 1kg', 'Arroz blanco grano largo', 2, 100, 2.50, 20],
                ['Aceite de Oliva', 'Aceite extra virgen 500ml', 2, 8, 8.99, 5],
                ['Camisa Formal', 'Camisa blanca talla M', 3, 15, 29.99, 5],
                ['Destornillador Set', 'Set de 6 destornilladores', 4, 12, 19.99, 5],
                ['Lapiceros Caja', 'Caja de 12 lapiceros azules', 5, 30, 5.99, 15]
            ];

            $stmt = $this->conn->prepare("INSERT INTO products (nombre, descripcion, categoria_id, cantidad, precio, stock_minimo) VALUES (?, ?, ?, ?, ?, ?)");
            foreach ($defaultProducts as $prod) {
                $stmt->execute($prod);
            }
        }
    }

    public function getConnection() {
        return $this->conn;
    }
}