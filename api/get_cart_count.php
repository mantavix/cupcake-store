<?php
session_start();
header('Content-Type: application/json');
include_once '../config/database.php';

try {
    $count = 0;
    
    if (isset($_SESSION['user_id'])) {
        $result = fetchOne("SELECT SUM(quantity) as total FROM cart WHERE user_id = ?", [$_SESSION['user_id']]);
        $count = $result['total'] ?? 0;
    }
    
    echo json_encode([
        'success' => true,
        'count' => (int)$count
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Erro ao obter contador do carrinho: ' . $e->getMessage(),
        'count' => 0
    ]);
}
?>
