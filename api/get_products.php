<?php
header('Content-Type: application/json');
include_once '../config/database.php';

try {
    $query = "SELECT * FROM products WHERE is_active = 1 ORDER BY name";
    $products = fetchData($query);
    
    echo json_encode([
        'success' => true,
        'data' => $products
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Erro ao buscar produtos: ' . $e->getMessage()
    ]);
}
?>
