-- Criar banco de dados
CREATE DATABASE IF NOT EXISTS cupcake_store CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE cupcake_store;

-- Tabela de usuários
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    username VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    cpf VARCHAR(14) UNIQUE NOT NULL,
    phone VARCHAR(20) NOT NULL,
    address TEXT NOT NULL,
    city VARCHAR(100) NOT NULL,
    state VARCHAR(50) NOT NULL,
    zip_code VARCHAR(10) NOT NULL,
    user_type ENUM('customer', 'admin') DEFAULT 'customer',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabela de produtos
CREATE TABLE products (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    image VARCHAR(255) NOT NULL,
    stock_quantity INT NOT NULL DEFAULT 0,
    category VARCHAR(100) DEFAULT 'cupcake',
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabela do carrinho
CREATE TABLE cart (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_product (user_id, product_id)
);

-- Tabela de pedidos
CREATE TABLE orders (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    total_amount DECIMAL(10,2) NOT NULL,
    status ENUM('pending', 'confirmed', 'preparing', 'shipped', 'delivered', 'cancelled') DEFAULT 'pending',
    payment_method VARCHAR(50) NOT NULL,
    delivery_address TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Tabela de itens do pedido
CREATE TABLE order_items (
    id INT PRIMARY KEY AUTO_INCREMENT,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- Inserir usuário admin padrão
-- Senha: admin123 (hash será gerado dinamicamente)
-- Para login use: usuário "admin" e senha "admin123"
INSERT INTO users (name, email, username, password, cpf, phone, address, city, state, zip_code, user_type) 
VALUES ('Administrador', 'admin@cupcakestore.com', 'admin', '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm', '000.000.000-00', '(11) 99999-9999', 'Rua Principal, 123', 'São Paulo', 'SP', '01000-000', 'admin');

-- Inserir produtos exemplo com imagens reais
INSERT INTO products (name, description, price, image, stock_quantity) VALUES
('Cupcake de Chocolate', 'Delicioso cupcake de chocolate com cobertura cremosa e raspas de chocolate', 8.50, 'cupcake chocolate.webp', 50),
('Cupcake de Duplo Chocolate', 'Intenso sabor chocolate com dupla cobertura e ganache', 9.50, 'cupcake duplochocolate.jpg', 45),
('Cupcake de Chocolate com Avelã', 'Combinação perfeita de chocolate e avelã torrada', 10.00, 'cupcake chocolate com avelã.webp', 30),
('Cupcake de Morango', 'Cupcake sabor morango com pedaços da fruta e chantilly', 8.00, 'cupcake morango.jpg', 40),
('Cupcake de Limão', 'Refrescante cupcake de limão siciliano com cobertura cítrica', 7.75, 'cupcake de limão.jpg', 35),
('Cupcake de Avelã', 'Massa aerada com avelãs trituradas e cobertura especial', 8.75, 'cupcake avelã.jpg', 25),
('Cupcake de Duplo Creme', 'Cremoso cupcake com recheio e cobertura de creme', 9.25, 'cupcake de duplo creme.jpg', 30),
('Cupcake Oreo', 'Cupcake com biscoitos Oreo triturados e creme especial', 9.00, 'cupcake oreo.jpg', 35),
('Cupcake Confeitado', 'Elegante cupcake decorado com confeitos especiais', 10.50, 'cupcake confeitado.jpg', 20),
('Cupcake Festivo', 'Cupcake colorido perfeito para comemorações', 8.90, 'cupcake festivo.jpg', 40),
('Cupcake Diet', 'Versão sem açúcar, igualmente deliciosa e saudável', 9.75, 'cupcake diet.jpg', 15);
