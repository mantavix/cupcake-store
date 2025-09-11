<?php
session_start();
header('Content-Type: application/json');
include_once '../config/database.php';

// Verificar se é admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    echo json_encode([
        'success' => false,
        'message' => 'Acesso negado'
    ]);
    exit;
}

$orderId = $_GET['id'] ?? 0;

if (!$orderId) {
    echo json_encode([
        'success' => false,
        'message' => 'ID do pedido não informado'
    ]);
    exit;
}

try {
    // Buscar dados do pedido
    $order = fetchOne("
        SELECT o.*, u.name as customer_name, u.email as customer_email, 
               u.phone as customer_phone, u.cpf as customer_cpf
        FROM orders o
        JOIN users u ON o.user_id = u.id
        WHERE o.id = ?
    ", [$orderId]);
    
    if (!$order) {
        echo json_encode([
            'success' => false,
            'message' => 'Pedido não encontrado'
        ]);
        exit;
    }
    
    // Buscar itens do pedido
    $orderItems = fetchData("
        SELECT oi.*, p.name as product_name, p.description as product_description
        FROM order_items oi
        JOIN products p ON oi.product_id = p.id
        WHERE oi.order_id = ?
    ", [$orderId]);
    
    // Status labels
    $statusLabels = [
        'pending' => 'Aguardando Confirmação',
        'confirmed' => 'Confirmado',
        'preparing' => 'Preparando',
        'shipped' => 'Enviado',
        'delivered' => 'Entregue',
        'cancelled' => 'Cancelado'
    ];
    
    // Payment method labels
    $paymentLabels = [
        'credit_card' => 'Cartão de Crédito',
        'debit_card' => 'Cartão de Débito',
        'pix' => 'PIX',
        'cash_on_delivery' => 'Dinheiro na Entrega'
    ];
    
    // Gerar HTML
    $html = '
    <h3 style="color: var(--primary-green); margin-bottom: 1.5rem; text-align: center;">
        <i class="fas fa-receipt"></i> Detalhes do Pedido #' . $order['id'] . '
    </h3>
    
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 2rem;">
        <div>
            <h4 style="color: var(--primary-green); margin-bottom: 0.5rem;">Cliente</h4>
            <p><strong>Nome:</strong> ' . htmlspecialchars($order['customer_name']) . '</p>
            <p><strong>E-mail:</strong> ' . htmlspecialchars($order['customer_email']) . '</p>
            <p><strong>Telefone:</strong> ' . htmlspecialchars($order['customer_phone']) . '</p>
            <p><strong>CPF:</strong> ' . htmlspecialchars($order['customer_cpf']) . '</p>
        </div>
        
        <div>
            <h4 style="color: var(--primary-green); margin-bottom: 0.5rem;">Pedido</h4>
            <p><strong>Data:</strong> ' . date('d/m/Y H:i', strtotime($order['created_at'])) . '</p>
            <p><strong>Status:</strong> ' . ($statusLabels[$order['status']] ?? $order['status']) . '</p>
            <p><strong>Pagamento:</strong> ' . ($paymentLabels[$order['payment_method']] ?? $order['payment_method']) . '</p>
            <p><strong>Total:</strong> <span style="color: var(--primary-green); font-weight: bold;">R$ ' . number_format($order['total_amount'], 2, ',', '.') . '</span></p>
        </div>
    </div>
    
    <div style="margin-bottom: 2rem;">
        <h4 style="color: var(--primary-green); margin-bottom: 0.5rem;">Endereço de Entrega</h4>
        <p>' . nl2br(htmlspecialchars($order['delivery_address'])) . '</p>
    </div>
    
    <div>
        <h4 style="color: var(--primary-green); margin-bottom: 1rem;">Itens do Pedido</h4>
        <div style="border: 1px solid var(--beige); border-radius: 8px; overflow: hidden;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead style="background-color: var(--beige);">
                    <tr>
                        <th style="padding: 0.75rem; text-align: left;">Produto</th>
                        <th style="padding: 0.75rem; text-align: center;">Qtd</th>
                        <th style="padding: 0.75rem; text-align: right;">Preço Unit.</th>
                        <th style="padding: 0.75rem; text-align: right;">Subtotal</th>
                    </tr>
                </thead>
                <tbody>';
    
    foreach ($orderItems as $item) {
        $subtotal = $item['quantity'] * $item['price'];
        $html .= '
                    <tr style="border-bottom: 1px solid var(--beige);">
                        <td style="padding: 0.75rem;">
                            <div style="font-weight: 600;">' . htmlspecialchars($item['product_name']) . '</div>
                            <div style="font-size: 0.9rem; color: var(--text-light);">' . htmlspecialchars($item['product_description']) . '</div>
                        </td>
                        <td style="padding: 0.75rem; text-align: center;">' . $item['quantity'] . '</td>
                        <td style="padding: 0.75rem; text-align: right;">R$ ' . number_format($item['price'], 2, ',', '.') . '</td>
                        <td style="padding: 0.75rem; text-align: right; font-weight: 600;">R$ ' . number_format($subtotal, 2, ',', '.') . '</td>
                    </tr>';
    }
    
    $html .= '
                </tbody>
            </table>
        </div>
    </div>';
    
    echo json_encode([
        'success' => true,
        'html' => $html
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Erro ao buscar detalhes do pedido: ' . $e->getMessage()
    ]);
}
?>
