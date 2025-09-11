<?php
session_start();
include_once '../config/database.php';

// Verificar se é admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header('Location: ../pages/login.php');
    exit;
}

$action = $_GET['action'] ?? '';
$editId = $_GET['edit'] ?? 0;
$error = '';
$success = '';

// Processar ações
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_product'])) {
        $name = trim($_POST['name']);
        $description = trim($_POST['description']);
        $price = (float)$_POST['price'];
        $stock = (int)$_POST['stock_quantity'];
        $image = trim($_POST['image']) ?: 'default.jpg';
        
        if ($name && $description && $price > 0 && $stock >= 0) {
            try {
                executeQuery("INSERT INTO products (name, description, price, image, stock_quantity) VALUES (?, ?, ?, ?, ?)", 
                            [$name, $description, $price, $image, $stock]);
                $success = 'Produto adicionado com sucesso!';
            } catch (Exception $e) {
                $error = 'Erro ao adicionar produto: ' . $e->getMessage();
            }
        } else {
            $error = 'Todos os campos são obrigatórios e devem ter valores válidos';
        }
    }
    
    if (isset($_POST['update_product'])) {
        $id = (int)$_POST['id'];
        $name = trim($_POST['name']);
        $description = trim($_POST['description']);
        $price = (float)$_POST['price'];
        $stock = (int)$_POST['stock_quantity'];
        $image = trim($_POST['image']) ?: 'default.jpg';
        $isActive = isset($_POST['is_active']) ? 1 : 0;
        
        if ($name && $description && $price > 0 && $stock >= 0) {
            try {
                executeQuery("UPDATE products SET name = ?, description = ?, price = ?, image = ?, stock_quantity = ?, is_active = ? WHERE id = ?", 
                            [$name, $description, $price, $image, $stock, $isActive, $id]);
                $success = 'Produto atualizado com sucesso!';
                $editId = 0; // Sair do modo de edição
            } catch (Exception $e) {
                $error = 'Erro ao atualizar produto: ' . $e->getMessage();
            }
        } else {
            $error = 'Todos os campos são obrigatórios e devem ter valores válidos';
        }
    }
    
    if (isset($_POST['delete_product'])) {
        $id = (int)$_POST['id'];
        try {
            executeQuery("UPDATE products SET is_active = 0 WHERE id = ?", [$id]);
            $success = 'Produto removido com sucesso!';
        } catch (Exception $e) {
            $error = 'Erro ao remover produto: ' . $e->getMessage();
        }
    }
}

