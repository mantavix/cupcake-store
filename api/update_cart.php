<?php
session_start();
header('Content-Type: application/json');
include_once '../config/database.php';

// Verificar se o usuário está logado
if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Você precisa estar logado'
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
    
    executeQuery("UPDATE cart SET quantity = ? WHERE user_id = ? AND product_id = ?", 
                [$quantity, $userId, $productId]);
    
    echo json_encode([
        'success' => true,
        'message' => 'Quantidade atualizada com sucesso'
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Erro ao atualizar quantidade: ' . $e->getMessage()
    ]);
}
?>
