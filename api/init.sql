
PRAGMA foreign_keys = ON;

CREATE TABLE IF NOT EXISTS categories (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    nombre VARCHAR(100) NOT NULL UNIQUE,
    descripcion TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS products (
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
);

-- seed categories
INSERT INTO categories (nombre, descripcion) VALUES
('Electrónica','Productos electrónicos y tecnología'),
('Alimentos','Productos alimenticios'),
('Ropa','Prendas de vestir'),
('Herramientas','Herramientas y equipos'),
('Oficina','Artículos de oficina');

-- seed products
INSERT INTO products (nombre, descripcion, categoria_id, cantidad, precio, stock_minimo) VALUES
('Laptop Dell','Laptop Dell Inspiron 15', (SELECT id FROM categories WHERE nombre='Electrónica'), 5, 899.99, 5),
('Mouse Inalámbrico','Mouse Logitech M185', (SELECT id FROM categories WHERE nombre='Electrónica'), 25, 15.99, 5),
('Arroz 1kg','Arroz blanco grano largo', (SELECT id FROM categories WHERE nombre='Alimentos'), 100, 2.50, 20),
('Aceite de Oliva','Aceite extra virgen 500ml', (SELECT id FROM categories WHERE nombre='Alimentos'), 8, 8.99, 10),
('Camisa Formal','Camisa blanca talla M', (SELECT id FROM categories WHERE nombre='Ropa'), 15, 29.99, 5),
('Destornillador Set','Set de 6 destornilladores', (SELECT id FROM categories WHERE nombre='Herramientas'), 12, 19.99, 5),
('Lapiceros Caja','Caja con 20 lapiceros', (SELECT id FROM categories WHERE nombre='Oficina'), 30, 5.99, 10);
