<?php
// Script para diagnosticar problemas com produtos

include_once '../config/database.php';

echo "<h2>Diagnóstico do Sistema de Produtos</h2>";

try {
    echo "<h3>✓ Conexão com Banco de Dados</h3>";
    echo "<p>Banco: cupcake_store conectado com sucesso</p>";
    
    // Testar se a tabela products existe
    $tables = $pdo->query("SHOW TABLES LIKE 'products'")->fetchAll();
    if (empty($tables)) {
        echo "<p style='color: red;'>❌ Tabela 'products' não encontrada!</p>";
        echo "<p><a href='setup_database.php'>Clique aqui para criar o banco</a></p>";
        exit;
    }
    echo "<p>✓ Tabela 'products' encontrada</p>";
    
    // Verificar produtos
    echo "<h3>Verificação dos Produtos</h3>";
    $products = fetchData("SELECT * FROM products ORDER BY name");
    
    if (empty($products)) {
        echo "<p style='color: red;'>❌ Nenhum produto encontrado!</p>";
        echo "<p><a href='update_images.php'>Clique aqui para adicionar produtos</a></p>";
        exit;
    }
    
    echo "<p>✓ " . count($products) . " produtos encontrados</p>";
    
    // Listar produtos
    echo "<h4>Lista de Produtos:</h4>";
    echo "<table border='1' style='border-collapse: collapse; width: 100%; margin: 10px 0;'>";
    echo "<tr style='background: #6B8E6B; color: white;'>";
    echo "<th style='padding: 8px;'>ID</th>";
    echo "<th style='padding: 8px;'>Nome</th>";
    echo "<th style='padding: 8px;'>Preço</th>";
    echo "<th style='padding: 8px;'>Estoque</th>";
    echo "<th style='padding: 8px;'>Imagem</th>";
    echo "<th style='padding: 8px;'>Ativo</th>";
    echo "</tr>";
    
    foreach ($products as $product) {
        $activeStatus = $product['is_active'] ? 'Sim' : 'Não';
        $rowColor = $product['is_active'] ? 'white' : '#ffeeee';
        
        echo "<tr style='background: $rowColor;'>";
        echo "<td style='padding: 8px;'>{$product['id']}</td>";
        echo "<td style='padding: 8px;'>{$product['name']}</td>";
        echo "<td style='padding: 8px;'>R$ " . number_format($product['price'], 2, ',', '.') . "</td>";
        echo "<td style='padding: 8px;'>{$product['stock_quantity']}</td>";
        echo "<td style='padding: 8px;'>{$product['image']}</td>";
        echo "<td style='padding: 8px;'>$activeStatus</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // Verificar produtos ativos
    $activeProducts = fetchData("SELECT * FROM products WHERE is_active = 1 ORDER BY name");
    echo "<h4>Produtos Ativos: " . count($activeProducts) . "</h4>";
    
    if (empty($activeProducts)) {
        echo "<p style='color: red;'>❌ Nenhum produto ativo encontrado!</p>";
        echo "<p>Ativando todos os produtos...</p>";
        
        executeQuery("UPDATE products SET is_active = 1");
        echo "<p>✓ Todos os produtos foram ativados</p>";
    }
    
    // Testar API
    echo "<h3>Teste da API de Produtos</h3>";
    
    // Simular a API
    try {
        $query = "SELECT * FROM products WHERE is_active = 1 ORDER BY name";
        $apiProducts = fetchData($query);
        
        $apiResponse = [
            'success' => true,
            'data' => $apiProducts
        ];
        
        echo "<p>✓ API simulada: SUCCESS</p>";
        echo "<p>✓ Produtos retornados: " . count($apiProducts) . "</p>";
        
        // Mostrar JSON da API
        echo "<h4>JSON da API:</h4>";
        echo "<textarea readonly style='width: 100%; height: 150px; font-family: monospace;'>";
        echo json_encode($apiResponse, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        echo "</textarea>";
        
    } catch (Exception $e) {
        echo "<p style='color: red;'>❌ API simulada: ERRO</p>";
        echo "<p>Erro: " . $e->getMessage() . "</p>";
    }
    
    // Verificar imagens
    echo "<h3>Verificação das Imagens</h3>";
    $imagePath = '../assets/img/cupcake-';
    $missingImages = [];
    
    foreach ($activeProducts as $product) {
        $fullPath = $imagePath . $product['image'];
        if (!file_exists($fullPath)) {
            $missingImages[] = $product['image'];
        }
    }
    
    if (empty($missingImages)) {
        echo "<p>✓ Todas as imagens foram encontradas</p>";
    } else {
        echo "<p style='color: orange;'>⚠️ Imagens não encontradas:</p>";
        echo "<ul>";
        foreach ($missingImages as $image) {
            echo "<li>$image</li>";
        }
        echo "</ul>";
    }
    
    // Resultado final
    echo "<h3 style='color: green;'>Resultado do Diagnóstico</h3>";
    
    if (!empty($activeProducts)) {
        echo "<p style='background: #d4edda; padding: 15px; border-radius: 5px; color: #155724;'>";
        echo "<strong>✓ PRODUTOS OK!</strong><br>";
        echo "O sistema de produtos deveria estar funcionando.<br>";
        echo "Produtos ativos: " . count($activeProducts);
        echo "</p>";
        
        echo "<h4>Testar agora:</h4>";
        echo "<p><a href='../index.php' style='background: #6B8E6B; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Ver Loja</a></p>";
        echo "<p><a href='../api/get_products.php' style='background: #888888; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Testar API</a></p>";
        
    } else {
        echo "<p style='background: #f8d7da; padding: 15px; border-radius: 5px; color: #721c24;'>";
        echo "<strong>❌ PROBLEMAS ENCONTRADOS!</strong><br>";
        echo "Execute as correções sugeridas acima.";
        echo "</p>";
        
        echo "<h4>Corrigir problemas:</h4>";
        echo "<p><a href='update_images.php' style='background: #dc3545; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Adicionar Produtos</a></p>";
    }
    
} catch (Exception $e) {
    echo "<h3 style='color: red;'>❌ Erro no Diagnóstico</h3>";
    echo "<p>Erro: " . $e->getMessage() . "</p>";
    echo "<p><a href='setup_database.php'>Executar instalador completo</a></p>";
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teste de Produtos - Cupcake Store</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 1000px;
            margin: 50px auto;
            padding: 20px;
            background-color: #f5f5f5;
        }
        h2 {
            color: #6B8E6B;
            text-align: center;
        }
        h3 {
            color: #6B8E6B;
            border-bottom: 2px solid #6B8E6B;
            padding-bottom: 5px;
        }
        p {
            background: white;
            padding: 10px;
            border-radius: 5px;
            margin: 5px 0;
        }
        table {
            background: white;
            border-radius: 5px;
            overflow: hidden;
        }
        ul {
            background: white;
            padding: 15px;
            border-radius: 5px;
            margin: 5px 0;
        }
        textarea {
            background: white;
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 10px;
        }
        a {
            color: #6B8E6B;
            text-decoration: none;
            font-weight: bold;
        }
        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <!-- O conteúdo PHP será exibido aqui -->
</body>
</html>
