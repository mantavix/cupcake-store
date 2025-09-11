<?php
session_start();
include_once '../config/database.php';

// Verificar se o usuário está logado
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$orderId = $_GET['order_id'] ?? 0;
$userId = $_SESSION['user_id'];

// Buscar dados do pedido
try {
    $order = fetchOne("SELECT * FROM orders WHERE id = ? AND user_id = ?", [$orderId, $userId]);
    
    if (!$order) {
        header('Location: ../index.php');
        exit;
    }
    
    // Buscar itens do pedido
    $orderItems = fetchData("
        SELECT oi.*, p.name, p.description
        FROM order_items oi
        JOIN products p ON oi.product_id = p.id
        WHERE oi.order_id = ?
    ", [$orderId]);
    
} catch (Exception $e) {
    header('Location: ../index.php');
    exit;
}

$success = '';
if (isset($_SESSION['order_success'])) {
    $success = $_SESSION['order_success'];
    unset($_SESSION['order_success']);
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pedido Confirmado - Cupcake Store</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="container">
            <div class="nav">
                <div class="logo">
                    <a href="../index.php" style="color: white; text-decoration: none;">
                        <h1><i class="fas fa-birthday-cake"></i> Cupcake Store</h1>
                    </a>
                </div>
                <nav class="nav-menu">
                    <ul>
                        <li><a href="../index.php">Início</a></li>
                        <li><a href="../index.php#products">Produtos</a></li>
                        <li><a href="profile.php">Perfil</a></li>
                        <li><a href="../actions/logout.php">Sair</a></li>
                    </ul>
                </nav>
            </div>
        </div>
    </header>

    <!-- Success Section -->
    <section class="success-section" style="padding: 4rem 0; min-height: 70vh;">
        <div class="container">
            <?php if ($success): ?>
                <div class="alert alert-success" style="text-align: center; font-size: 1.1rem;">
                    <?php echo htmlspecialchars($success); ?>
                </div>
            <?php endif; ?>
            
            <!-- Success Message -->
            <div style="text-align: center; margin-bottom: 3rem;">
                <div style="color: var(--success); font-size: 4rem; margin-bottom: 1rem;">
                    <i class="fas fa-check-circle"></i>
                </div>
                <h2 style="color: var(--primary-green); margin-bottom: 1rem;">Pedido Confirmado!</h2>
                <p style="font-size: 1.1rem; color: var(--text-light);">
                    Obrigado por escolher a Cupcake Store! Seu pedido foi recebido e está sendo preparado com muito carinho.
                </p>
            </div>
            
            <!-- Order Details -->
            <div style="max-width: 800px; margin: 0 auto;">
                <div style="background: var(--white); padding: 2rem; border-radius: 15px; box-shadow: 0 5px 15px var(--shadow); margin-bottom: 2rem;">
                    <h3 style="color: var(--primary-green); margin-bottom: 1.5rem; text-align: center;">
                        <i class="fas fa-receipt"></i> Detalhes do Pedido #<?php echo $orderId; ?>
                    </h3>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; margin-bottom: 2rem;">
                        <div>
                            <h4 style="color: var(--primary-green); margin-bottom: 0.5rem;">Data do Pedido</h4>
                            <p><?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?></p>
                        </div>
                        <div>
                            <h4 style="color: var(--primary-green); margin-bottom: 0.5rem;">Status</h4>
                            <p style="color: var(--success); font-weight: 600;">
                                <i class="fas fa-clock"></i> 
                                <?php 
                                $statusLabels = [
                                    'pending' => 'Aguardando Confirmação',
                                    'confirmed' => 'Confirmado',
                                    'preparing' => 'Preparando',
                                    'shipped' => 'Enviado',
                                    'delivered' => 'Entregue',
                                    'cancelled' => 'Cancelado'
                                ];
                                echo $statusLabels[$order['status']] ?? $order['status'];
                                ?>
                            </p>
                        </div>
                    </div>
                    
                    <div style="margin-bottom: 2rem;">
                        <h4 style="color: var(--primary-green); margin-bottom: 0.5rem;">Método de Pagamento</h4>
                        <p>
                            <?php 
                            $paymentLabels = [
                                'credit_card' => 'Cartão de Crédito',
                                'debit_card' => 'Cartão de Débito',
                                'pix' => 'PIX',
                                'cash_on_delivery' => 'Dinheiro na Entrega'
                            ];
                            echo $paymentLabels[$order['payment_method']] ?? $order['payment_method'];
                            ?>
                        </p>
                    </div>
                    
                    <div style="margin-bottom: 2rem;">
                        <h4 style="color: var(--primary-green); margin-bottom: 0.5rem;">Endereço de Entrega</h4>
                        <p><?php echo nl2br(htmlspecialchars($order['delivery_address'])); ?></p>
                    </div>
                </div>
                
                <!-- Order Items -->
                <div style="background: var(--white); padding: 2rem; border-radius: 15px; box-shadow: 0 5px 15px var(--shadow); margin-bottom: 2rem;">
                    <h3 style="color: var(--primary-green); margin-bottom: 1.5rem; text-align: center;">
                        <i class="fas fa-birthday-cake"></i> Itens do Pedido
                    </h3>
                    
                    <?php foreach ($orderItems as $item): ?>
                        <div style="display: flex; justify-content: space-between; align-items: center; padding: 1rem 0; border-bottom: 1px solid var(--beige);">
                            <div>
                                <div style="font-weight: 600; color: var(--primary-green);"><?php echo htmlspecialchars($item['name']); ?></div>
                                <div style="font-size: 0.9rem; color: var(--text-light);">
                                    <?php echo $item['quantity']; ?>x R$ <?php echo number_format($item['price'], 2, ',', '.'); ?>
                                </div>
                            </div>
                            <div style="font-weight: 600;">
                                R$ <?php echo number_format($item['price'] * $item['quantity'], 2, ',', '.'); ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    
                    <div style="text-align: right; margin-top: 1rem; padding-top: 1rem; border-top: 2px solid var(--primary-green);">
                        <div style="font-size: 1.2rem; font-weight: 700; color: var(--primary-green);">
                            Total: R$ <?php echo number_format($order['total_amount'], 2, ',', '.'); ?>
                        </div>
                    </div>
                </div>
                
                <!-- Next Steps -->
                <div style="background: linear-gradient(45deg, var(--light-green), var(--beige)); padding: 2rem; border-radius: 15px; margin-bottom: 2rem;">
                    <h3 style="color: var(--primary-green); margin-bottom: 1.5rem; text-align: center;">
                        <i class="fas fa-info-circle"></i> Próximos Passos
                    </h3>
                    
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem;">
                        <div style="text-align: center;">
                            <div style="color: var(--primary-green); font-size: 2rem; margin-bottom: 0.5rem;">
                                <i class="fas fa-envelope"></i>
                            </div>
                            <h4 style="margin-bottom: 0.5rem;">Confirmação por E-mail</h4>
                            <p style="font-size: 0.9rem;">Você receberá um e-mail com os detalhes do seu pedido</p>
                        </div>
                        
                        <div style="text-align: center;">
                            <div style="color: var(--primary-green); font-size: 2rem; margin-bottom: 0.5rem;">
                                <i class="fas fa-cookie-bite"></i>
                            </div>
                            <h4 style="margin-bottom: 0.5rem;">Preparação</h4>
                            <p style="font-size: 0.9rem;">Nossos chefs começarão a preparar seus cupcakes</p>
                        </div>
                        
                        <div style="text-align: center;">
                            <div style="color: var(--primary-green); font-size: 2rem; margin-bottom: 0.5rem;">
                                <i class="fas fa-truck"></i>
                            </div>
                            <h4 style="margin-bottom: 0.5rem;">Entrega</h4>
                            <p style="font-size: 0.9rem;">Entrega prevista em 30 a 50 minutos</p>
                        </div>
                    </div>
                </div>
                
                <!-- Action Buttons -->
                <div style="text-align: center;">
                    <a href="../index.php" class="btn btn-primary" style="margin-right: 1rem;">
                        <i class="fas fa-home"></i> Voltar ao Início
                    </a>
                    <a href="../index.php#products" class="btn btn-secondary">
                        <i class="fas fa-shopping-bag"></i> Continuar Comprando
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3>Cupcake Store</h3>
                    <p>Os melhores cupcakes artesanais da cidade!</p>
                </div>
                <div class="footer-section">
                    <h3>Contato</h3>
                    <p><i class="fas fa-phone"></i> (11) 99999-9999</p>
                    <p><i class="fas fa-envelope"></i> contato@cupcakestore.com</p>
                </div>
                <div class="footer-section">
                    <h3>Redes Sociais</h3>
                    <div class="social-links">
                        <a href="#"><i class="fab fa-facebook"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-whatsapp"></i></a>
                    </div>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2025 Cupcake Store - desenvolvido por Mantavix-Tech, todos os direitos reservados.</p>
            </div>
        </div>
    </footer>
</body>
</html>
