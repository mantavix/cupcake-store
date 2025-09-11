<?php
session_start();
header('Content-Type: application/json');
include_once '../config/database.php';

// Verificar se o usuário está logado
if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Você precisa estar logado para adicionar itens ao carrinho'
    ]);
    exit;
}

// Obter dados da requisição
$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['product_id']) || !isset($input['quantity'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Dados incompletos'
    ]);
    exit;
}

$userId = $_SESSION['user_id'];
$productId = (int)$input['product_id'];
$quantity = (int)$input['quantity'];

if ($quantity <= 0) {
    echo json_encode([
        'success' => false,
        'message' => 'Quantidade deve ser maior que zero'
    ]);
    exit;
}

try {
    // Verificar se o produto existe e tem estoque suficiente
    $product = fetchOne("SELECT * FROM products WHERE id = ? AND is_active = 1", [$productId]);
    
    if (!$product) {
        echo json_encode([
            'success' => false,
            'message' => 'Produto não encontrado'
        ]);
        exit;
    }
    
    if ($product['stock_quantity'] < $quantity) {
        echo json_encode([
            'success' => false,
            'message' => 'Estoque insuficiente. Disponível: ' . $product['stock_quantity'] . ' unidades'
        ]);
        exit;
    }
    
    // Verificar se o item já está no carrinho
    $existingItem = fetchOne("SELECT * FROM cart WHERE user_id = ? AND product_id = ?", [$userId, $productId]);
    
    if ($existingItem) {
        // Atualizar quantidade
        $newQuantity = $existingItem['quantity'] + $quantity;
        
        if ($product['stock_quantity'] < $newQuantity) {
            echo json_encode([
                'success' => false,
                'message' => 'Estoque insuficiente. Você já tem ' . $existingItem['quantity'] . ' unidades no carrinho'
            ]);
            exit;
        }
        
        executeQuery("UPDATE cart SET quantity = ? WHERE user_id = ? AND product_id = ?", 
                    [$newQuantity, $userId, $productId]);
    } else {
        // Adicionar novo item
        executeQuery("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)", 
                    [$userId, $productId, $quantity]);
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'Produto adicionado ao carrinho com sucesso'
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Erro ao adicionar ao carrinho: ' . $e->getMessage()
    ]);
}
?>
