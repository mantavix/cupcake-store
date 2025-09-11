<?php
session_start();
include_once '../config/database.php';

// Se já está logado, redirecionar
if (isset($_SESSION['user_id'])) {
    header('Location: ../index.php');
    exit;
}

// Verificar se token foi fornecido
$token = isset($_GET['token']) ? trim($_GET['token']) : '';
$validToken = false;
$userEmail = '';

if ($token) {
    try {
        // Verificar se o token é válido e não expirou
        $resetData = fetchOne("
            SELECT pr.*, u.email, u.name 
            FROM password_resets pr 
            JOIN users u ON pr.user_id = u.id 
            WHERE pr.token = ? AND pr.expires_at > NOW() AND pr.used = FALSE
        ", [$token]);
        
        if ($resetData) {
            $validToken = true;
            $userEmail = $resetData['email'];
        }
    } catch (Exception $e) {
        // Token inválido
    }
}

$error = '';
$success = '';
if (isset($_SESSION['reset_error'])) {
    $error = $_SESSION['reset_error'];
    unset($_SESSION['reset_error']);
}
if (isset($_SESSION['reset_success'])) {
    $success = $_SESSION['reset_success'];
    unset($_SESSION['reset_success']);
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Redefinir Senha - Cupcake Store</title>
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

    <!-- Reset Password Form -->
    <section class="auth-section" style="padding: 4rem 0; min-height: 80vh; display: flex; align-items: center;">
        <div class="container">
            <div class="form-container">
                <h2 style="text-align: center; color: var(--primary-green); margin-bottom: 2rem;">
                    <i class="fas fa-lock"></i> Redefinir Senha
                </h2>
                
                <?php if (!$validToken): ?>
                    <div class="alert alert-error">
                        <i class="fas fa-exclamation-triangle"></i> 
                        Token inválido ou expirado. Solicite uma nova recuperação de senha.
                    </div>
                    
                    <div style="text-align: center; margin-top: 2rem;">
                        <a href="forgot_password.php" class="btn btn-primary">
                            <i class="fas fa-key"></i> Solicitar Nova Recuperação
                        </a>
                    </div>
                    
                <?php else: ?>
                    <div style="text-align: center; margin-bottom: 2rem; color: var(--text-light);">
                        <p>Olá! Digite sua nova senha para a conta: <strong><?php echo htmlspecialchars($userEmail); ?></strong></p>
                    </div>
                    
                    <?php if ($success): ?>
                        <div class="alert alert-success">
                            <?php echo htmlspecialchars($success); ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($error): ?>
                        <div class="alert alert-error">
                            <?php echo htmlspecialchars($error); ?>
                        </div>
                    <?php endif; ?>
                    
                    <form action="../actions/reset_password_process.php" method="POST" id="resetForm">
                        <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
                        
                        <div class="form-group">
                            <label for="password">Nova Senha</label>
                            <input type="password" id="password" name="password" required minlength="6" 
                                   placeholder="Digite sua nova senha (mínimo 6 caracteres)">
                        </div>
                        
                        <div class="form-group">
                            <label for="confirm_password">Confirmar Nova Senha</label>
                            <input type="password" id="confirm_password" name="confirm_password" required minlength="6" 
                                   placeholder="Digite novamente sua nova senha">
                        </div>
                        
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary" style="width: 100%;">
                                <i class="fas fa-save"></i> Redefinir Senha
                            </button>
                        </div>
                    </form>
                <?php endif; ?>
                
                <div style="text-align: center; margin-top: 2rem;">
                    <p>Lembrou da senha? <a href="login.php" style="color: var(--primary-green); font-weight: 600;">Fazer login</a></p>
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
        document.getElementById('resetForm')?.addEventListener('submit', function(e) {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            
            // Validar se as senhas coincidem
            if (password !== confirmPassword) {
                e.preventDefault();
                showAlert('As senhas não coincidem', 'error');
                return;
            }
            
            // Validar tamanho da senha
            if (password.length < 6) {
                e.preventDefault();
                showAlert('A senha deve ter pelo menos 6 caracteres', 'error');
                return;
            }
        });
    </script>
</body>
</html>
