<?php
// Simular dados de um pedido para demonstração
$orderId = '2024' . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT);
$orderTotal = 47.50;
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pedido Realizado - Cupcake Store</title>
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
            </div>
        </div>
    </header>

    <!-- Success Message -->
    <section style="padding: 4rem 0; min-height: 70vh;">
        <div class="container">
            <!-- Success Alert -->
            <div style="background: linear-gradient(135deg, #d4edda, #c3e6cb); border: 2px solid #28a745; border-radius: 20px; padding: 2rem; text-align: center; margin-bottom: 3rem; box-shadow: 0 10px 30px rgba(40, 167, 69, 0.2);">
                <div style="font-size: 4rem; color: #28a745; margin-bottom: 1rem;">
                    <i class="fas fa-check-circle"></i>
                </div>
                <h2 style="color: #155724; margin-bottom: 1rem;">🎉 Pedido Realizado com Sucesso!</h2>
                <p style="font-size: 1.1rem; color: #155724; margin-bottom: 0.5rem;">
                    Seu pedido <strong>#<?php echo $orderId; ?></strong> foi confirmado
                </p>
                <p style="color: #155724; font-size: 1rem;">
                    Total: <strong>R$ <?php echo number_format($orderTotal, 2, ',', '.'); ?></strong>
                </p>
            </div>

            <div style="background: var(--white); border-radius: 20px; padding: 2.5rem; box-shadow: 0 15px 35px var(--shadow); margin-bottom: 2rem;">
                <h3 style="color: var(--primary-green); text-align: center; margin-bottom: 2rem;">
                    <i class="fas fa-info-circle"></i> Próximos Passos
                </h3>
                
                <!-- Timeline -->
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 2rem; margin-bottom: 2rem;">
                    <div style="text-align: center;">
                        <div style="color: #28a745; font-size: 2rem; margin-bottom: 0.5rem;">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <h4 style="margin-bottom: 0.5rem;">Pedido Confirmado</h4>
                        <p style="font-size: 0.9rem;">Pagamento processado com sucesso</p>
                    </div>
                    
                    <div style="text-align: center;">
                        <div style="color: var(--primary-green); font-size: 2rem; margin-bottom: 0.5rem;">
                            <i class="fas fa-cookie-bite"></i>
                        </div>
                        <h4 style="margin-bottom: 0.5rem;">Preparação</h4>
                        <p style="font-size: 0.9rem;">Nossos chefs começarão a preparar seus cupcakes</p>
                    </div>
                    
                    <div style="text-align: center;">
                        <div style="color: var(--primary-green); font-size: 2rem; margin-bottom: 0.5rem;">
                            <i class="fas fa-truck"></i>
                        </div>
                        <h4 style="margin-bottom: 0.5rem;">Entrega</h4>
                        <p style="font-size: 0.9rem; background: #e8f5e8; padding: 0.5rem; border-radius: 8px; font-weight: 600;">
                            Entrega prevista em 30 a 50 minutos
                        </p>
                    </div>
                </div>
            </div>

            <!-- Demonstration Notice -->
            <div style="background: #fff3cd; border: 2px solid #ffc107; border-radius: 15px; padding: 1.5rem; text-align: center; margin-bottom: 2rem;">
                <div style="color: #856404; font-size: 2rem; margin-bottom: 0.5rem;">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <h4 style="color: #856404; margin-bottom: 0.5rem;">📋 Esta é uma Demonstração</h4>
                <p style="color: #856404; margin-bottom: 1rem;">
                    Esta página simula como ficaria a tela de sucesso após um pedido real, mostrando a nova mensagem de tempo de entrega.
                </p>
                <div style="background: #fcf8e3; padding: 1rem; border-radius: 8px; margin: 1rem 0;">
                    <p style="color: #856404; margin: 0; font-weight: 600;">
                        ✨ Destaque: A mensagem "Entrega prevista em 30 a 50 minutos" foi alterada de "2-3 dias úteis"
                    </p>
                </div>
            </div>

            <!-- Action Buttons -->
            <div style="text-align: center;">
                <a href="../index.php" class="btn btn-primary" style="margin-right: 1rem;">
                    <i class="fas fa-home"></i> Voltar ao Início
                </a>
                <a href="test_delivery_time.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Voltar ao Teste
                </a>
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
                    <p style="color: var(--primary-green); font-weight: 600;">
                        <i class="fas fa-shipping-fast"></i> Entrega super rápida: 30-50 minutos
                    </p>
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
</body>
</html>
