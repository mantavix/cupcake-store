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
    executeQuery("DELETE FROM cart WHERE user_id = ?", [$userId]);
    
    echo json_encode([
        'success' => true,
        'message' => 'Carrinho esvaziado com sucesso'
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Erro ao esvaziar carrinho: ' . $e->getMessage()
    ]);
}
?>
