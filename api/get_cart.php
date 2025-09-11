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

$userId = $_SESSION['user_id'];

try {
    $cartItems = fetchData("
        SELECT c.*, p.name, p.description, p.price, p.image, p.stock_quantity,
               (c.quantity * p.price) as subtotal
        FROM cart c 
        JOIN products p ON c.product_id = p.id 
        WHERE c.user_id = ? AND p.is_active = 1
        ORDER BY c.created_at DESC
    ", [$userId]);
    
    $total = 0;
    foreach ($cartItems as $item) {
        $total += $item['subtotal'];
    }
    
    echo json_encode([
        'success' => true,
        'items' => $cartItems,
        'total' => $total,
        'count' => count($cartItems)
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Erro ao obter itens do carrinho: ' . $e->getMessage()
    ]);
}
?>
