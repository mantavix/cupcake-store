<?php
session_start();

// Se já está logado, redirecionar
if (isset($_SESSION['user_id'])) {
    header('Location: ../index.php');
    exit;
}

$error = '';
$success = '';
$resetLink = '';
if (isset($_SESSION['forgot_error'])) {
    $error = $_SESSION['forgot_error'];
    unset($_SESSION['forgot_error']);
}
if (isset($_SESSION['forgot_success'])) {
    $success = $_SESSION['forgot_success'];
    unset($_SESSION['forgot_success']);
}
if (isset($_SESSION['reset_link_demo'])) {
    $resetLink = $_SESSION['reset_link_demo'];
    unset($_SESSION['reset_link_demo']);
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Senha - Cupcake Store</title>
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
                        <li><a href="login.php">Login</a></li>
                        <li><a href="register.php">Cadastro</a></li>
                    </ul>
                </nav>
            </div>
        </div>
    </header>

    <!-- Forgot Password Form -->
    <section class="auth-section" style="padding: 4rem 0; min-height: 80vh; display: flex; align-items: center;">
        <div class="container">
            <div class="form-container">
                <h2 style="text-align: center; color: var(--primary-green); margin-bottom: 2rem;">
                    <i class="fas fa-key"></i> Recuperar Senha
                </h2>
                
                <div style="text-align: center; margin-bottom: 2rem; color: var(--text-light);">
                    <p>Digite seu e-mail para receber as instruções de recuperação de senha.</p>
                </div>
                
                <?php if ($success): ?>
                    <div class="alert alert-success">
                        <?php echo htmlspecialchars($success); ?>
                        
                        <?php if ($resetLink): ?>
                            <div style="margin-top: 1.5rem; padding: 1rem; background-color: rgba(255, 255, 255, 0.8); border-radius: 8px; border-left: 4px solid var(--primary-green);">
                                <h5 style="color: var(--primary-green); margin-bottom: 0.5rem;">
                                    <i class="fas fa-info-circle"></i> Modo Demonstração
                                </h5>
                                <p style="margin-bottom: 0.5rem; font-size: 0.9rem;">Como o email não está configurado, use o link abaixo para redefinir sua senha:</p>
                                <a href="<?php echo htmlspecialchars($resetLink); ?>" 
                                   class="btn btn-secondary" 
                                   style="font-size: 0.9rem; display: inline-flex; align-items: center; gap: 0.5rem;">
                                    <i class="fas fa-external-link-alt"></i> Acessar Link de Recuperação
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
                
                <?php if ($error): ?>
                    <div class="alert alert-error">
                        <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>
                
                <form action="../actions/forgot_password_process.php" method="POST" id="forgotForm">
                    <div class="form-group">
                        <label for="email">E-mail</label>
                        <input type="email" id="email" name="email" required placeholder="Digite seu e-mail cadastrado">
                    </div>
                    
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary" style="width: 100%;">
                            <i class="fas fa-paper-plane"></i> Enviar Instruções
                        </button>
                    </div>
                </form>
                
                <div style="text-align: center; margin-top: 2rem;">
                    <p>Lembrou da senha? <a href="login.php" style="color: var(--primary-green); font-weight: 600;">Fazer login</a></p>
                </div>
                
                <div style="text-align: center; margin-top: 1rem;">
                    <p>Não tem uma conta? <a href="register.php" style="color: var(--primary-green); font-weight: 600;">Cadastre-se aqui</a></p>
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
        document.getElementById('forgotForm').addEventListener('submit', function(e) {
            const email = document.getElementById('email').value;
            
            // Validar email
            if (!validateEmail(email)) {
                e.preventDefault();
                showAlert('Por favor, digite um e-mail válido', 'error');
                return;
            }
        });
    </script>
</body>
</html>
