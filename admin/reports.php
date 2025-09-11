<?php
session_start();
include_once '../config/database.php';

// Verificar se é admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header('Location: ../pages/login.php');
    exit;
}

// Período padrão (último mês)
$startDate = $_GET['start_date'] ?? date('Y-m-01');
$endDate = $_GET['end_date'] ?? date('Y-m-t');

try {
    // Vendas por período
    $salesData = fetchData("
        SELECT 
            DATE(created_at) as date,
            COUNT(*) as orders_count,
            SUM(total_amount) as total_sales
        FROM orders 
        WHERE created_at BETWEEN ? AND ? 
        AND status != 'cancelled'
        GROUP BY DATE(created_at)
        ORDER BY date DESC
    ", [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
    
    // Produtos mais vendidos
    $topProducts = fetchData("
        SELECT 
            p.name,
            SUM(oi.quantity) as total_sold,
            SUM(oi.quantity * oi.price) as total_revenue
        FROM order_items oi
        JOIN products p ON oi.product_id = p.id
        JOIN orders o ON oi.order_id = o.id
        WHERE o.created_at BETWEEN ? AND ?
        AND o.status != 'cancelled'
        GROUP BY p.id, p.name
        ORDER BY total_sold DESC
        LIMIT 10
    ", [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
    
    // Resumo geral
    $summary = fetchOne("
        SELECT 
            COUNT(*) as total_orders,
            SUM(total_amount) as total_revenue,
            AVG(total_amount) as avg_order_value,
            COUNT(DISTINCT user_id) as unique_customers
        FROM orders 
        WHERE created_at BETWEEN ? AND ?
        AND status != 'cancelled'
    ", [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
    
    // Status dos pedidos
    $orderStatus = fetchData("
        SELECT 
            status,
            COUNT(*) as count,
            SUM(total_amount) as total_amount
        FROM orders 
        WHERE created_at BETWEEN ? AND ?
        GROUP BY status
        ORDER BY count DESC
    ", [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
    
    // Métodos de pagamento
    $paymentMethods = fetchData("
        SELECT 
            payment_method,
            COUNT(*) as count,
            SUM(total_amount) as total_amount
        FROM orders 
        WHERE created_at BETWEEN ? AND ?
        AND status != 'cancelled'
        GROUP BY payment_method
        ORDER BY count DESC
    ", [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
    
} catch (Exception $e) {
    $salesData = [];
    $topProducts = [];
    $summary = ['total_orders' => 0, 'total_revenue' => 0, 'avg_order_value' => 0, 'unique_customers' => 0];
    $orderStatus = [];
    $paymentMethods = [];
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relatórios - Cupcake Store Admin</title>
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
                <i class="fas fa-chart-bar"></i> Relatórios de Vendas
            </h2>
            
            <!-- Period Filter -->
            <div style="background: var(--white); padding: 2rem; border-radius: 15px; box-shadow: 0 5px 15px var(--shadow); margin-bottom: 2rem;">
                <h3 style="color: var(--primary-green); margin-bottom: 1rem;">
                    <i class="fas fa-calendar"></i> Período
                </h3>
                
                <form method="GET" style="display: flex; gap: 1rem; align-items: end; flex-wrap: wrap;">
                    <div class="form-group" style="margin-bottom: 0;">
                        <label for="start_date">Data Inicial</label>
                        <input type="date" id="start_date" name="start_date" value="<?php echo $startDate; ?>">
                    </div>
                    
                    <div class="form-group" style="margin-bottom: 0;">
                        <label for="end_date">Data Final</label>
                        <input type="date" id="end_date" name="end_date" value="<?php echo $endDate; ?>">
                    </div>
                    
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-filter"></i> Filtrar
                    </button>
                    
                    <div style="display: flex; gap: 0.5rem;">
                        <a href="reports.php?start_date=<?php echo date('Y-m-d'); ?>&end_date=<?php echo date('Y-m-d'); ?>" 
                           class="btn btn-secondary">Hoje</a>
                        <a href="reports.php?start_date=<?php echo date('Y-m-01'); ?>&end_date=<?php echo date('Y-m-t'); ?>" 
                           class="btn btn-secondary">Este Mês</a>
                        <a href="reports.php?start_date=<?php echo date('Y-01-01'); ?>&end_date=<?php echo date('Y-12-31'); ?>" 
                           class="btn btn-secondary">Este Ano</a>
                    </div>
                </form>
            </div>
            
            <!-- Summary Cards -->
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem; margin-bottom: 3rem;">
                <div class="stat-card" style="background: linear-gradient(135deg, var(--primary-green), var(--light-green)); color: white; padding: 2rem; border-radius: 15px; text-align: center; box-shadow: 0 5px 15px var(--shadow);">
                    <div style="font-size: 3rem; margin-bottom: 0.5rem;">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                    <h3 style="font-size: 2rem; margin-bottom: 0.5rem;"><?php echo $summary['total_orders'] ?? 0; ?></h3>
                    <p>Total de Pedidos</p>
                </div>
                
                <div class="stat-card" style="background: linear-gradient(135deg, var(--success), #4caf50); color: white; padding: 2rem; border-radius: 15px; text-align: center; box-shadow: 0 5px 15px var(--shadow);">
                    <div style="font-size: 3rem; margin-bottom: 0.5rem;">
                        <i class="fas fa-dollar-sign"></i>
                    </div>
                    <h3 style="font-size: 2rem; margin-bottom: 0.5rem;">R$ <?php echo number_format($summary['total_revenue'] ?? 0, 2, ',', '.'); ?></h3>
                    <p>Receita Total</p>
                </div>
                
                <div class="stat-card" style="background: linear-gradient(135deg, #ff9800, #f57c00); color: white; padding: 2rem; border-radius: 15px; text-align: center; box-shadow: 0 5px 15px var(--shadow);">
                    <div style="font-size: 3rem; margin-bottom: 0.5rem;">
                        <i class="fas fa-calculator"></i>
                    </div>
                    <h3 style="font-size: 2rem; margin-bottom: 0.5rem;">R$ <?php echo number_format($summary['avg_order_value'] ?? 0, 2, ',', '.'); ?></h3>
                    <p>Ticket Médio</p>
                </div>
                
                <div class="stat-card" style="background: linear-gradient(135deg, #9c27b0, #673ab7); color: white; padding: 2rem; border-radius: 15px; text-align: center; box-shadow: 0 5px 15px var(--shadow);">
                    <div style="font-size: 3rem; margin-bottom: 0.5rem;">
                        <i class="fas fa-users"></i>
                    </div>
                    <h3 style="font-size: 2rem; margin-bottom: 0.5rem;"><?php echo $summary['unique_customers'] ?? 0; ?></h3>
                    <p>Clientes Únicos</p>
                </div>
            </div>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; margin-bottom: 2rem;">
                <!-- Top Products -->
                <div style="background: var(--white); padding: 2rem; border-radius: 15px; box-shadow: 0 5px 15px var(--shadow);">
                    <h3 style="color: var(--primary-green); margin-bottom: 1.5rem;">
                        <i class="fas fa-star"></i> Produtos Mais Vendidos
                    </h3>
                    
                    <?php if (empty($topProducts)): ?>
                        <p style="text-align: center; color: var(--text-light); padding: 2rem;">
                            Nenhuma venda no período selecionado
                        </p>
                    <?php else: ?>
                        <div style="max-height: 400px; overflow-y: auto;">
                            <?php foreach ($topProducts as $index => $product): ?>
                                <div style="display: flex; justify-content: space-between; align-items: center; padding: 0.75rem 0; border-bottom: 1px solid var(--beige);">
                                    <div>
                                        <div style="font-weight: 600;"><?php echo ($index + 1) . '. ' . htmlspecialchars($product['name']); ?></div>
                                        <div style="font-size: 0.9rem; color: var(--text-light);">
                                            <?php echo $product['total_sold']; ?> unidades vendidas
                                        </div>
                                    </div>
                                    <div style="text-align: right;">
                                        <div style="font-weight: 600; color: var(--primary-green);">
                                            R$ <?php echo number_format($product['total_revenue'], 2, ',', '.'); ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
                
                <!-- Order Status -->
                <div style="background: var(--white); padding: 2rem; border-radius: 15px; box-shadow: 0 5px 15px var(--shadow);">
                    <h3 style="color: var(--primary-green); margin-bottom: 1.5rem;">
                        <i class="fas fa-chart-pie"></i> Status dos Pedidos
                    </h3>
                    
                    <?php if (empty($orderStatus)): ?>
                        <p style="text-align: center; color: var(--text-light); padding: 2rem;">
                            Nenhum pedido no período selecionado
                        </p>
                    <?php else: ?>
                        <?php 
                        $statusLabels = [
                            'pending' => 'Pendentes',
                            'confirmed' => 'Confirmados',
                            'preparing' => 'Preparando',
                            'shipped' => 'Enviados',
                            'delivered' => 'Entregues',
                            'cancelled' => 'Cancelados'
                        ];
                        ?>
                        <div>
                            <?php foreach ($orderStatus as $status): ?>
                                <div style="display: flex; justify-content: space-between; align-items: center; padding: 0.75rem 0; border-bottom: 1px solid var(--beige);">
                                    <div>
                                        <div style="font-weight: 600;"><?php echo $statusLabels[$status['status']] ?? $status['status']; ?></div>
                                        <div style="font-size: 0.9rem; color: var(--text-light);">
                                            <?php echo $status['count']; ?> pedidos
                                        </div>
                                    </div>
                                    <div style="text-align: right;">
                                        <div style="font-weight: 600; color: var(--primary-green);">
                                            R$ <?php echo number_format($status['total_amount'], 2, ',', '.'); ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
                <!-- Sales by Date -->
                <div style="background: var(--white); padding: 2rem; border-radius: 15px; box-shadow: 0 5px 15px var(--shadow);">
                    <h3 style="color: var(--primary-green); margin-bottom: 1.5rem;">
                        <i class="fas fa-chart-line"></i> Vendas por Data
                    </h3>
                    
                    <?php if (empty($salesData)): ?>
                        <p style="text-align: center; color: var(--text-light); padding: 2rem;">
                            Nenhuma venda no período selecionado
                        </p>
                    <?php else: ?>
                        <div style="max-height: 400px; overflow-y: auto;">
                            <?php foreach ($salesData as $sale): ?>
                                <div style="display: flex; justify-content: space-between; align-items: center; padding: 0.75rem 0; border-bottom: 1px solid var(--beige);">
                                    <div>
                                        <div style="font-weight: 600;"><?php echo date('d/m/Y', strtotime($sale['date'])); ?></div>
                                        <div style="font-size: 0.9rem; color: var(--text-light);">
                                            <?php echo $sale['orders_count']; ?> pedidos
                                        </div>
                                    </div>
                                    <div style="text-align: right;">
                                        <div style="font-weight: 600; color: var(--primary-green);">
                                            R$ <?php echo number_format($sale['total_sales'], 2, ',', '.'); ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
                
                <!-- Payment Methods -->
                <div style="background: var(--white); padding: 2rem; border-radius: 15px; box-shadow: 0 5px 15px var(--shadow);">
                    <h3 style="color: var(--primary-green); margin-bottom: 1.5rem;">
                        <i class="fas fa-credit-card"></i> Métodos de Pagamento
                    </h3>
                    
                    <?php if (empty($paymentMethods)): ?>
                        <p style="text-align: center; color: var(--text-light); padding: 2rem;">
                            Nenhuma venda no período selecionado
                        </p>
                    <?php else: ?>
                        <?php 
                        $paymentLabels = [
                            'credit_card' => 'Cartão de Crédito',
                            'debit_card' => 'Cartão de Débito',
                            'pix' => 'PIX',
                            'cash_on_delivery' => 'Dinheiro na Entrega'
                        ];
                        ?>
                        <div>
                            <?php foreach ($paymentMethods as $payment): ?>
                                <div style="display: flex; justify-content: space-between; align-items: center; padding: 0.75rem 0; border-bottom: 1px solid var(--beige);">
                                    <div>
                                        <div style="font-weight: 600;"><?php echo $paymentLabels[$payment['payment_method']] ?? $payment['payment_method']; ?></div>
                                        <div style="font-size: 0.9rem; color: var(--text-light);">
                                            <?php echo $payment['count']; ?> transações
                                        </div>
                                    </div>
                                    <div style="text-align: right;">
                                        <div style="font-weight: 600; color: var(--primary-green);">
                                            R$ <?php echo number_format($payment['total_amount'], 2, ',', '.'); ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Export Buttons -->
            <div style="text-align: center; margin-top: 3rem;">
                <h3 style="color: var(--primary-green); margin-bottom: 1rem;">
                    <i class="fas fa-download"></i> Exportar Dados
                </h3>
                <div style="display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap;">
                    <button onclick="exportToPDF()" class="btn btn-danger">
                        <i class="fas fa-file-pdf"></i> Exportar PDF
                    </button>
                    <button onclick="exportToCSV()" class="btn btn-success">
                        <i class="fas fa-file-csv"></i> Exportar CSV
                    </button>
                    <button onclick="window.print()" class="btn btn-secondary">
                        <i class="fas fa-print"></i> Imprimir
                    </button>
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
                    <p>Relatório de <?php echo date('d/m/Y', strtotime($startDate)); ?> a <?php echo date('d/m/Y', strtotime($endDate)); ?></p>
                </div>
                <div class="footer-section">
                    <h3>Gerado em</h3>
                    <p><?php echo date('d/m/Y H:i'); ?></p>
                    <p>Por: <?php echo htmlspecialchars($_SESSION['user_name']); ?></p>
                </div>
            </div>
        </div>
    </footer>

    <script>
        function exportToPDF() {
            alert('Funcionalidade de exportação PDF será implementada com uma biblioteca específica');
        }
        
        function exportToCSV() {
            alert('Funcionalidade de exportação CSV será implementada');
        }
    </script>
</body>
</html>
