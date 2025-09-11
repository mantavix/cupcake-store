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

if (!isset($input['product_id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'ID do produto não informado'
    ]);
    exit;
}

$userId = $_SESSION['user_id'];
$productId = (int)$input['product_id'];

try {
    executeQuery("DELETE FROM cart WHERE user_id = ? AND product_id = ?", [$userId, $productId]);
    
    echo json_encode([
        'success' => true,
        'message' => 'Item removido do carrinho com sucesso'
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Erro ao remover item do carrinho: ' . $e->getMessage()
    ]);
}
?>
