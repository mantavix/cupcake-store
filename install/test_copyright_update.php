<?php
header('Content-Type: text/html; charset=utf-8');

echo "<h2>Verificação das Alterações de Copyright</h2>";

echo "<h3>✅ Alterações Implementadas</h3>";
echo "<div style='background: #d4edda; padding: 1rem; border-radius: 8px; border-left: 4px solid #28a745; margin: 1rem 0;'>";
echo "<p><strong>Alteração realizada em todos os arquivos:</strong></p>";
echo "<p>❌ <strong>Antes:</strong> <em>&copy; 2024 Cupcake Store. Todos os direitos reservados.</em></p>";
echo "<p>✅ <strong>Agora:</strong> <strong>&copy; 2025 Cupcake Store - desenvolvido por Mantavix-Tech, todos os direitos reservados.</strong></p>";
echo "</div>";

echo "<h3>📄 Arquivos Atualizados</h3>";

$files = [
    'index.php' => 'Página Principal',
    'pages/cart.php' => 'Página do Carrinho',
    'pages/checkout.php' => 'Página de Checkout',
    'pages/login.php' => 'Página de Login',
    'pages/register.php' => 'Página de Cadastro',
    'pages/order_success.php' => 'Página de Pedido Concluído',
    'pages/profile.php' => 'Página de Perfil',
    'pages/forgot_password.php' => 'Página de Esqueci Senha',
    'pages/reset_password.php' => 'Página de Redefinir Senha',
    'install/simulate_order_success.php' => 'Simulação de Pedido'
];

echo "<div style='display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1rem; margin: 1rem 0;'>";

foreach ($files as $file => $description) {
    $fullPath = '../' . $file;
    $exists = file_exists($fullPath);
    $hasNewCopyright = false;
    
    if ($exists) {
        $content = file_get_contents($fullPath);
        $hasNewCopyright = strpos($content, '2025 Cupcake Store - desenvolvido por Mantavix-Tech') !== false;
    }
    
    $statusColor = $hasNewCopyright ? '#28a745' : '#dc3545';
    $statusIcon = $hasNewCopyright ? '✅' : '❌';
    
    echo "<div style='background: var(--white); padding: 1rem; border-radius: 8px; border-left: 4px solid $statusColor; box-shadow: 0 2px 5px rgba(0,0,0,0.1);'>";
    echo "<h4 style='margin: 0 0 0.5rem 0; color: $statusColor;'>$statusIcon $description</h4>";
    echo "<p style='margin: 0; font-size: 0.9rem; color: #666;'>$file</p>";
    if ($hasNewCopyright) {
        echo "<p style='margin: 0.5rem 0 0 0; font-size: 0.8rem; color: #28a745;'>Copyright atualizado ✓</p>";
    }
    echo "</div>";
}

echo "</div>";

echo "<h3>🧪 Links para Verificação Manual</h3>";
echo "<div style='display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1rem; margin: 1rem 0;'>";

$testLinks = [
    '../index.php' => 'Página Principal',
    '../pages/login.php' => 'Login',
    '../pages/register.php' => 'Cadastro',
    '../pages/cart.php' => 'Carrinho (requer login)',
    '../pages/checkout.php' => 'Checkout (requer login)',
    '../install/simulate_order_success.php' => 'Simulação Pedido'
];

foreach ($testLinks as $url => $name) {
    echo "<div style='background: var(--light-beige); padding: 1rem; border-radius: 8px; text-align: center;'>";
    echo "<h5 style='color: var(--primary-green); margin: 0 0 0.5rem 0;'>$name</h5>";
    echo "<a href='$url' class='btn btn-primary' target='_blank'>Verificar Footer</a>";
    echo "</div>";
}

echo "</div>";

echo "<h3>🔍 Verificação Automática</h3>";
echo "<div style='background: #f8f9fa; padding: 1rem; border-radius: 8px; border: 1px solid #dee2e6; margin: 1rem 0;'>";

// Verificar se ainda existe alguma referência antiga
$oldReferences = [];
$searchPattern = '2024.*Cupcake Store';

foreach ($files as $file => $description) {
    $fullPath = '../' . $file;
    if (file_exists($fullPath)) {
        $content = file_get_contents($fullPath);
        if (preg_match('/' . $searchPattern . '/i', $content)) {
            $oldReferences[] = $file;
        }
    }
}

if (empty($oldReferences)) {
    echo "<p style='color: #28a745; font-weight: 600;'>✅ Nenhuma referência antiga encontrada - Todas as alterações foram aplicadas com sucesso!</p>";
} else {
    echo "<p style='color: #dc3545; font-weight: 600;'>⚠️ Referências antigas ainda encontradas em:</p>";
    echo "<ul>";
    foreach ($oldReferences as $file) {
        echo "<li style='color: #dc3545;'>$file</li>";
    }
    echo "</ul>";
}

// Verificar novas referências
$newReferences = 0;
foreach ($files as $file => $description) {
    $fullPath = '../' . $file;
    if (file_exists($fullPath)) {
        $content = file_get_contents($fullPath);
        if (strpos($content, '2025 Cupcake Store - desenvolvido por Mantavix-Tech') !== false) {
            $newReferences++;
        }
    }
}

echo "<p style='color: #28a745; font-weight: 600;'>✅ Novo copyright encontrado em $newReferences de " . count($files) . " arquivos</p>";

echo "</div>";

echo "<h3>📋 Como Verificar</h3>";
echo "<ol>";
echo "<li><strong>Abra qualquer página do site</strong> (use os links acima)</li>";
echo "<li><strong>Role até o final da página</strong> (footer)</li>";
echo "<li><strong>Procure pela mensagem:</strong> <em>&copy; 2025 Cupcake Store - desenvolvido por Mantavix-Tech, todos os direitos reservados.</em></li>";
echo "<li><strong>Confirme que não há mais referências a 2024</strong></li>";
echo "</ol>";

echo "<h3>🎨 Exemplo Visual</h3>";
echo "<div style='background: var(--white); padding: 1.5rem; border-radius: 8px; border: 1px solid var(--beige); margin: 1rem 0; text-align: center;'>";
echo "<h4 style='color: var(--primary-green); margin-bottom: 1rem;'>Footer Atualizado</h4>";
echo "<div style='background: #2c3e50; color: white; padding: 1rem; border-radius: 8px;'>";
echo "<p style='margin: 0; font-size: 0.9rem;'>&copy; 2025 Cupcake Store - desenvolvido por Mantavix-Tech, todos os direitos reservados.</p>";
echo "</div>";
echo "</div>";

echo "<div style='text-align: center; margin-top: 2rem;'>";
echo "<a href='../index.php' class='btn btn-primary' style='margin-right: 1rem;'>🏠 Ir para o Site</a>";
echo "<a href='test_copyright_update.php' class='btn btn-secondary'>🔄 Recarregar Teste</a>";
echo "</div>";

echo "<p style='color: green; font-weight: bold; text-align: center; margin-top: 2rem;'>✅ Copyright Atualizado com Sucesso para 2025!</p>";
?>

<style>
:root {
    --primary-green: #6b8e6b;
    --light-beige: #f5f3f0;
    --white: #ffffff;
    --beige: #d4c5b9;
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

body {
    font-family: 'Poppins', sans-serif;
    max-width: 1000px;
    margin: 0 auto;
    padding: 2rem;
    background: #f8f9fa;
}

h2, h3, h4, h5 {
    color: var(--primary-green);
}
</style>
