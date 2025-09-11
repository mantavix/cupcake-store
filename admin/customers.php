<?php
session_start();
include_once '../config/database.php';

// Verificar se é admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header('Location: ../pages/login.php');
    exit;
}

$search = $_GET['search'] ?? '';
$error = '';
$success = '';

// Processar ações
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_customer'])) {
        $customerId = (int)$_POST['customer_id'];
        $name = trim($_POST['name']);
        $email = trim($_POST['email']);
        $phone = trim($_POST['phone']);
        $address = trim($_POST['address']);
        $city = trim($_POST['city']);
        $state = trim($_POST['state']);
        $zipCode = trim($_POST['zip_code']);
        
        if ($name && $email && $phone && $address && $city && $state && $zipCode) {
            try {
                executeQuery("UPDATE users SET name = ?, email = ?, phone = ?, address = ?, city = ?, state = ?, zip_code = ?, updated_at = NOW() WHERE id = ? AND user_type = 'customer'", 
                            [$name, $email, $phone, $address, $city, $state, $zipCode, $customerId]);
                $success = 'Cliente atualizado com sucesso!';
            } catch (Exception $e) {
                $error = 'Erro ao atualizar cliente: ' . $e->getMessage();
            }
        } else {
            $error = 'Todos os campos são obrigatórios';
        }
    }
}

// Construir query para buscar clientes
$whereClause = "WHERE user_type = 'customer'";
$params = [];

if ($search) {
    $whereClause .= " AND (name LIKE ? OR email LIKE ? OR cpf LIKE ?)";
    $searchTerm = "%{$search}%";
    $params = [$searchTerm, $searchTerm, $searchTerm];
}

