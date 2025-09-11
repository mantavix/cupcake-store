<?php
// Script para diagnosticar problemas com o carrinho

session_start();
include_once '../config/database.php';

echo "<h2>Diagnóstico do Sistema de Carrinho</h2>";

try {
    echo "<h3>✓ Conexão com Banco de Dados</h3>";
    echo "<p>Banco: cupcake_store conectado com sucesso</p>";
    
    // Verificar tabelas necessárias
    $tables = ['cart', 'products', 'users'];
    foreach ($tables as $table) {
        $result = $pdo->query("SHOW TABLES LIKE '$table'")->fetchAll();
        if (empty($result)) {
            echo "<p style='color: red;'>❌ Tabela '$table' não encontrada!</p>";
            echo "<p><a href='setup_database.php'>Clique aqui para criar o banco</a></p>";
            exit;
        }
        echo "<p>✓ Tabela '$table' encontrada</p>";
    }
    
    // Verificar sessão
    echo "<h3>Verificação de Sessão</h3>";
    if (isset($_SESSION['user_id'])) {
        echo "<p>✓ Usuário logado: ID " . $_SESSION['user_id'] . "</p>";
        echo "<p>✓ Nome: " . ($_SESSION['user_name'] ?? 'N/A') . "</p>";
        echo "<p>✓ Tipo: " . ($_SESSION['user_type'] ?? 'N/A') . "</p>";
        
        $userId = $_SESSION['user_id'];
        $isLoggedIn = true;
    } else {
        echo "<p style='color: red;'>❌ Usuário NÃO está logado</p>";
        echo "<p><strong>PROBLEMA IDENTIFICADO:</strong> Para adicionar itens ao carrinho, é necessário estar logado!</p>";
        echo "<p><a href='../pages/login.php'>Fazer Login</a> | <a href='../pages/register.php'>Cadastrar-se</a></p>";
        $isLoggedIn = false;
        $userId = null;
    }
    
    // Verificar produtos ativos
    echo "<h3>Verificação de Produtos</h3>";
    $products = fetchData("SELECT * FROM products WHERE is_active = 1 LIMIT 5");
    
    if (empty($products)) {
        echo "<p style='color: red;'>❌ Nenhum produto ativo encontrado!</p>";
        echo "<p><a href='update_images.php'>Adicionar produtos</a></p>";
    } else {
        echo "<p>✓ " . count($products) . " produtos ativos encontrados</p>";
        
        // Mostrar alguns produtos
        echo "<h4>Primeiros 5 produtos:</h4>";
        echo "<ul>";
        foreach ($products as $product) {
            echo "<li>ID: {$product['id']} - {$product['name']} - R$ " . number_format($product['price'], 2, ',', '.') . " (Estoque: {$product['stock_quantity']})</li>";
        }
        echo "</ul>";
    }
    
    // Se está logado, testar o carrinho
    if ($isLoggedIn && !empty($products)) {
        echo "<h3>Teste do Carrinho</h3>";
        
        // Verificar carrinho atual
        $cartItems = fetchData("SELECT c.*, p.name FROM cart c JOIN products p ON c.product_id = p.id WHERE c.user_id = ?", [$userId]);
        
        echo "<p>✓ Itens no carrinho: " . count($cartItems) . "</p>";
        
        if (!empty($cartItems)) {
            echo "<h4>Itens no carrinho:</h4>";
            echo "<ul>";
            $totalQuantity = 0;
            foreach ($cartItems as $item) {
                echo "<li>{$item['name']} - Quantidade: {$item['quantity']}</li>";
                $totalQuantity += $item['quantity'];
            }
            echo "</ul>";
            echo "<p><strong>Total de itens:</strong> $totalQuantity</p>";
        }
        
        // Testar API de adicionar ao carrinho (simulação)
        echo "<h4>Teste da API de Carrinho:</h4>";
        
        $testProduct = $products[0]; // Primeiro produto
        $testQuantity = 1;
        
        // Simular adição ao carrinho
        echo "<p>Testando adicionar produto: {$testProduct['name']} (ID: {$testProduct['id']})</p>";
        
        // Verificar se já existe no carrinho
        $existingItem = fetchOne("SELECT * FROM cart WHERE user_id = ? AND product_id = ?", [$userId, $testProduct['id']]);
        
        if ($existingItem) {
            echo "<p>✓ Produto já está no carrinho (Quantidade atual: {$existingItem['quantity']})</p>";
        } else {
            echo "<p>✓ Produto não está no carrinho ainda</p>";
        }
        
        // Verificar estoque
        if ($testProduct['stock_quantity'] >= $testQuantity) {
            echo "<p>✓ Estoque suficiente ({$testProduct['stock_quantity']} disponível)</p>";
        } else {
            echo "<p style='color: red;'>❌ Estoque insuficiente</p>";
        }
    }
    
    // Testar APIs diretamente
    echo "<h3>Teste das APIs</h3>";
    
    // Testar API de produtos
    echo "<h4>API de Produtos:</h4>";
    $apiUrl = 'http://localhost/cupcake-store/api/get_products.php';
    echo "<p><a href='$apiUrl' target='_blank'>Testar get_products.php</a></p>";
    
    // Testar API de contador do carrinho
    if ($isLoggedIn) {
        echo "<h4>API de Contador do Carrinho:</h4>";
        $countUrl = 'http://localhost/cupcake-store/api/get_cart_count.php';
        echo "<p><a href='$countUrl' target='_blank'>Testar get_cart_count.php</a></p>";
        
        // Simular chamada da API
        try {
            $result = fetchOne("SELECT SUM(quantity) as total FROM cart WHERE user_id = ?", [$userId]);
            $count = $result['total'] ?? 0;
            echo "<p>✓ Simulação API: $count itens no carrinho</p>";
        } catch (Exception $e) {
            echo "<p style='color: red;'>❌ Erro na simulação: " . $e->getMessage() . "</p>";
        }
    }
    
    // Verificar JavaScript (instruções)
    echo "<h3>Verificação do JavaScript</h3>";
    echo "<p>Para verificar se o JavaScript está funcionando:</p>";
    echo "<ol>";
    echo "<li>Vá para a <a href='../index.php' target='_blank'>página principal</a></li>";
    echo "<li>Abra o Console do navegador (F12)</li>";
    echo "<li>Procure por erros de JavaScript</li>";
    echo "<li>Tente adicionar um produto ao carrinho</li>";
    echo "<li>Verifique se aparece alert de sucesso ou erro</li>";
    echo "</ol>";
    
    // Status sobre o estoque
    echo "<h3>Sobre o Estoque</h3>";
    echo "<div style='background: #fff3cd; padding: 15px; border-radius: 5px; color: #856404;'>";
    echo "<strong>ℹ️ INFORMAÇÃO IMPORTANTE:</strong><br>";
    echo "O estoque NÃO diminui quando você adiciona itens ao carrinho.<br>";
    echo "O estoque só é reduzido quando a compra é finalizada com sucesso.<br>";
    echo "Isso é o comportamento correto para evitar 'reservas' desnecessárias.";
    echo "</div>";
    
    // Resultado final
    echo "<h3 style='color: green;'>Resultado do Diagnóstico</h3>";
    
    if ($isLoggedIn && !empty($products)) {
        echo "<div style='background: #d4edda; padding: 15px; border-radius: 5px; color: #155724;'>";
        echo "<strong>✓ SISTEMA PARECE OK!</strong><br>";
        echo "Usuário logado ✓<br>";
        echo "Produtos disponíveis ✓<br>";
        echo "Tabelas do banco ✓<br><br>";
        echo "<strong>Se ainda não funciona:</strong><br>";
        echo "1. Verifique o Console do navegador (F12)<br>";
        echo "2. Teste as APIs manualmente<br>";
        echo "3. Verifique se não há bloqueio de JavaScript";
        echo "</div>";
        
        echo "<h4>Testar carrinho:</h4>";
        echo "<p><a href='../index.php' style='background: #6B8E6B; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Ir para Loja</a></p>";
        
    } else if (!$isLoggedIn) {
        echo "<div style='background: #f8d7da; padding: 15px; border-radius: 5px; color: #721c24;'>";
        echo "<strong>❌ PROBLEMA PRINCIPAL: NÃO ESTÁ LOGADO!</strong><br>";
        echo "Para usar o carrinho, você precisa estar logado.<br>";
        echo "Faça login ou cadastre-se primeiro.";
        echo "</div>";
        
        echo "<h4>Fazer login:</h4>";
        echo "<p><a href='../pages/login.php' style='background: #6B8E6B; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Login</a> ";
        echo "<a href='../pages/register.php' style='background: #888888; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Cadastro</a></p>";
        
    } else if (empty($products)) {
        echo "<div style='background: #f8d7da; padding: 15px; border-radius: 5px; color: #721c24;'>";
        echo "<strong>❌ PROBLEMA: SEM PRODUTOS!</strong><br>";
        echo "Não há produtos ativos para adicionar ao carrinho.";
        echo "</div>";
        
        echo "<h4>Adicionar produtos:</h4>";
        echo "<p><a href='update_images.php' style='background: #dc3545; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Atualizar Produtos</a></p>";
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
    <title>Teste de Carrinho - Cupcake Store</title>
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
        ul, ol {
            background: white;
            padding: 15px;
            border-radius: 5px;
            margin: 5px 0;
        }
        div {
            margin: 10px 0;
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
