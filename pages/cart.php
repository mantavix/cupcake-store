<?php
session_start();
include_once '../config/database.php';

// Verificar se o usuário está logado
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$userId = $_SESSION['user_id'];

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
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carrinho - Cupcake Store</title>
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
                        <li><a href="../index.php#products">Produtos</a></li>
                        <li><a href="profile.php">Perfil</a></li>
                        <li><a href="../actions/logout.php">Sair</a></li>
                    </ul>
                </nav>
            </div>
        </div>
    </header>

    <!-- Cart Section -->
    <section class="cart-section" style="padding: 4rem 0; min-height: 70vh;">
        <div class="container">
            <h2 class="section-title">
                <i class="fas fa-shopping-cart"></i> Meu Carrinho
            </h2>
            
            <?php if (empty($cartItems)): ?>
                <div style="text-align: center; padding: 4rem 0;">
                    <i class="fas fa-shopping-cart" style="font-size: 4rem; color: var(--silver-gray); margin-bottom: 1rem;"></i>
                    <h3 style="color: var(--text-light); margin-bottom: 1rem;">Seu carrinho está vazio</h3>
                    <p style="color: var(--text-light); margin-bottom: 2rem;">Adicione alguns cupcakes deliciosos ao seu carrinho!</p>
                    <a href="../index.php#products" class="btn btn-primary">
                        <i class="fas fa-shopping-bag"></i> Continuar Comprando
                    </a>
                </div>
            <?php else: ?>
                <div style="display: grid; grid-template-columns: 1fr 300px; gap: 2rem;">
                    <!-- Cart Items -->
                    <div class="cart-items">
                        <?php foreach ($cartItems as $item): ?>
                            <div class="cart-item">
                                <div class="cart-item-image">
                                    <img src="../assets/img/cupcake-/<?php echo htmlspecialchars($item['image']); ?>" 
                                         alt="<?php echo htmlspecialchars($item['name']); ?>" 
                                         style="width: 100%; height: 100%; object-fit: cover; border-radius: 8px;"
                                         onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                    <div style="display: none; width: 100%; height: 100%; align-items: center; justify-content: center; font-size: 2rem; color: var(--primary-green);">
                                        <i class="fas fa-birthday-cake"></i>
                                    </div>
                                </div>
                                <div class="cart-item-info">
                                    <h4 class="cart-item-name"><?php echo htmlspecialchars($item['name']); ?></h4>
                                    <p class="cart-item-price">R$ <?php echo number_format($item['price'], 2, ',', '.'); ?> cada</p>
                                    <p style="color: var(--text-light); font-size: 0.9rem;">
                                        Estoque disponível: <?php echo $item['stock_quantity']; ?> unidades
                                    </p>
                                </div>
                                <div class="cart-item-actions">
                                    <div class="quantity-selector">
                                        <button class="quantity-btn" onclick="updateQuantity(<?php echo $item['product_id']; ?>, <?php echo max(1, $item['quantity'] - 1); ?>)">-</button>
                                        <input type="number" class="quantity-input" value="<?php echo $item['quantity']; ?>" 
                                               min="1" max="<?php echo $item['stock_quantity']; ?>"
                                               onchange="updateQuantity(<?php echo $item['product_id']; ?>, this.value)">
                                        <button class="quantity-btn" onclick="updateQuantity(<?php echo $item['product_id']; ?>, <?php echo min($item['stock_quantity'], $item['quantity'] + 1); ?>)">+</button>
                                    </div>
                                    <div style="text-align: center; margin: 0 1rem;">
                                        <strong style="color: var(--primary-green);">
                                            R$ <?php echo number_format($item['subtotal'], 2, ',', '.'); ?>
                                        </strong>
                                    </div>
                                    <button class="btn btn-danger" onclick="removeFromCart(<?php echo $item['product_id']; ?>)" 
                                            style="padding: 0.5rem; min-width: 40px;">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        
                        <div style="text-align: center; margin-top: 2rem;">
                            <button class="btn btn-secondary" onclick="clearCart()" style="margin-right: 1rem;">
                                <i class="fas fa-trash-alt"></i> Esvaziar Carrinho
                            </button>
                            <a href="../index.php#products" class="btn btn-secondary">
                                <i class="fas fa-plus"></i> Continuar Comprando
                            </a>
                        </div>
                    </div>
                    
                    <!-- Cart Summary -->
                    <div class="cart-summary">
                        <h3 style="color: var(--primary-green); margin-bottom: 1rem; text-align: center;">
                            <i class="fas fa-receipt"></i> Resumo do Pedido
                        </h3>
                        
                        <div style="border-bottom: 2px solid var(--beige); padding-bottom: 1rem; margin-bottom: 1rem;">
                            <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                                <span>Itens (<?php echo count($cartItems); ?>):</span>
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
                        </div>
                        
                        <div class="cart-total" style="border-bottom: 2px solid var(--primary-green); padding-bottom: 1rem; margin-bottom: 1rem;">
                            Total: R$ <?php echo number_format($total, 2, ',', '.'); ?>
                        </div>
                        
                        <a href="checkout.php" class="btn btn-primary" style="width: 100%; text-align: center; margin-bottom: 1rem;">
                            <i class="fas fa-credit-card"></i> Finalizar Compra
                        </a>
                        
                        <div style="text-align: center; font-size: 0.9rem; color: var(--text-light);">
                            <i class="fas fa-shield-alt"></i> Compra 100% segura
                        </div>
                    </div>
                </div>
            <?php endif; ?>
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
        function updateQuantity(productId, quantity) {
            if (quantity < 1) return;
            updateCartQuantity(productId, quantity);
        }
    </script>
</body>
</html>
