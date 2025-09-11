<?php
session_start();

// Se já está logado, redirecionar
if (isset($_SESSION['user_id'])) {
    header('Location: ../index.php');
    exit;
}

$error = '';
$success = '';
if (isset($_SESSION['register_error'])) {
    $error = $_SESSION['register_error'];
    unset($_SESSION['register_error']);
}
if (isset($_SESSION['register_success'])) {
    $success = $_SESSION['register_success'];
    unset($_SESSION['register_success']);
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro - Cupcake Store</title>
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
                    </ul>
                </nav>
            </div>
        </div>
    </header>

    <!-- Register Form -->
    <section class="auth-section" style="padding: 4rem 0;">
        <div class="container">
            <div class="form-container" style="max-width: 600px;">
                <h2 style="text-align: center; color: var(--primary-green); margin-bottom: 2rem;">
                    <i class="fas fa-user-plus"></i> Criar Sua Conta
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
                
                <form action="../actions/register_process.php" method="POST" id="registerForm">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                        <div class="form-group">
                            <label for="name">Nome Completo *</label>
                            <input type="text" id="name" name="name" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="cpf">CPF *</label>
                            <input type="text" id="cpf" name="cpf" required maxlength="14">
                        </div>
                    </div>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                        <div class="form-group">
                            <label for="email">E-mail *</label>
                            <input type="email" id="email" name="email" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="phone">Telefone *</label>
                            <input type="text" id="phone" name="phone" required maxlength="15">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="address">Endereço Completo *</label>
                        <textarea id="address" name="address" required rows="3" placeholder="Rua, número, complemento"></textarea>
                    </div>
                    
                    <div style="display: grid; grid-template-columns: 2fr 1fr 1fr; gap: 1rem;">
                        <div class="form-group">
                            <label for="city">Cidade *</label>
                            <input type="text" id="city" name="city" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="state">Estado *</label>
                            <select id="state" name="state" required>
                                <option value="">Selecione</option>
                                <option value="AC">AC</option>
                                <option value="AL">AL</option>
                                <option value="AP">AP</option>
                                <option value="AM">AM</option>
                                <option value="BA">BA</option>
                                <option value="CE">CE</option>
                                <option value="DF">DF</option>
                                <option value="ES">ES</option>
                                <option value="GO">GO</option>
                                <option value="MA">MA</option>
                                <option value="MT">MT</option>
                                <option value="MS">MS</option>
                                <option value="MG">MG</option>
                                <option value="PA">PA</option>
                                <option value="PB">PB</option>
                                <option value="PR">PR</option>
                                <option value="PE">PE</option>
                                <option value="PI">PI</option>
                                <option value="RJ">RJ</option>
                                <option value="RN">RN</option>
                                <option value="RS">RS</option>
                                <option value="RO">RO</option>
                                <option value="RR">RR</option>
                                <option value="SC">SC</option>
                                <option value="SP">SP</option>
                                <option value="SE">SE</option>
                                <option value="TO">TO</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="zip_code">CEP *</label>
                            <input type="text" id="zip_code" name="zip_code" required maxlength="9">
                        </div>
                    </div>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                        <div class="form-group">
                            <label for="username">Nome de Usuário *</label>
                            <input type="text" id="username" name="username" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="password">Senha *</label>
                            <input type="password" id="password" name="password" required minlength="6">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="confirm_password">Confirmar Senha *</label>
                        <input type="password" id="confirm_password" name="confirm_password" required minlength="6">
                    </div>
                    
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary" style="width: 100%;">
                            <i class="fas fa-user-plus"></i> Criar Conta
                        </button>
                    </div>
                </form>
                
                <div style="text-align: center; margin-top: 2rem;">
                    <p>Já tem uma conta? <a href="login.php" style="color: var(--primary-green); font-weight: 600;">Faça login aqui</a></p>
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
        document.getElementById('registerForm').addEventListener('submit', function(e) {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            const cpf = document.getElementById('cpf').value;
            const email = document.getElementById('email').value;
            
            // Validar se as senhas coincidem
            if (password !== confirmPassword) {
                e.preventDefault();
                showAlert('As senhas não coincidem', 'error');
                return;
            }
            
            // Validar CPF
            if (!validateCPF(cpf)) {
                e.preventDefault();
                showAlert('CPF inválido', 'error');
                return;
            }
            
            // Validar email
            if (!validateEmail(email)) {
                e.preventDefault();
                showAlert('E-mail inválido', 'error');
                return;
            }
            
            // Validar campos obrigatórios
            if (!validateForm('registerForm')) {
                e.preventDefault();
                showAlert('Por favor, preencha todos os campos obrigatórios', 'error');
                return;
            }
        });
        
        // Formatação automática do CEP
        document.getElementById('zip_code').addEventListener('input', function() {
            let value = this.value.replace(/\D/g, '');
            if (value.length >= 5) {
                value = value.replace(/(\d{5})(\d)/, '$1-$2');
            }
            this.value = value;
        });
    </script>
</body>
</html>
