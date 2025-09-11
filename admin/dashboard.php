<?php
session_start();
include_once '../config/database.php';

// Verificar se é admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header('Location: ../pages/login.php');
    exit;
}

// Buscar estatísticas
try {
    // Total de pedidos
    $totalOrders = fetchOne("SELECT COUNT(*) as total FROM orders")['total'];
    
    // Total de vendas
    $totalSales = fetchOne("SELECT SUM(total_amount) as total FROM orders WHERE status != 'cancelled'")['total'] ?? 0;
    
    // Pedidos pendentes
    $pendingOrders = fetchOne("SELECT COUNT(*) as total FROM orders WHERE status = 'pending'")['total'];
    
    // Total de produtos
    $totalProducts = fetchOne("SELECT COUNT(*) as total FROM products WHERE is_active = 1")['total'];
    
    // Total de clientes
    $totalCustomers = fetchOne("SELECT COUNT(*) as total FROM users WHERE user_type = 'customer'")['total'];
    
    // Produtos com baixo estoque
    $lowStockProducts = fetchData("SELECT * FROM products WHERE stock_quantity <= 5 AND is_active = 1 ORDER BY stock_quantity ASC");
    
    // Pedidos recentes
    $recentOrders = fetchData("
        SELECT o.*, u.name as customer_name, u.email as customer_email
        FROM orders o
        JOIN users u ON o.user_id = u.id
        ORDER BY o.created_at DESC
        LIMIT 10
    ");
    
} catch (Exception $e) {
    $totalOrders = $totalSales = $pendingOrders = $totalProducts = $totalCustomers = 0;
    $lowStockProducts = [];
    $recentOrders = [];
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Cupcake Store</title>
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
                        <h1 style="margin: 0;">Cupcake Store Admin</h1>
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

    <!-- Dashboard Content -->
    <section class="admin-dashboard" style="padding: 2rem 0; background-color: var(--light-beige); min-height: 100vh;">
        <div class="container">
            <h2 style="color: var(--primary-green); margin-bottom: 2rem;">
                <i class="fas fa-tachometer-alt"></i> Dashboard Administrativo
            </h2>
            
            <!-- Stats Cards -->
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem; margin-bottom: 3rem;">
                <div class="stat-card" style="background: linear-gradient(135deg, var(--primary-green), var(--light-green)); color: white; padding: 2rem; border-radius: 15px; text-align: center; box-shadow: 0 5px 15px var(--shadow);">
                    <div style="font-size: 3rem; margin-bottom: 0.5rem;">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                    <h3 style="font-size: 2rem; margin-bottom: 0.5rem;"><?php echo $totalOrders; ?></h3>
                    <p>Total de Pedidos</p>
                </div>
                
                <div class="stat-card" style="background: linear-gradient(135deg, var(--success), #4caf50); color: white; padding: 2rem; border-radius: 15px; text-align: center; box-shadow: 0 5px 15px var(--shadow);">
                    <div style="font-size: 3rem; margin-bottom: 0.5rem;">
                        <i class="fas fa-dollar-sign"></i>
                    </div>
                    <h3 style="font-size: 2rem; margin-bottom: 0.5rem;">R$ <?php echo number_format($totalSales, 2, ',', '.'); ?></h3>
                    <p>Total de Vendas</p>
                </div>
                
                <div class="stat-card" style="background: linear-gradient(135deg, var(--warning), #ff9800); color: white; padding: 2rem; border-radius: 15px; text-align: center; box-shadow: 0 5px 15px var(--shadow);">
                    <div style="font-size: 3rem; margin-bottom: 0.5rem;">
                        <i class="fas fa-clock"></i>
                    </div>
                    <h3 style="font-size: 2rem; margin-bottom: 0.5rem;"><?php echo $pendingOrders; ?></h3>
                    <p>Pedidos Pendentes</p>
                </div>
                
                <div class="stat-card" style="background: linear-gradient(135deg, var(--silver-gray), var(--dark-gray)); color: white; padding: 2rem; border-radius: 15px; text-align: center; box-shadow: 0 5px 15px var(--shadow);">
                    <div style="font-size: 3rem; margin-bottom: 0.5rem;">
                        <i class="fas fa-birthday-cake"></i>
                    </div>
                    <h3 style="font-size: 2rem; margin-bottom: 0.5rem;"><?php echo $totalProducts; ?></h3>
                    <p>Produtos Ativos</p>
                </div>
                
                <div class="stat-card" style="background: linear-gradient(135deg, #9c27b0, #673ab7); color: white; padding: 2rem; border-radius: 15px; text-align: center; box-shadow: 0 5px 15px var(--shadow);">
                    <div style="font-size: 3rem; margin-bottom: 0.5rem;">
                        <i class="fas fa-users"></i>
                    </div>
                    <h3 style="font-size: 2rem; margin-bottom: 0.5rem;"><?php echo $totalCustomers; ?></h3>
                    <p>Total de Clientes</p>
                </div>
            </div>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
                <!-- Low Stock Alert -->
                <div style="background: var(--white); padding: 2rem; border-radius: 15px; box-shadow: 0 5px 15px var(--shadow);">
                    <h3 style="color: var(--danger); margin-bottom: 1.5rem;">
                        <i class="fas fa-exclamation-triangle"></i> Alerta de Estoque Baixo
                    </h3>
                    
                    <?php if (empty($lowStockProducts)): ?>
                        <p style="color: var(--success); text-align: center; padding: 2rem;">
                            <i class="fas fa-check-circle"></i><br>
                            Todos os produtos estão com estoque adequado!
                        </p>
                    <?php else: ?>
                        <div style="max-height: 300px; overflow-y: auto;">
                            <?php foreach ($lowStockProducts as $product): ?>
                                <div style="display: flex; justify-content: space-between; align-items: center; padding: 0.75rem 0; border-bottom: 1px solid var(--beige);">
                                    <div>
                                        <div style="font-weight: 600;"><?php echo htmlspecialchars($product['name']); ?></div>
                                        <div style="font-size: 0.9rem; color: var(--text-light);">
                                            ID: <?php echo $product['id']; ?>
                                        </div>
                                    </div>
                                    <div style="text-align: right;">
                                        <div style="color: var(--danger); font-weight: 600;">
                                            <?php echo $product['stock_quantity']; ?> unidades
                                        </div>
                                        <a href="products.php?edit=<?php echo $product['id']; ?>" style="font-size: 0.8rem; color: var(--primary-green);">
                                            Editar
                                        </a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
                
                <!-- Recent Orders -->
                <div style="background: var(--white); padding: 2rem; border-radius: 15px; box-shadow: 0 5px 15px var(--shadow);">
                    <h3 style="color: var(--primary-green); margin-bottom: 1.5rem;">
                        <i class="fas fa-list"></i> Pedidos Recentes
                    </h3>
                    
                    <?php if (empty($recentOrders)): ?>
                        <p style="text-align: center; color: var(--text-light); padding: 2rem;">
                            Nenhum pedido encontrado
                        </p>
                    <?php else: ?>
                        <div style="max-height: 300px; overflow-y: auto;">
                            <?php foreach ($recentOrders as $order): ?>
                                <div style="display: flex; justify-content: space-between; align-items: center; padding: 0.75rem 0; border-bottom: 1px solid var(--beige);">
                                    <div>
                                        <div style="font-weight: 600;">#<?php echo $order['id']; ?></div>
                                        <div style="font-size: 0.9rem; color: var(--text-light);">
                                            <?php echo htmlspecialchars($order['customer_name']); ?>
                                        </div>
                                        <div style="font-size: 0.8rem; color: var(--text-light);">
                                            <?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?>
                                        </div>
                                    </div>
                                    <div style="text-align: right;">
                                        <div style="font-weight: 600; color: var(--primary-green);">
                                            R$ <?php echo number_format($order['total_amount'], 2, ',', '.'); ?>
                                        </div>
                                        <div style="font-size: 0.8rem;">
                                            <span class="status-badge status-<?php echo $order['status']; ?>" style="padding: 0.2rem 0.5rem; border-radius: 12px; font-size: 0.7rem; font-weight: 600; color: white; background-color: var(--primary-green);">
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
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <div style="text-align: center; margin-top: 1rem;">
                            <a href="orders.php" class="btn btn-secondary">Ver Todos os Pedidos</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Quick Actions -->
            <div style="margin-top: 3rem;">
                <h3 style="color: var(--primary-green); margin-bottom: 1.5rem; text-align: center;">
                    <i class="fas fa-bolt"></i> Ações Rápidas
                </h3>
                
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
                    <a href="products.php?action=add" class="btn btn-primary" style="text-align: center; padding: 1.5rem;">
                        <i class="fas fa-plus"></i><br>Adicionar Produto
                    </a>
                    
                    <a href="orders.php?status=pending" class="btn btn-warning" style="text-align: center; padding: 1.5rem;">
                        <i class="fas fa-clock"></i><br>Ver Pedidos Pendentes
                    </a>
                    
                    <a href="reports.php" class="btn btn-secondary" style="text-align: center; padding: 1.5rem;">
                        <i class="fas fa-chart-bar"></i><br>Gerar Relatório
                    </a>
                    
                    <a href="customers.php" class="btn btn-secondary" style="text-align: center; padding: 1.5rem;">
                        <i class="fas fa-users"></i><br>Gerenciar Clientes
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
                    <p>Painel Administrativo</p>
                </div>
                <div class="footer-section">
                    <h3>Admin: <?php echo htmlspecialchars($_SESSION['user_name']); ?></h3>
                    <p>Logado desde: <?php echo date('d/m/Y H:i'); ?></p>
                </div>
            </div>
        </div>
    </footer>
</body>
</html>
