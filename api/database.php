<?php
// database.php
class Database {
    // Credenciales de base de datos
    private $host = "localhost";
    private $db_name = "inventarios_db";
    private $username = "root";
    private $password = "";
    public $conn;

    // Obtener la conexión
    public function getConnection() {
        $this->conn = null;

        try {
            // Conexión PDO a MySQL
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name, 
                $this->username, 
                $this->password
            );
            // Configuración de caracteres y errores
            $this->conn->exec("set names utf8mb4");
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $exception) {
            echo "Error de conexión: " . $exception->getMessage();
        }

        return $this->conn;
    }
}
?>