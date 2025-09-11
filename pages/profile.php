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
try {
    $user = fetchOne("SELECT * FROM users WHERE id = ?", [$userId]);
    
    // Buscar pedidos do usuário
    $orders = fetchData("
        SELECT o.*, COUNT(oi.id) as items_count
        FROM orders o
        LEFT JOIN order_items oi ON o.id = oi.order_id
        WHERE o.user_id = ?
        GROUP BY o.id
        ORDER BY o.created_at DESC
        LIMIT 10
    ", [$userId]);
    
} catch (Exception $e) {
    $user = null;
    $orders = [];
}

if (!$user) {
    header('Location: login.php');
    exit;
}

$error = '';
$success = '';
if (isset($_SESSION['profile_error'])) {
    $error = $_SESSION['profile_error'];
    unset($_SESSION['profile_error']);
}
if (isset($_SESSION['profile_success'])) {
    $success = $_SESSION['profile_success'];
    unset($_SESSION['profile_success']);
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meu Perfil - Cupcake Store</title>
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
                        <li><a href="cart.php">Carrinho</a></li>
                        <li><a href="profile.php">Perfil</a></li>
                        <li><a href="../actions/logout.php">Sair</a></li>
                    </ul>
                </nav>
            </div>
        </div>
    </header>

    <!-- Profile Section -->
    <section class="profile-section" style="padding: 4rem 0; background-color: var(--light-beige); min-height: 80vh;">
        <div class="container">
            <h2 class="section-title">
                <i class="fas fa-user"></i> Meu Perfil
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
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
                <!-- User Info -->
                <div style="background: var(--white); padding: 2rem; border-radius: 15px; box-shadow: 0 5px 15px var(--shadow);">
                    <h3 style="color: var(--primary-green); margin-bottom: 1.5rem;">
                        <i class="fas fa-id-card"></i> Informações Pessoais
                    </h3>
                    
                    <div style="margin-bottom: 1rem;">
                        <strong style="color: var(--primary-green);">Nome:</strong><br>
                        <?php echo htmlspecialchars($user['name']); ?>
                    </div>
                    
                    <div style="margin-bottom: 1rem;">
                        <strong style="color: var(--primary-green);">E-mail:</strong><br>
                        <?php echo htmlspecialchars($user['email']); ?>
                    </div>
                    
                    <div style="margin-bottom: 1rem;">
                        <strong style="color: var(--primary-green);">CPF:</strong><br>
                        <?php echo htmlspecialchars($user['cpf']); ?>
                    </div>
                    
                    <div style="margin-bottom: 1rem;">
                        <strong style="color: var(--primary-green);">Telefone:</strong><br>
                        <?php echo htmlspecialchars($user['phone']); ?>
                    </div>
                    
                    <div style="margin-bottom: 1rem;">
                        <strong style="color: var(--primary-green);">Endereço:</strong><br>
                        <?php echo htmlspecialchars($user['address']); ?><br>
                        <?php echo htmlspecialchars($user['city']); ?>/<?php echo htmlspecialchars($user['state']); ?> - CEP: <?php echo htmlspecialchars($user['zip_code']); ?>
                    </div>
                    
                    <div style="margin-bottom: 1rem;">
                        <strong style="color: var(--primary-green);">Usuário:</strong><br>
                        <?php echo htmlspecialchars($user['username']); ?>
                    </div>
                    
                    <div style="margin-bottom: 1rem;">
                        <strong style="color: var(--primary-green);">Membro desde:</strong><br>
                        <?php echo date('d/m/Y', strtotime($user['created_at'])); ?>
                    </div>
                    
                    <div style="text-align: center; margin-top: 2rem;">
                        <button onclick="toggleEditForm()" class="btn btn-primary">
                            <i class="fas fa-edit"></i> Editar Informações
                        </button>
                    </div>
                </div>
                
                <!-- Order History -->
                <div style="background: var(--white); padding: 2rem; border-radius: 15px; box-shadow: 0 5px 15px var(--shadow);">
                    <h3 style="color: var(--primary-green); margin-bottom: 1.5rem;">
                        <i class="fas fa-history"></i> Histórico de Pedidos
                    </h3>
                    
                    <?php if (empty($orders)): ?>
                        <div style="text-align: center; padding: 2rem; color: var(--text-light);">
                            <i class="fas fa-shopping-bag" style="font-size: 3rem; margin-bottom: 1rem;"></i>
                            <p>Você ainda não fez nenhum pedido</p>
                            <a href="../index.php#products" class="btn btn-primary" style="margin-top: 1rem;">
                                Fazer Primeiro Pedido
                            </a>
                        </div>
                    <?php else: ?>
                        <div style="max-height: 400px; overflow-y: auto;">
                            <?php foreach ($orders as $order): ?>
                                <div style="border: 1px solid var(--beige); border-radius: 8px; padding: 1rem; margin-bottom: 1rem;">
                                    <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 0.5rem;">
                                        <div>
                                            <div style="font-weight: 600; color: var(--primary-green);">
                                                Pedido #<?php echo $order['id']; ?>
                                            </div>
                                            <div style="font-size: 0.9rem; color: var(--text-light);">
                                                <?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?>
                                            </div>
                                        </div>
                                        <div style="text-align: right;">
                                            <div style="font-weight: 600;">
                                                R$ <?php echo number_format($order['total_amount'], 2, ',', '.'); ?>
                                            </div>
                                            <div style="font-size: 0.8rem;">
                                                <?php echo $order['items_count']; ?> itens
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div style="display: flex; justify-content: space-between; align-items: center;">
                                        <div>
                                            <span style="padding: 0.2rem 0.5rem; border-radius: 12px; font-size: 0.8rem; font-weight: 600; color: white; background-color: var(--primary-green);">
                                                <?php 
                                                $statusLabels = [
                                                    'pending' => 'Pendente',
                                                    'confirmed' => 'Confirmado',
                                                    'preparing' => 'Preparando',
                                                    'shipped' => 'Enviado',
                                                    'delivered' => 'Entregue',
                                                    'cancelled' => 'Cancelado'
                                                ];
                                                echo $statusLabels[$order['status']] ?? $order['status'];
                                                ?>
                                            </span>
                                        </div>
                                        
                                        <div>
                                            <?php 
                                            $paymentLabels = [
                                                'credit_card' => 'Cartão de Crédito',
                                                'debit_card' => 'Cartão de Débito',
                                                'pix' => 'PIX',
                                                'cash_on_delivery' => 'Dinheiro na Entrega'
                                            ];
                                            echo $paymentLabels[$order['payment_method']] ?? $order['payment_method'];
                                            ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <?php if (count($orders) >= 10): ?>
                            <div style="text-align: center; margin-top: 1rem;">
                                <small style="color: var(--text-light);">
                                    Mostrando os 10 pedidos mais recentes
                                </small>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>

    <!-- Edit Form Modal -->
    <div id="editModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
        <div style="background: var(--white); padding: 2rem; border-radius: 15px; max-width: 600px; max-height: 80vh; overflow-y: auto; position: relative; margin: 2rem;">
            <button onclick="toggleEditForm()" style="position: absolute; top: 1rem; right: 1rem; background: none; border: none; font-size: 1.5rem; cursor: pointer;">
                <i class="fas fa-times"></i>
            </button>
            
            <h3 style="color: var(--primary-green); margin-bottom: 1.5rem;">
                <i class="fas fa-edit"></i> Editar Informações
            </h3>
            
            <form action="../actions/update_profile.php" method="POST">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div class="form-group">
                        <label for="name">Nome Completo *</label>
                        <input type="text" id="name" name="name" required value="<?php echo htmlspecialchars($user['name']); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="phone">Telefone *</label>
                        <input type="text" id="phone" name="phone" required value="<?php echo htmlspecialchars($user['phone']); ?>">
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="email">E-mail *</label>
                    <input type="email" id="email" name="email" required value="<?php echo htmlspecialchars($user['email']); ?>">
                </div>
                
                <div class="form-group">
                    <label for="address">Endereço Completo *</label>
                    <textarea id="address" name="address" required rows="3"><?php echo htmlspecialchars($user['address']); ?></textarea>
                </div>
                
                <div style="display: grid; grid-template-columns: 2fr 1fr 1fr; gap: 1rem;">
                    <div class="form-group">
                        <label for="city">Cidade *</label>
                        <input type="text" id="city" name="city" required value="<?php echo htmlspecialchars($user['city']); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="state">Estado *</label>
                        <select id="state" name="state" required>
                            <option value="">Selecione</option>
                            <?php 
                            $states = ['AC', 'AL', 'AP', 'AM', 'BA', 'CE', 'DF', 'ES', 'GO', 'MA', 'MT', 'MS', 'MG', 'PA', 'PB', 'PR', 'PE', 'PI', 'RJ', 'RN', 'RS', 'RO', 'RR', 'SC', 'SP', 'SE', 'TO'];
                            foreach ($states as $state) {
                                $selected = $state === $user['state'] ? 'selected' : '';
                                echo "<option value=\"$state\" $selected>$state</option>";
                            }
                            ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="zip_code">CEP *</label>
                        <input type="text" id="zip_code" name="zip_code" required value="<?php echo htmlspecialchars($user['zip_code']); ?>">
                    </div>
                </div>
                
                <div style="text-align: center; margin-top: 2rem;">
                    <button type="submit" class="btn btn-primary" style="margin-right: 1rem;">
                        <i class="fas fa-save"></i> Salvar Alterações
                    </button>
                    <button type="button" onclick="toggleEditForm()" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancelar
                    </button>
                </div>
            </form>
        </div>
    </div>

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
        function toggleEditForm() {
            const modal = document.getElementById('editModal');
            modal.style.display = modal.style.display === 'flex' ? 'none' : 'flex';
        }
        
        // Fechar modal clicando fora
        document.getElementById('editModal').addEventListener('click', function(e) {
            if (e.target === this) {
                toggleEditForm();
            }
        });
    </script>
</body>
</html>
