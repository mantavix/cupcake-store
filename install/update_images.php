<?php
// Script para atualizar produtos existentes com as novas imagens

// Configurações do banco
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'cupcake_store';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$database", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<h2>Atualizando produtos com imagens reais</h2>";
    
    // Primeiro, limpar produtos existentes
    $pdo->exec("DELETE FROM products");
    echo "<p>✓ Produtos antigos removidos</p>";
    
    // Inserir novos produtos com imagens reais
    $products = [
        ['Cupcake de Chocolate', 'Delicioso cupcake de chocolate com cobertura cremosa e raspas de chocolate', 8.50, 'cupcake chocolate.webp', 50],
        ['Cupcake de Duplo Chocolate', 'Intenso sabor chocolate com dupla cobertura e ganache', 9.50, 'cupcake duplochocolate.jpg', 45],
        ['Cupcake de Chocolate com Avelã', 'Combinação perfeita de chocolate e avelã torrada', 10.00, 'cupcake chocolate com avelã.webp', 30],
        ['Cupcake de Morango', 'Cupcake sabor morango com pedaços da fruta e chantilly', 8.00, 'cupcake morango.jpg', 40],
        ['Cupcake de Limão', 'Refrescante cupcake de limão siciliano com cobertura cítrica', 7.75, 'cupcake de limão.jpg', 35],
        ['Cupcake de Avelã', 'Massa aerada com avelãs trituradas e cobertura especial', 8.75, 'cupcake avelã.jpg', 25],
        ['Cupcake de Duplo Creme', 'Cremoso cupcake com recheio e cobertura de creme', 9.25, 'cupcake de duplo creme.jpg', 30],
        ['Cupcake Oreo', 'Cupcake com biscoitos Oreo triturados e creme especial', 9.00, 'cupcake oreo.jpg', 35],
        ['Cupcake Confeitado', 'Elegante cupcake decorado com confeitos especiais', 10.50, 'cupcake confeitado.jpg', 20],
        ['Cupcake Festivo', 'Cupcake colorido perfeito para comemorações', 8.90, 'cupcake festivo.jpg', 40],
        ['Cupcake Diet', 'Versão sem açúcar, igualmente deliciosa e saudável', 9.75, 'cupcake diet.jpg', 15]
    ];
    
    $stmt = $pdo->prepare("INSERT INTO products (name, description, price, image, stock_quantity) VALUES (?, ?, ?, ?, ?)");
    
    foreach ($products as $product) {
        $stmt->execute($product);
        echo "<p>✓ Produto adicionado: {$product[0]}</p>";
    }
    
    // Corrigir senha do admin
    echo "<p>✓ Corrigindo senha do administrador...</p>";
    $adminPassword = password_hash('admin123', PASSWORD_DEFAULT);
    $pdo->exec("UPDATE users SET password = '$adminPassword' WHERE username = 'admin'");
    echo "<p>✓ Senha do admin atualizada</p>";
    
    echo "<h3 style='color: green;'>✓ Atualização concluída com sucesso!</h3>";
    echo "<h4>Novos produtos:</h4>";
    echo "<ul>";
    echo "<li>11 cupcakes com imagens reais</li>";
    echo "<li>Descrições detalhadas</li>";
    echo "<li>Preços variados</li>";
    echo "<li>Estoque diferenciado</li>";
    echo "<li><strong>Login admin corrigido: admin / admin123</strong></li>";
    echo "</ul>";
    
    echo "<p><a href='../index.php' style='background: #6B8E6B; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Ver Loja Atualizada</a></p>";
    echo "<p><a href='../admin/products.php' style='background: #888888; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Gerenciar Produtos</a></p>";
    
} catch (PDOException $e) {
    echo "<h3 style='color: red;'>Erro ao atualizar:</h3>";
    echo "<p>" . $e->getMessage() . "</p>";
    echo "<h4>Possíveis soluções:</h4>";
    echo "<ul>";
    echo "<li>Execute primeiro o <a href='setup_database.php'>instalador principal</a></li>";
    echo "<li>Verifique se o MySQL está rodando</li>";
    echo "<li>Confirme as credenciais do banco</li>";
    echo "</ul>";
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Atualização - Cupcake Store</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background-color: #f5f5f5;
        }
        h2 {
            color: #6B8E6B;
            text-align: center;
        }
        p {
            background: white;
            padding: 10px;
            border-radius: 5px;
            margin: 5px 0;
        }
        a {
            text-decoration: none;
            margin: 5px;
            display: inline-block;
        }
    </style>
</head>
<body>
    <!-- O conteúdo PHP será exibido aqui -->
</body>
</html>