// Buscar produtos
try {
    $products = fetchData("SELECT * FROM products ORDER BY created_at DESC");
    
    // Se está editando, buscar dados do produto
    $editProduct = null;
    if ($editId > 0) {
        $editProduct = fetchOne("SELECT * FROM products WHERE id = ?", [$editId]);
    }
} catch (Exception $e) {
    $products = [];
    $editProduct = null;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Produtos - Cupcake Store Admin</title>
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
                    <a href="dashboard.php" style="color: white; text-decoration: none; display: flex; align-items: center;">
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

    <!-- Content -->
    <section style="padding: 2rem 0; background-color: var(--light-beige); min-height: 100vh;">
        <div class="container">
            <h2 style="color: var(--primary-green); margin-bottom: 2rem;">
                <i class="fas fa-birthday-cake"></i> Gerenciar Produtos
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
            
            <!-- Add/Edit Product Form -->
            <div style="background: var(--white); padding: 2rem; border-radius: 15px; box-shadow: 0 5px 15px var(--shadow); margin-bottom: 2rem;">
                <h3 style="color: var(--primary-green); margin-bottom: 1.5rem;">
                    <i class="fas fa-<?php echo $editProduct ? 'edit' : 'plus'; ?>"></i> 
                    <?php echo $editProduct ? 'Editar Produto' : 'Adicionar Novo Produto'; ?>
                </h3>
                
                <form method="POST" style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                    <?php if ($editProduct): ?>
                        <input type="hidden" name="id" value="<?php echo $editProduct['id']; ?>">
                    <?php endif; ?>
                    
                    <div class="form-group">
                        <label for="name">Nome do Produto *</label>
                        <input type="text" id="name" name="name" required 
                               value="<?php echo $editProduct ? htmlspecialchars($editProduct['name']) : ''; ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="price">Preço (R$) *</label>
                        <input type="number" id="price" name="price" step="0.01" min="0" required 
                               value="<?php echo $editProduct ? $editProduct['price'] : ''; ?>">
                    </div>
                    
                    <div class="form-group" style="grid-column: 1 / -1;">
                        <label for="description">Descrição *</label>
                        <textarea id="description" name="description" rows="3" required><?php echo $editProduct ? htmlspecialchars($editProduct['description']) : ''; ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="stock_quantity">Quantidade em Estoque *</label>
                        <input type="number" id="stock_quantity" name="stock_quantity" min="0" required 
                               value="<?php echo $editProduct ? $editProduct['stock_quantity'] : ''; ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="image">Imagem (nome do arquivo)</label>
                        <input type="text" id="image" name="image" placeholder="ex: chocolate.jpg" 
                               value="<?php echo $editProduct ? htmlspecialchars($editProduct['image']) : ''; ?>">
                    </div>
                    
                    <?php if ($editProduct): ?>
                        <div class="form-group">
                            <label>
                                <input type="checkbox" name="is_active" <?php echo $editProduct['is_active'] ? 'checked' : ''; ?>>
                                Produto Ativo
                            </label>
                        </div>
                    <?php endif; ?>
                    
                    <div style="grid-column: 1 / -1; text-align: center; margin-top: 1rem;">
                        <button type="submit" name="<?php echo $editProduct ? 'update_product' : 'add_product'; ?>" 
                                class="btn btn-primary" style="margin-right: 1rem;">
                            <i class="fas fa-<?php echo $editProduct ? 'save' : 'plus'; ?>"></i> 
                            <?php echo $editProduct ? 'Atualizar Produto' : 'Adicionar Produto'; ?>
                        </button>
                        
                        <?php if ($editProduct): ?>
                            <a href="products.php" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancelar
                            </a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
            
            <!-- Products List -->
            <div style="background: var(--white); border-radius: 15px; box-shadow: 0 5px 15px var(--shadow); overflow: hidden;">
                <div style="padding: 1.5rem; border-bottom: 2px solid var(--beige);">
                    <h3 style="color: var(--primary-green);">
                        <i class="fas fa-list"></i> Lista de Produtos (<?php echo count($products); ?>)
                    </h3>
                </div>
                
                <?php if (empty($products)): ?>
                    <div style="text-align: center; padding: 3rem; color: var(--text-light);">
                        <i class="fas fa-box-open" style="font-size: 3rem; margin-bottom: 1rem;"></i>
                        <p>Nenhum produto cadastrado ainda</p>
                    </div>
                <?php else: ?>
                    <div style="overflow-x: auto;">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nome</th>
                                    <th>Descrição</th>
                                    <th>Preço</th>
                                    <th>Estoque</th>
                                    <th>Status</th>
                                    <th>Criado em</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($products as $product): ?>
                                    <tr style="<?php echo !$product['is_active'] ? 'opacity: 0.6;' : ''; ?>">
                                        <td><?php echo $product['id']; ?></td>
                                        <td style="font-weight: 600;"><?php echo htmlspecialchars($product['name']); ?></td>
                                        <td style="max-width: 200px; overflow: hidden; text-overflow: ellipsis;">
                                            <?php echo htmlspecialchars(substr($product['description'], 0, 50)) . (strlen($product['description']) > 50 ? '...' : ''); ?>
                                        </td>
                                        <td style="font-weight: 600; color: var(--primary-green);">
                                            R$ <?php echo number_format($product['price'], 2, ',', '.'); ?>
                                        </td>
                                        <td>
                                            <span style="color: <?php echo $product['stock_quantity'] <= 5 ? 'var(--danger)' : 'var(--text-dark)'; ?>; font-weight: 600;">
                                                <?php echo $product['stock_quantity']; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span style="padding: 0.25rem 0.5rem; border-radius: 12px; font-size: 0.8rem; font-weight: 600; color: white; background-color: <?php echo $product['is_active'] ? 'var(--success)' : 'var(--danger)'; ?>;">
                                                <?php echo $product['is_active'] ? 'Ativo' : 'Inativo'; ?>
                                            </span>
                                        </td>
                                        <td><?php echo date('d/m/Y', strtotime($product['created_at'])); ?></td>
                                        <td>
                                            <div style="display: flex; gap: 0.5rem;">
                                                <a href="products.php?edit=<?php echo $product['id']; ?>" 
                                                   class="btn btn-secondary" style="padding: 0.25rem 0.5rem; font-size: 0.8rem;">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                
                                                <?php if ($product['is_active']): ?>
                                                    <form method="POST" style="display: inline;" 
                                                          onsubmit="return confirm('Tem certeza que deseja remover este produto?')">
                                                        <input type="hidden" name="id" value="<?php echo $product['id']; ?>">
                                                        <button type="submit" name="delete_product" 
                                                                class="btn btn-danger" style="padding: 0.25rem 0.5rem; font-size: 0.8rem;">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                <?php endif; ?>
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

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3>Cupcake Store</h3>
                    <p>Painel Administrativo - Produtos</p>
                </div>
                <div class="footer-section">
                    <h3>Total de Produtos</h3>
                    <p>Ativos: <?php echo count(array_filter($products, function($p) { return $p['is_active']; })); ?></p>
                    <p>Inativos: <?php echo count(array_filter($products, function($p) { return !$p['is_active']; })); ?></p>
                </div>
            </div>
        </div>
    </footer>
</body>
</html>
