<?php
session_start();
include_once 'config/database.php';

// Verificar se o usuário está logado
$isLoggedIn = isset($_SESSION['user_id']);
$isAdmin = isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'admin';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cupcake Store - Loja Online</title>
    <link rel="stylesheet" href="assets/css/style.css">
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
                    <img src="assets/img/logo/logo_cupcake-store.jpg" alt="Cupcake Store" style="height: 50px; margin-right: 10px; vertical-align: middle; border-radius: 8px;">
                    <h1 style="display: inline; vertical-align: middle;">Cupcake Store</h1>
                </div>
                <nav class="nav-menu">
                    <ul>
                        <li><a href="#home">Início</a></li>
                        <li><a href="#products">Produtos</a></li>
                        <li><a href="<?php echo $isLoggedIn ? 'pages/cart.php' : 'pages/login.php'; ?>" id="cart-link">
                            <i class="fas fa-shopping-cart"></i> 
                            Carrinho (<span id="cart-count">0</span>)
                        </a></li>
                        <?php if ($isLoggedIn): ?>
                            <?php if ($isAdmin): ?>
                                <li><a href="admin/dashboard.php">Admin</a></li>
                            <?php endif; ?>
                            <li><a href="pages/profile.php">Perfil</a></li>
                            <li><a href="actions/logout.php">Sair</a></li>
                        <?php else: ?>
                            <li><a href="pages/login.php">Login</a></li>
                            <li><a href="pages/register.php">Cadastro</a></li>
                        <?php endif; ?>
                    </ul>
                </nav>
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <section id="home" class="hero">
        <div class="container">
            <div class="hero-content">
                <h2>Cupcakes Artesanais Deliciosos</h2>
                <p>Feitos com muito carinho e ingredientes selecionados</p>
                <a href="#products" class="btn btn-primary">Ver Produtos</a>
            </div>
        </div>
    </section>

    <!-- Products Section -->
    <section id="products" class="products-section">
        <div class="container">
            <h2 class="section-title">Nossos Cupcakes</h2>
            
            <div class="products-grid" id="products-grid">
                <!-- Products will be loaded here -->
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

    <!-- Modal de Login Required -->
    <div id="loginModal" class="login-modal">
        <div class="login-modal-content">
            <button class="login-modal-close" onclick="closeLoginModal()">
                <i class="fas fa-times"></i>
            </button>
            
            <div class="login-modal-icon">
                <i class="fas fa-user-lock"></i>
            </div>
            
            <h3>É preciso estar logado para efetuar a compra</h3>
            <p>Para adicionar produtos ao carrinho e finalizar sua compra, você precisa fazer login em sua conta ou criar uma nova conta.</p>
            
            <div class="login-modal-buttons">
                <a href="pages/login.php" class="btn btn-primary">
                    <i class="fas fa-sign-in-alt"></i> Fazer Login
                </a>
                <a href="pages/register.php" class="btn btn-secondary">
                    <i class="fas fa-user-plus"></i> Criar Conta
                </a>
            </div>
        </div>
    </div>

    <script>
        // Passar informação de login para o JavaScript
        window.isUserLoggedIn = <?php echo $isLoggedIn ? 'true' : 'false'; ?>;
    </script>
    <script src="assets/js/main.js"></script>
</body>
</html>
