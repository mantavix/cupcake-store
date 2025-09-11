<?php
session_start();
include_once '../config/database.php';

// Verificar se é admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header('Location: ../pages/login.php');
    exit;
}

$statusFilter = $_GET['status'] ?? '';
$error = '';
$success = '';

// Processar ações
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_status'])) {
        $orderId = (int)$_POST['order_id'];
        $newStatus = $_POST['new_status'];
        
        $validStatuses = ['pending', 'confirmed', 'preparing', 'shipped', 'delivered', 'cancelled'];
        
        if (in_array($newStatus, $validStatuses)) {
            try {
                executeQuery("UPDATE orders SET status = ?, updated_at = NOW() WHERE id = ?", [$newStatus, $orderId]);
                $success = "Status do pedido #{$orderId} atualizado para: " . $newStatus;
            } catch (Exception $e) {
                $error = 'Erro ao atualizar status: ' . $e->getMessage();
            }
        } else {
            $error = 'Status inválido';
        }
    }
}

// Construir query para buscar pedidos
$whereClause = '';
$params = [];

if ($statusFilter) {
    $whereClause = 'WHERE o.status = ?';
    $params[] = $statusFilter;
}

try {
    $orders = fetchData("
        SELECT o.*, u.name as customer_name, u.email as customer_email, u.phone as customer_phone
        FROM orders o
        JOIN users u ON o.user_id = u.id
        {$whereClause}
        ORDER BY o.created_at DESC
    ", $params);
} catch (Exception $e) {
    $orders = [];
}

// Contar pedidos por status
try {
    $statusCounts = fetchData("
        SELECT status, COUNT(*) as count 
        FROM orders 
        GROUP BY status
    ");
    $statusCountsArray = [];
    foreach ($statusCounts as $count) {
        $statusCountsArray[$count['status']] = $count['count'];
    }
} catch (Exception $e) {
    $statusCountsArray = [];
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Pedidos - Cupcake Store Admin</title>
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
                <i class="fas fa-shopping-cart"></i> Gerenciar Pedidos
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
            
            <!-- Status Filter -->
            <div style="background: var(--white); padding: 1.5rem; border-radius: 15px; box-shadow: 0 5px 15px var(--shadow); margin-bottom: 2rem;">
                <h3 style="color: var(--primary-green); margin-bottom: 1rem;">
                    <i class="fas fa-filter"></i> Filtrar por Status
                </h3>
                
                <div style="display: flex; flex-wrap: wrap; gap: 1rem;">
                    <a href="orders.php" class="btn <?php echo !$statusFilter ? 'btn-primary' : 'btn-secondary'; ?>">
                        Todos (<?php echo array_sum($statusCountsArray); ?>)
                    </a>
                    
                    <a href="orders.php?status=pending" class="btn <?php echo $statusFilter === 'pending' ? 'btn-primary' : 'btn-secondary'; ?>">
                        Pendentes (<?php echo $statusCountsArray['pending'] ?? 0; ?>)
                    </a>
                    
                    <a href="orders.php?status=confirmed" class="btn <?php echo $statusFilter === 'confirmed' ? 'btn-primary' : 'btn-secondary'; ?>">
                        Confirmados (<?php echo $statusCountsArray['confirmed'] ?? 0; ?>)
                    </a>
                    
                    <a href="orders.php?status=preparing" class="btn <?php echo $statusFilter === 'preparing' ? 'btn-primary' : 'btn-secondary'; ?>">
                        Preparando (<?php echo $statusCountsArray['preparing'] ?? 0; ?>)
                    </a>
                    
                    <a href="orders.php?status=shipped" class="btn <?php echo $statusFilter === 'shipped' ? 'btn-primary' : 'btn-secondary'; ?>">
                        Enviados (<?php echo $statusCountsArray['shipped'] ?? 0; ?>)
                    </a>
                    
                    <a href="orders.php?status=delivered" class="btn <?php echo $statusFilter === 'delivered' ? 'btn-primary' : 'btn-secondary'; ?>">
                        Entregues (<?php echo $statusCountsArray['delivered'] ?? 0; ?>)
                    </a>
                    
                    <a href="orders.php?status=cancelled" class="btn <?php echo $statusFilter === 'cancelled' ? 'btn-primary' : 'btn-secondary'; ?>">
                        Cancelados (<?php echo $statusCountsArray['cancelled'] ?? 0; ?>)
                    </a>
                </div>
            </div>
            
            <!-- Orders List -->
            <div style="background: var(--white); border-radius: 15px; box-shadow: 0 5px 15px var(--shadow); overflow: hidden;">
                <div style="padding: 1.5rem; border-bottom: 2px solid var(--beige);">
                    <h3 style="color: var(--primary-green);">
                        <i class="fas fa-list"></i> 
                        <?php 
                        if ($statusFilter) {
                            $statusLabels = [
                                'pending' => 'Pedidos Pendentes',
                                'confirmed' => 'Pedidos Confirmados',
                                'preparing' => 'Pedidos em Preparo',
                                'shipped' => 'Pedidos Enviados',
                                'delivered' => 'Pedidos Entregues',
                                'cancelled' => 'Pedidos Cancelados'
                            ];
                            echo $statusLabels[$statusFilter];
                        } else {
                            echo 'Todos os Pedidos';
                        }
                        ?>
                        (<?php echo count($orders); ?>)
                    </h3>
                </div>
                
                <?php if (empty($orders)): ?>
                    <div style="text-align: center; padding: 3rem; color: var(--text-light);">
                        <i class="fas fa-shopping-cart" style="font-size: 3rem; margin-bottom: 1rem;"></i>
                        <p>Nenhum pedido encontrado</p>
                    </div>
                <?php else: ?>
                    <div style="overflow-x: auto;">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Pedido</th>
                                    <th>Cliente</th>
                                    <th>Data</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th>Pagamento</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($orders as $order): ?>
                                    <tr>
                                        <td style="font-weight: 600;">#<?php echo $order['id']; ?></td>
                                        <td>
                                            <div style="font-weight: 600;"><?php echo htmlspecialchars($order['customer_name']); ?></div>
                                            <div style="font-size: 0.9rem; color: var(--text-light);">
                                                <?php echo htmlspecialchars($order['customer_email']); ?>
                                            </div>
                                            <div style="font-size: 0.9rem; color: var(--text-light);">
                                                <?php echo htmlspecialchars($order['customer_phone']); ?>
                                            </div>
                                        </td>
                                        <td>
                                            <div><?php echo date('d/m/Y', strtotime($order['created_at'])); ?></div>
                                            <div style="font-size: 0.9rem; color: var(--text-light);">
                                                <?php echo date('H:i', strtotime($order['created_at'])); ?>
                                            </div>
                                        </td>
                                        <td style="font-weight: 600; color: var(--primary-green);">
                                            R$ <?php echo number_format($order['total_amount'], 2, ',', '.'); ?>
                                        </td>
                                        <td>
                                            <form method="POST" style="display: inline;">
                                                <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                                <select name="new_status" onchange="this.form.submit()" 
                                                        style="padding: 0.25rem; border-radius: 5px; border: 1px solid var(--beige);">
                                                    <option value="pending" <?php echo $order['status'] === 'pending' ? 'selected' : ''; ?>>Pendente</option>
                                                    <option value="confirmed" <?php echo $order['status'] === 'confirmed' ? 'selected' : ''; ?>>Confirmado</option>
                                                    <option value="preparing" <?php echo $order['status'] === 'preparing' ? 'selected' : ''; ?>>Preparando</option>
                                                    <option value="shipped" <?php echo $order['status'] === 'shipped' ? 'selected' : ''; ?>>Enviado</option>
                                                    <option value="delivered" <?php echo $order['status'] === 'delivered' ? 'selected' : ''; ?>>Entregue</option>
                                                    <option value="cancelled" <?php echo $order['status'] === 'cancelled' ? 'selected' : ''; ?>>Cancelado</option>
                                                </select>
                                                <input type="hidden" name="update_status" value="1">
                                            </form>
                                        </td>
                                        <td>
                                            <?php 
                                            $paymentLabels = [
                                                'credit_card' => 'Cartão de Crédito',
                                                'debit_card' => 'Cartão de Débito',
                                                'pix' => 'PIX',
                                                'cash_on_delivery' => 'Dinheiro na Entrega'
                                            ];
                                            echo $paymentLabels[$order['payment_method']] ?? $order['payment_method'];
                                            ?>
                                        </td>
                                        <td>
                                            <button class="btn btn-secondary" style="padding: 0.25rem 0.5rem; font-size: 0.8rem;" 
                                                    onclick="viewOrderDetails(<?php echo $order['id']; ?>)">
                                                <i class="fas fa-eye"></i> Ver
                                            </button>
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

    <!-- Modal para detalhes do pedido -->
    <div id="orderModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
        <div style="background: var(--white); padding: 2rem; border-radius: 15px; max-width: 600px; max-height: 80vh; overflow-y: auto; position: relative;">
            <button onclick="closeOrderModal()" style="position: absolute; top: 1rem; right: 1rem; background: none; border: none; font-size: 1.5rem; cursor: pointer;">
                <i class="fas fa-times"></i>
            </button>
            <div id="orderDetails">
                <!-- Conteúdo será carregado via JavaScript -->
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3>Cupcake Store</h3>
                    <p>Painel Administrativo - Pedidos</p>
                </div>
                <div class="footer-section">
                    <h3>Resumo</h3>
                    <p>Total de Pedidos: <?php echo count($orders); ?></p>
                    <p>Valor Total: R$ <?php echo number_format(array_sum(array_column($orders, 'total_amount')), 2, ',', '.'); ?></p>
                </div>
            </div>
        </div>
    </footer>

    <script>
        async function viewOrderDetails(orderId) {
            try {
                const response = await fetch(`../api/get_order_details.php?id=${orderId}`);
                const data = await response.json();
                
                if (data.success) {
                    document.getElementById('orderDetails').innerHTML = data.html;
                    document.getElementById('orderModal').style.display = 'flex';
                } else {
                    alert('Erro ao carregar detalhes do pedido');
                }
            } catch (error) {
                alert('Erro ao carregar detalhes do pedido');
            }
        }
        
        function closeOrderModal() {
            document.getElementById('orderModal').style.display = 'none';
        }
        
        // Fechar modal clicando fora
        document.getElementById('orderModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeOrderModal();
            }
        });
    </script>
</body>
</html>
