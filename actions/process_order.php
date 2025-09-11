<?php
session_start();
include_once '../config/database.php';

// Verificar se o usuário está logado
if (!isset($_SESSION['user_id'])) {
    header('Location: ../pages/login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../pages/checkout.php');
    exit;
}

$userId = $_SESSION['user_id'];
$deliveryAddress = trim($_POST['delivery_address']);
$paymentMethod = $_POST['payment_method'] ?? '';
$orderNotes = trim($_POST['order_notes'] ?? '');

// Validações
if (empty($deliveryAddress) || empty($paymentMethod)) {
    $_SESSION['checkout_error'] = 'Todos os campos obrigatórios devem ser preenchidos';
    header('Location: ../pages/checkout.php');
    exit;
}

try {
    // Iniciar transação
    $pdo->beginTransaction();
    
    // Buscar itens do carrinho
    $cartItems = fetchData("
        SELECT c.*, p.name, p.price, p.stock_quantity
        FROM cart c 
        JOIN products p ON c.product_id = p.id 
        WHERE c.user_id = ? AND p.is_active = 1
    ", [$userId]);
    
    if (empty($cartItems)) {
        throw new Exception('Carrinho vazio');
    }
    
    // Verificar estoque e calcular total
    $total = 0;
    foreach ($cartItems as $item) {
        if ($item['stock_quantity'] < $item['quantity']) {
            throw new Exception('Estoque insuficiente para ' . $item['name']);
        }
        $total += $item['price'] * $item['quantity'];
    }
    
    // Criar pedido
    $stmt = executeQuery("INSERT INTO orders (user_id, total_amount, status, payment_method, delivery_address) VALUES (?, ?, 'pending', ?, ?)", 
                        [$userId, $total, $paymentMethod, $deliveryAddress . ($orderNotes ? '\n\nObservações: ' . $orderNotes : '')]);
    
    $orderId = $pdo->lastInsertId();
    
    // Adicionar itens do pedido e atualizar estoque
    foreach ($cartItems as $item) {
        // Adicionar item do pedido
        executeQuery("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)", 
                    [$orderId, $item['product_id'], $item['quantity'], $item['price']]);
        
        // Atualizar estoque
        executeQuery("UPDATE products SET stock_quantity = stock_quantity - ? WHERE id = ?", 
                    [$item['quantity'], $item['product_id']]);
    }
    
    // Limpar carrinho
    executeQuery("DELETE FROM cart WHERE user_id = ?", [$userId]);
    
    // Confirmar transação
    $pdo->commit();
    
    // Simular processamento do pagamento
    $paymentStatus = 'success'; // Em uma implementação real, aqui seria a integração com gateway de pagamento
    
    if ($paymentStatus === 'success') {
        // Atualizar status do pedido
        executeQuery("UPDATE orders SET status = 'confirmed' WHERE id = ?", [$orderId]);
        
        $_SESSION['order_success'] = "Pedido #{$orderId} realizado com sucesso! Em breve você receberá uma confirmação por e-mail.";
        header('Location: ../pages/order_success.php?order_id=' . $orderId);
    } else {
        throw new Exception('Erro no processamento do pagamento');
    }
    
} catch (Exception $e) {
    // Rollback em caso de erro
    if ($pdo->inTransaction()) {
        $pdo->rollback();
    }
    
    $_SESSION['checkout_error'] = 'Erro ao processar pedido: ' . $e->getMessage();
    header('Location: ../pages/checkout.php');
}
exit;
?>