try {
    $customers = fetchData("
        SELECT u.*, 
               COUNT(o.id) as total_orders,
               COALESCE(SUM(o.total_amount), 0) as total_spent
        FROM users u
        LEFT JOIN orders o ON u.id = o.user_id AND o.status != 'cancelled'
        {$whereClause}
        GROUP BY u.id
        ORDER BY u.created_at DESC
    ", $params);
} catch (Exception $e) {
    $customers = [];
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Clientes - Cupcake Store Admin</title>
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
                    <a href="dashboard.php" style="color: white; text-decoration: none;">
                        <h1><i class="fas fa-birthday-cake"></i> Cupcake Store Admin</h1>
                    </a>
                </div>
                <nav class="nav-menu">
                    <ul>
                        <li><a href="dashboard.php">Dashboard</a></li>
                        <li><a href="products.php">Produtos</a></li>
                        <li><a href="orders.php">Pedidos</a></li>
                        <li><a href="customers.php">Clientes</a></li>
                        <li><a href="reports.php">Relatórios</a></li>
                        <li><a href="../index.php">Ver Loja</a></li>
                        <li><a href="../actions/logout.php">Sair</a></li>
                    </ul>
                </nav>
            </div>
        </div>
    </header>

    <!-- Content -->
    <section style="padding: 2rem 0; background-color: var(--light-beige); min-height: 100vh;">
        <div class="container">
            <h2 style="color: var(--primary-green); margin-bottom: 2rem;">
                <i class="fas fa-users"></i> Gerenciar Clientes
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
            
            <!-- Search -->
            <div style="background: var(--white); padding: 1.5rem; border-radius: 15px; box-shadow: 0 5px 15px var(--shadow); margin-bottom: 2rem;">
                <h3 style="color: var(--primary-green); margin-bottom: 1rem;">
                    <i class="fas fa-search"></i> Buscar Clientes
                </h3>
                
                <form method="GET" style="display: flex; gap: 1rem; align-items: end;">
                    <div class="form-group" style="flex: 1; margin-bottom: 0;">
                        <input type="text" name="search" placeholder="Buscar por nome, email ou CPF..." 
                               value="<?php echo htmlspecialchars($search); ?>" style="width: 100%;">
                    </div>
                    
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i> Buscar
                    </button>
                    
                    <?php if ($search): ?>
                        <a href="customers.php" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Limpar
                        </a>
                    <?php endif; ?>
                </form>
            </div>
            
            <!-- Customers List -->
            <div style="background: var(--white); border-radius: 15px; box-shadow: 0 5px 15px var(--shadow); overflow: hidden;">
                <div style="padding: 1.5rem; border-bottom: 2px solid var(--beige);">
                    <h3 style="color: var(--primary-green);">
                        <i class="fas fa-list"></i> Lista de Clientes (<?php echo count($customers); ?>)
                    </h3>
                </div>
                
                <?php if (empty($customers)): ?>
                    <div style="text-align: center; padding: 3rem; color: var(--text-light);">
                        <i class="fas fa-users" style="font-size: 3rem; margin-bottom: 1rem;"></i>
                        <p><?php echo $search ? 'Nenhum cliente encontrado' : 'Nenhum cliente cadastrado ainda'; ?></p>
                    </div>
                <?php else: ?>
                    <div style="overflow-x: auto;">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nome</th>
                                    <th>E-mail</th>
                                    <th>Telefone</th>
                                    <th>CPF</th>
                                    <th>Cidade/Estado</th>
                                    <th>Pedidos</th>
                                    <th>Total Gasto</th>
                                    <th>Cadastro</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($customers as $customer): ?>
                                    <tr>
                                        <td><?php echo $customer['id']; ?></td>
                                        <td style="font-weight: 600;"><?php echo htmlspecialchars($customer['name']); ?></td>
                                        <td><?php echo htmlspecialchars($customer['email']); ?></td>
                                        <td><?php echo htmlspecialchars($customer['phone']); ?></td>
                                        <td><?php echo htmlspecialchars($customer['cpf']); ?></td>
                                        <td><?php echo htmlspecialchars($customer['city'] . '/' . $customer['state']); ?></td>
                                        <td style="text-align: center;">
                                            <span style="background: var(--primary-green); color: white; padding: 0.25rem 0.5rem; border-radius: 12px; font-size: 0.8rem;">
                                                <?php echo $customer['total_orders']; ?>
                                            </span>
                                        </td>
                                        <td style="font-weight: 600; color: var(--primary-green);">
                                            R$ <?php echo number_format($customer['total_spent'], 2, ',', '.'); ?>
                                        </td>
                                        <td><?php echo date('d/m/Y', strtotime($customer['created_at'])); ?></td>
                                        <td>
                                            <div style="display: flex; gap: 0.5rem;">
                                                <button onclick="viewCustomer(<?php echo $customer['id']; ?>)" 
                                                        class="btn btn-secondary" style="padding: 0.25rem 0.5rem; font-size: 0.8rem;">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                
                                                <button onclick="editCustomer(<?php echo $customer['id']; ?>)" 
                                                        class="btn btn-primary" style="padding: 0.25rem 0.5rem; font-size: 0.8rem;">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Customer Details Modal -->
    <div id="customerModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
        <div style="background: var(--white); padding: 2rem; border-radius: 15px; max-width: 600px; max-height: 80vh; overflow-y: auto; position: relative;">
            <button onclick="closeCustomerModal()" style="position: absolute; top: 1rem; right: 1rem; background: none; border: none; font-size: 1.5rem; cursor: pointer;">
                <i class="fas fa-times"></i>
            </button>
            <div id="customerDetails">
                <!-- Conteúdo será carregado via JavaScript -->
            </div>
        </div>
    </div>

    <!-- Edit Customer Modal -->
    <div id="editModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
        <div style="background: var(--white); padding: 2rem; border-radius: 15px; max-width: 600px; max-height: 80vh; overflow-y: auto; position: relative;">
            <button onclick="closeEditModal()" style="position: absolute; top: 1rem; right: 1rem; background: none; border: none; font-size: 1.5rem; cursor: pointer;">
                <i class="fas fa-times"></i>
            </button>
            <div id="editForm">
                <!-- Formulário será carregado via JavaScript -->
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3>Cupcake Store</h3>
                    <p>Painel Administrativo - Clientes</p>
                </div>
                <div class="footer-section">
                    <h3>Estatísticas</h3>
                    <p>Total de Clientes: <?php echo count($customers); ?></p>
                    <p>Total Faturado: R$ <?php echo number_format(array_sum(array_column($customers, 'total_spent')), 2, ',', '.'); ?></p>
                </div>
            </div>
        </div>
    </footer>

    <script>
        const customers = <?php echo json_encode($customers); ?>;
        
        function viewCustomer(customerId) {
            const customer = customers.find(c => c.id == customerId);
            if (!customer) return;
            
            const html = `
                <h3 style="color: var(--primary-green); margin-bottom: 1.5rem;">
                    <i class="fas fa-user"></i> Detalhes do Cliente
                </h3>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 2rem;">
                    <div>
                        <h4 style="color: var(--primary-green);">Informações Pessoais</h4>
                        <p><strong>Nome:</strong> ${customer.name}</p>
                        <p><strong>E-mail:</strong> ${customer.email}</p>
                        <p><strong>CPF:</strong> ${customer.cpf}</p>
                        <p><strong>Telefone:</strong> ${customer.phone}</p>
                        <p><strong>Usuário:</strong> ${customer.username}</p>
                    </div>
                    
                    <div>
                        <h4 style="color: var(--primary-green);">Endereço</h4>
                        <p><strong>Endereço:</strong> ${customer.address}</p>
                        <p><strong>Cidade:</strong> ${customer.city}</p>
                        <p><strong>Estado:</strong> ${customer.state}</p>
                        <p><strong>CEP:</strong> ${customer.zip_code}</p>
                    </div>
                </div>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem; text-align: center;">
                    <div style="background: var(--light-beige); padding: 1rem; border-radius: 8px;">
                        <h4 style="color: var(--primary-green); margin-bottom: 0.5rem;">${customer.total_orders}</h4>
                        <p style="font-size: 0.9rem;">Pedidos Realizados</p>
                    </div>
                    
                    <div style="background: var(--light-beige); padding: 1rem; border-radius: 8px;">
                        <h4 style="color: var(--primary-green); margin-bottom: 0.5rem;">R$ ${parseFloat(customer.total_spent).toFixed(2).replace('.', ',')}</h4>
                        <p style="font-size: 0.9rem;">Total Gasto</p>
                    </div>
                    
                    <div style="background: var(--light-beige); padding: 1rem; border-radius: 8px;">
                        <h4 style="color: var(--primary-green); margin-bottom: 0.5rem;">${new Date(customer.created_at).toLocaleDateString('pt-BR')}</h4>
                        <p style="font-size: 0.9rem;">Cliente Desde</p>
                    </div>
                </div>
            `;
            
            document.getElementById('customerDetails').innerHTML = html;
            document.getElementById('customerModal').style.display = 'flex';
        }
        
        function editCustomer(customerId) {
            const customer = customers.find(c => c.id == customerId);
            if (!customer) return;
            
            const html = `
                <h3 style="color: var(--primary-green); margin-bottom: 1.5rem;">
                    <i class="fas fa-edit"></i> Editar Cliente
                </h3>
                
                <form method="POST">
                    <input type="hidden" name="customer_id" value="${customer.id}">
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                        <div class="form-group">
                            <label>Nome Completo *</label>
                            <input type="text" name="name" required value="${customer.name}">
                        </div>
                        
                        <div class="form-group">
                            <label>Telefone *</label>
                            <input type="text" name="phone" required value="${customer.phone}">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>E-mail *</label>
                        <input type="email" name="email" required value="${customer.email}">
                    </div>
                    
                    <div class="form-group">
                        <label>Endereço *</label>
                        <textarea name="address" required rows="2">${customer.address}</textarea>
                    </div>
                    
                    <div style="display: grid; grid-template-columns: 2fr 1fr 1fr; gap: 1rem;">
                        <div class="form-group">
                            <label>Cidade *</label>
                            <input type="text" name="city" required value="${customer.city}">
                        </div>
                        
                        <div class="form-group">
                            <label>Estado *</label>
                            <input type="text" name="state" required value="${customer.state}" maxlength="2">
                        </div>
                        
                        <div class="form-group">
                            <label>CEP *</label>
                            <input type="text" name="zip_code" required value="${customer.zip_code}">
                        </div>
                    </div>
                    
                    <div style="text-align: center; margin-top: 2rem;">
                        <button type="submit" name="update_customer" class="btn btn-primary" style="margin-right: 1rem;">
                            <i class="fas fa-save"></i> Salvar Alterações
                        </button>
                        <button type="button" onclick="closeEditModal()" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Cancelar
                        </button>
                    </div>
                </form>
            `;
            
            document.getElementById('editForm').innerHTML = html;
            document.getElementById('editModal').style.display = 'flex';
        }
        
        function closeCustomerModal() {
            document.getElementById('customerModal').style.display = 'none';
        }
        
        function closeEditModal() {
            document.getElementById('editModal').style.display = 'none';
        }
        
        // Fechar modals clicando fora
        document.getElementById('customerModal').addEventListener('click', function(e) {
            if (e.target === this) closeCustomerModal();
        });
        
        document.getElementById('editModal').addEventListener('click', function(e) {
            if (e.target === this) closeEditModal();
        });
    </script>
</body>
</html>
