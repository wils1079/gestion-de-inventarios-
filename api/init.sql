-- init.sql
-- Estructura para MySQL/MariaDB

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

-- Crear tablas
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL UNIQUE,
    descripcion TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(200) NOT NULL,
    descripcion TEXT,
    categoria_id INT,
    cantidad INT NOT NULL DEFAULT 0,
    precio DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    stock_minimo INT DEFAULT 10,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (categoria_id) REFERENCES categories(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insertar datos iniciales (Semillas)
INSERT INTO categories (nombre, descripcion) VALUES
('Electrónica','Productos electrónicos y tecnología'),
('Alimentos','Productos alimenticios'),
('Ropa','Prendas de vestir'),
('Herramientas','Herramientas y equipos'),
('Oficina','Artículos de oficina');

INSERT INTO products (nombre, descripcion, categoria_id, cantidad, precio, stock_minimo) VALUES
('Laptop Dell','Laptop Dell Inspiron 15', 1, 5, 899.99, 5),
('Mouse Inalámbrico','Mouse Logitech M185', 1, 25, 15.99, 5),
('Arroz 1kg','Arroz blanco grano largo', 2, 100, 2.50, 20),
('Aceite de Oliva','Aceite extra virgen 500ml', 2, 8, 8.99, 10),
('Camisa Formal','Camisa blanca talla M', 3, 15, 29.99, 5),
('Destornillador Set','Set de 6 destornilladores', 4, 12, 19.99, 5),
('Lapiceros Caja','Caja con 20 lapiceros', 5, 30, 5.99, 10);