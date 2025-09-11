<?php
session_start();
header('Content-Type: text/html; charset=utf-8');

echo "<h2>Teste das Alterações de Tempo de Entrega</h2>";

echo "<h3>✅ Alterações Implementadas</h3>";
echo "<div style='background: #d4edda; padding: 1rem; border-radius: 8px; border-left: 4px solid #28a745; margin: 1rem 0;'>";
echo "<p><strong>1. Página de Sucesso do Pedido:</strong></p>";
echo "<p>✓ Alterado de: <em>'Entrega prevista em 2-3 dias úteis'</em></p>";
echo "<p>✓ Para: <strong>'Entrega prevista em 30 a 50 minutos'</strong></p>";
echo "<br>";
echo "<p><strong>2. Página do Carrinho:</strong></p>";
echo "<p>✓ Adicionada informação: <strong>'Entrega: 30-50 min'</strong> no resumo</p>";
echo "<br>";
echo "<p><strong>3. Página de Checkout:</strong></p>";
echo "<p>✓ Adicionada informação: <strong>'Entrega: 30-50 min'</strong> no resumo do pedido</p>";
echo "</div>";

echo "<h3>🧪 Links para Teste</h3>";
echo "<div style='display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1rem; margin: 1rem 0;'>";

// Link para carrinho (precisa estar logado e ter itens)
echo "<div style='background: var(--light-beige); padding: 1rem; border-radius: 8px; text-align: center;'>";
echo "<h4 style='color: var(--primary-green);'>🛒 Carrinho</h4>";
echo "<p style='font-size: 0.9rem;'>Visualize a nova informação de entrega</p>";
echo "<a href='../pages/cart.php' class='btn btn-primary' target='_blank'>Abrir Carrinho</a>";
echo "<p style='font-size: 0.8rem; color: var(--text-light);'>*Necessário estar logado com itens no carrinho</p>";
echo "</div>";

// Link para checkout (precisa estar logado e ter itens)
echo "<div style='background: var(--light-beige); padding: 1rem; border-radius: 8px; text-align: center;'>";
echo "<h4 style='color: var(--primary-green);'>💳 Checkout</h4>";
echo "<p style='font-size: 0.9rem;'>Confirme a informação no resumo do pedido</p>";
echo "<a href='../pages/checkout.php' class='btn btn-primary' target='_blank'>Abrir Checkout</a>";
echo "<p style='font-size: 0.8rem; color: var(--text-light);'>*Necessário estar logado com itens no carrinho</p>";
echo "</div>";

// Simulação da página de sucesso
echo "<div style='background: var(--light-beige); padding: 1rem; border-radius: 8px; text-align: center;'>";
echo "<h4 style='color: var(--primary-green);'>🎉 Página de Sucesso</h4>";
echo "<p style='font-size: 0.9rem;'>Simular pedido concluído</p>";
echo "<a href='simulate_order_success.php' class='btn btn-success' target='_blank'>Simular Pedido</a>";
echo "<p style='font-size: 0.8rem; color: var(--text-light);'>*Simulação para teste</p>";
echo "</div>";

echo "</div>";

echo "<h3>📋 Fluxo de Teste Completo</h3>";
echo "<ol>";
echo "<li><strong>Faça login:</strong> <a href='../pages/login.php' target='_blank'>Página de Login</a></li>";
echo "<li><strong>Adicione produtos ao carrinho:</strong> <a href='../index.php' target='_blank'>Página Principal</a></li>";
echo "<li><strong>Verifique no carrinho:</strong> Procure por 'Entrega: 30-50 min'</li>";
echo "<li><strong>Vá para checkout:</strong> Confirme a informação no resumo</li>";
echo "<li><strong>Finalize o pedido:</strong> Veja a mensagem '30 a 50 minutos' na página de sucesso</li>";
echo "</ol>";

echo "<h3>🎨 Visual das Alterações</h3>";
echo "<div style='background: var(--white); padding: 1.5rem; border-radius: 8px; border: 1px solid var(--beige); margin: 1rem 0;'>";
echo "<h4 style='color: var(--primary-green);'>💡 Resumo do Pedido (Exemplo)</h4>";
echo "<div style='border: 1px solid #ddd; padding: 1rem; border-radius: 8px;'>";
echo "<div style='display: flex; justify-content: space-between; margin-bottom: 0.5rem;'>";
echo "<span>Subtotal:</span><span>R$ 45,00</span>";
echo "</div>";
echo "<div style='display: flex; justify-content: space-between; margin-bottom: 0.5rem;'>";
echo "<span>Frete:</span><span style='color: var(--success);'>GRÁTIS</span>";
echo "</div>";
echo "<div style='display: flex; justify-content: space-between; margin-bottom: 0.5rem; background: #e8f5e8; padding: 0.5rem; border-radius: 4px;'>";
echo "<span>Entrega:</span><span style='color: var(--primary-green); font-weight: 600;'>30-50 min</span>";
echo "</div>";
echo "<div style='display: flex; justify-content: space-between; font-size: 1.2rem; font-weight: 700; color: var(--primary-green); border-top: 2px solid var(--primary-green); padding-top: 0.5rem; margin-top: 0.5rem;'>";
echo "<span>Total:</span><span>R$ 45,00</span>";
echo "</div>";
echo "</div>";
echo "</div>";

echo "<h3>📊 Impacto das Mudanças</h3>";
echo "<div style='background: #fff3cd; padding: 1rem; border-radius: 8px; border-left: 4px solid #ffc107; margin: 1rem 0;'>";
echo "<p><strong>Benefícios para o cliente:</strong></p>";
echo "<ul>";
echo "<li>🚀 <strong>Expectativa de entrega super rápida:</strong> De dias para minutos</li>";
echo "<li>📱 <strong>Entrega express:</strong> Ideal para cupcakes frescos</li>";
echo "<li>⭐ <strong>Diferencial competitivo:</strong> Entrega muito mais rápida que concorrentes</li>";
echo "<li>😋 <strong>Produto fresco:</strong> Cupcakes chegam quentinhos</li>";
echo "</ul>";
echo "</div>";

echo "<div style='text-align: center; margin-top: 2rem;'>";
echo "<a href='../index.php' class='btn btn-primary' style='margin-right: 1rem;'>🏠 Voltar ao Site</a>";
echo "<a href='test_delivery_time.php' class='btn btn-secondary'>🔄 Recarregar Teste</a>";
echo "</div>";

echo "<p style='color: green; font-weight: bold; text-align: center; margin-top: 2rem;'>✅ Tempo de Entrega Atualizado com Sucesso!</p>";
?>

<style>
:root {
    --primary-green: #6b8e6b;
    --light-beige: #f5f3f0;
    --white: #ffffff;
    --beige: #d4c5b9;
    --text-light: #666;
    --success: #28a745;
}

.btn {
    display: inline-block;
    padding: 0.75rem 1.5rem;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 600;
    text-align: center;
    transition: all 0.3s ease;
    border: none;
}

.btn-primary {
    background: var(--primary-green);
    color: white;
}

.btn-primary:hover {
    background: #5a7a5a;
    transform: translateY(-2px);
}

.btn-secondary {
    background: var(--beige);
    color: #333;
}

.btn-secondary:hover {
    background: #c4b5a9;
}

.btn-success {
    background: var(--success);
    color: white;
}

.btn-success:hover {
    background: #218838;
}

body {
    font-family: 'Poppins', sans-serif;
    max-width: 800px;
    margin: 0 auto;
    padding: 2rem;
    background: #f8f9fa;
}

h2, h3, h4 {
    color: var(--primary-green);
}
</style>
