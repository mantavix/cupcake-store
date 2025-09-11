<?php
session_start();
include_once '../config/database.php';

// Verificar se o usuário está logado
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$userId = $_SESSION['user_id'];

// Buscar dados do usuário
$user = fetchOne("SELECT * FROM users WHERE id = ?", [$userId]);

// Buscar itens do carrinho
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
} catch (Exception $e) {
    $cartItems = [];
    $total = 0;
}

// Se o carrinho estiver vazio, redirecionar
if (empty($cartItems)) {
    header('Location: cart.php');
    exit;
}

$error = '';
$success = '';
if (isset($_SESSION['checkout_error'])) {
    $error = $_SESSION['checkout_error'];
    unset($_SESSION['checkout_error']);
}
if (isset($_SESSION['checkout_success'])) {
    $success = $_SESSION['checkout_success'];
    unset($_SESSION['checkout_success']);
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Finalizar Compra - Cupcake Store</title>
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
                    <a href="../index.php" style="color: white; text-decoration: none; display: flex; align-items: center;">
                        <img src="../assets/img/logo/logo_cupcake-store.jpg" alt="Cupcake Store" style="height: 50px; margin-right: 10px; border-radius: 8px;">
                        <h1 style="margin: 0;">Cupcake Store</h1>
                    </a>
                </div>
                <nav class="nav-menu">
                    <ul>
                        <li><a href="../index.php">Início</a></li>
                        <li><a href="cart.php">Carrinho</a></li>
                        <li><a href="profile.php">Perfil</a></li>
                        <li><a href="../actions/logout.php">Sair</a></li>
                    </ul>
                </nav>
            </div>
        </div>
    </header>

    <!-- Checkout Section -->
    <section class="checkout-section" style="padding: 4rem 0;">
        <div class="container">
            <h2 class="section-title">
                <i class="fas fa-credit-card"></i> Finalizar Compra
            </h2>
            
            <?php if ($error): ?>
                <div class="alert alert-error">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success">
                    <?php echo htmlspecialchars($success); ?>
                </div>
            <?php endif; ?>
            
            <div style="display: grid; grid-template-columns: 1fr 400px; gap: 3rem;">
                <!-- Checkout Form -->
                <div class="checkout-form">
                    <form action="../actions/process_order.php" method="POST" id="checkoutForm">
                        <!-- Endereço de Entrega -->
                        <div class="form-section" style="background: var(--white); padding: 2rem; border-radius: 15px; margin-bottom: 2rem; box-shadow: 0 5px 15px var(--shadow);">
                            <h3 style="color: var(--primary-green); margin-bottom: 1.5rem;">
                                <i class="fas fa-map-marker-alt"></i> Endereço de Entrega
                            </h3>
                            
                            <div class="form-group">
                                <label for="delivery_address">Endereço Completo *</label>
                                <textarea id="delivery_address" name="delivery_address" required rows="3"><?php echo htmlspecialchars($user['address'] . ', ' . $user['city'] . '/' . $user['state'] . ' - CEP: ' . $user['zip_code']); ?></textarea>
                                <small style="color: var(--text-light);">Confirme ou edite seu endereço de entrega</small>
                            </div>
                        </div>
                        
                        <!-- Método de Pagamento -->
                        <div class="form-section" style="background: var(--white); padding: 2rem; border-radius: 15px; margin-bottom: 2rem; box-shadow: 0 5px 15px var(--shadow);">
                            <h3 style="color: var(--primary-green); margin-bottom: 1.5rem;">
                                <i class="fas fa-credit-card"></i> Método de Pagamento
                            </h3>
                            
                            <div style="display: grid; gap: 1rem;">
                                <label class="payment-option" style="display: flex; align-items: center; padding: 1rem; border: 2px solid var(--beige); border-radius: 8px; cursor: pointer;">
                                    <input type="radio" name="payment_method" value="credit_card" required style="margin-right: 1rem;">
                                    <i class="fas fa-credit-card" style="margin-right: 0.5rem; color: var(--primary-green);"></i>
                                    <span>Cartão de Crédito</span>
                                </label>
                                
                                <label class="payment-option" style="display: flex; align-items: center; padding: 1rem; border: 2px solid var(--beige); border-radius: 8px; cursor: pointer;">
                                    <input type="radio" name="payment_method" value="debit_card" required style="margin-right: 1rem;">
                                    <i class="fas fa-money-check-alt" style="margin-right: 0.5rem; color: var(--primary-green);"></i>
                                    <span>Cartão de Débito</span>
                                </label>
                                
                                <label class="payment-option" style="display: flex; align-items: center; padding: 1rem; border: 2px solid var(--beige); border-radius: 8px; cursor: pointer;">
                                    <input type="radio" name="payment_method" value="pix" required style="margin-right: 1rem;">
                                    <i class="fas fa-qrcode" style="margin-right: 0.5rem; color: var(--primary-green);"></i>
                                    <span>PIX</span>
                                </label>
                                
                                <label class="payment-option" style="display: flex; align-items: center; padding: 1rem; border: 2px solid var(--beige); border-radius: 8px; cursor: pointer;">
                                    <input type="radio" name="payment_method" value="cash_on_delivery" required style="margin-right: 1rem;">
                                    <i class="fas fa-money-bill-wave" style="margin-right: 0.5rem; color: var(--primary-green);"></i>
                                    <span>Dinheiro na Entrega</span>
                                </label>
                            </div>
                        </div>
                        
                        <!-- Observações -->
                        <div class="form-section" style="background: var(--white); padding: 2rem; border-radius: 15px; margin-bottom: 2rem; box-shadow: 0 5px 15px var(--shadow);">
                            <h3 style="color: var(--primary-green); margin-bottom: 1.5rem;">
                                <i class="fas fa-comment"></i> Observações (Opcional)
                            </h3>
                            
                            <div class="form-group">
                                <textarea name="order_notes" rows="3" placeholder="Alguma observação especial para seu pedido?"></textarea>
                            </div>
                        </div>
                        
                        <div style="text-align: center;">
                            <button type="submit" class="btn btn-primary" style="font-size: 1.2rem; padding: 1rem 3rem;">
                                <i class="fas fa-check"></i> Confirmar Pedido
                            </button>
                        </div>
                    </form>
                </div>
                
                <!-- Order Summary -->
                <div class="order-summary">
                    <div style="background: var(--white); padding: 2rem; border-radius: 15px; box-shadow: 0 5px 15px var(--shadow); position: sticky; top: 2rem;">
                        <h3 style="color: var(--primary-green); margin-bottom: 1.5rem; text-align: center;">
                            <i class="fas fa-receipt"></i> Resumo do Pedido
                        </h3>
                        
                        <!-- Items -->
                        <div style="max-height: 300px; overflow-y: auto; margin-bottom: 1rem;">
                            <?php foreach ($cartItems as $item): ?>
                                <div style="display: flex; justify-content: space-between; align-items: center; padding: 0.75rem 0; border-bottom: 1px solid var(--beige);">
                                    <div>
                                        <div style="font-weight: 600; color: var(--primary-green);"><?php echo htmlspecialchars($item['name']); ?></div>
                                        <div style="font-size: 0.9rem; color: var(--text-light);">
                                            <?php echo $item['quantity']; ?>x R$ <?php echo number_format($item['price'], 2, ',', '.'); ?>
                                        </div>
                                    </div>
                                    <div style="font-weight: 600;">
                                        R$ <?php echo number_format($item['subtotal'], 2, ',', '.'); ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <!-- Totals -->
                        <div style="border-top: 2px solid var(--beige); padding-top: 1rem;">
                            <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                                <span>Subtotal:</span>
                                <span>R$ <?php echo number_format($total, 2, ',', '.'); ?></span>
                            </div>
                            <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                                <span>Frete:</span>
                                <span style="color: var(--success);">GRÁTIS</span>
                            </div>
                            <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                                <span>Entrega:</span>
                                <span style="color: var(--primary-green); font-weight: 600;">30-50 min</span>
                            </div>
                            <div style="display: flex; justify-content: space-between; font-size: 1.2rem; font-weight: 700; color: var(--primary-green); border-top: 2px solid var(--primary-green); padding-top: 0.5rem; margin-top: 0.5rem;">
                                <span>Total:</span>
                                <span>R$ <?php echo number_format($total, 2, ',', '.'); ?></span>
                            </div>
                        </div>
                        
                        <!-- Security Info -->
                        <div style="text-align: center; margin-top: 1.5rem; padding: 1rem; background-color: var(--light-beige); border-radius: 8px;">
                            <div style="color: var(--success); margin-bottom: 0.5rem;">
                                <i class="fas fa-shield-alt"></i> Compra 100% Segura
                            </div>
                            <div style="font-size: 0.9rem; color: var(--text-light);">
                                Seus dados estão protegidos
                            </div>
                        </div>
                    </div>
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

    <script src="../assets/js/main.js"></script>
    <script>
        // Destacar opção de pagamento selecionada
        document.querySelectorAll('input[name="payment_method"]').forEach(input => {
            input.addEventListener('change', function() {
                document.querySelectorAll('.payment-option').forEach(option => {
                    option.style.borderColor = 'var(--beige)';
                    option.style.backgroundColor = 'var(--white)';
                });
                
                this.closest('.payment-option').style.borderColor = 'var(--primary-green)';
                this.closest('.payment-option').style.backgroundColor = 'var(--light-beige)';
            });
        });
        
        // Validação do formulário
        document.getElementById('checkoutForm').addEventListener('submit', function(e) {
            const paymentMethod = document.querySelector('input[name="payment_method"]:checked');
            const deliveryAddress = document.getElementById('delivery_address').value.trim();
            
            if (!paymentMethod) {
                e.preventDefault();
                showAlert('Por favor, selecione um método de pagamento', 'error');
                return;
            }
            
            if (!deliveryAddress) {
                e.preventDefault();
                showAlert('Por favor, confirme o endereço de entrega', 'error');
                return;
            }
        });
    </script>
</body>
</html>
