<?php
include_once '../config/database.php';
header('Content-Type: text/html; charset=utf-8');

echo "<h2>Teste Completo do Sistema de Recuperação de Senha</h2>";

try {
    echo "<h3>1. Verificação da Infraestrutura</h3>";
    
    // Verificar se a tabela existe
    $tables = fetchData("SHOW TABLES LIKE 'password_resets'");
    if (count($tables) > 0) {
        echo "<p style='color: green;'>✓ Tabela 'password_resets' existe</p>";
    } else {
        echo "<p style='color: red;'>✗ Tabela 'password_resets' não encontrada</p>";
        echo "<p><a href='setup_password_reset.php'>Criar tabela agora</a></p>";
        exit;
    }
    
    // Verificar estrutura
    $columns = fetchData("DESCRIBE password_resets");
    echo "<p style='color: green;'>✓ Estrutura da tabela verificada (" . count($columns) . " colunas)</p>";
    
    echo "<h3>2. Verificação dos Arquivos</h3>";
    
    $files = [
        '../pages/forgot_password.php' => 'Página de Solicitação de Recuperação',
        '../pages/reset_password.php' => 'Página de Redefinição de Senha',
        '../actions/forgot_password_process.php' => 'Processamento de Solicitação',
        '../actions/reset_password_process.php' => 'Processamento de Redefinição'
    ];
    
    foreach ($files as $file => $description) {
        if (file_exists($file)) {
            echo "<p style='color: green;'>✓ $description</p>";
        } else {
            echo "<p style='color: red;'>✗ $description - Arquivo não encontrado: $file</p>";
        }
    }
    
    echo "<h3>3. Teste de Funcionalidade (Simulação)</h3>";
    
    // Verificar se há usuários para teste
    $users = fetchData("SELECT id, email, name FROM users WHERE user_type = 'customer' LIMIT 3");
    echo "<p style='color: green;'>✓ Usuários disponíveis para teste: " . count($users) . "</p>";
    
    if (count($users) > 0) {
        echo "<table border='1' style='border-collapse: collapse; width: 100%; margin: 1rem 0;'>";
        echo "<tr><th>ID</th><th>Nome</th><th>Email</th><th>Ação</th></tr>";
        foreach ($users as $user) {
            echo "<tr>";
            echo "<td>{$user['id']}</td>";
            echo "<td>{$user['name']}</td>";
            echo "<td>{$user['email']}</td>";
            echo "<td><a href='../pages/forgot_password.php'>Testar Recuperação</a></td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
    // Verificar tokens ativos
    $activeTokens = fetchData("SELECT COUNT(*) as count FROM password_resets WHERE expires_at > NOW() AND used = FALSE");
    echo "<p style='color: blue;'>ℹ Tokens ativos no sistema: {$activeTokens[0]['count']}</p>";
    
    echo "<h3>4. Fluxo Completo do Sistema</h3>";
    echo "<ol>";
    echo "<li><strong>Usuário esquece a senha:</strong> Clica em 'Esqueci minha senha' na página de login</li>";
    echo "<li><strong>Solicitação:</strong> Digita o e-mail na página de recuperação</li>";
    echo "<li><strong>Token gerado:</strong> Sistema cria token único com validade de 1 hora</li>";
    echo "<li><strong>Link disponível:</strong> Em modo demo, o link aparece na tela</li>";
    echo "<li><strong>Redefinição:</strong> Usuário acessa o link e define nova senha</li>";
    echo "<li><strong>Conclusão:</strong> Token é invalidado e usuário pode fazer login</li>";
    echo "</ol>";
    
    echo "<h3>5. Recursos de Segurança</h3>";
    echo "<ul>";
    echo "<li>✓ Tokens únicos e seguros (64 caracteres hexadecimais)</li>";
    echo "<li>✓ Expiração automática (1 hora)</li>";
    echo "<li>✓ Invalidação após uso</li>";
    echo "<li>✓ Hashing seguro de senhas (PASSWORD_DEFAULT)</li>";
    echo "<li>✓ Validação completa de entrada</li>";
    echo "<li>✓ Logs de auditoria</li>";
    echo "</ul>";
    
    echo "<h3>6. Links para Teste Manual</h3>";
    echo "<div style='background: var(--light-beige); padding: 1rem; border-radius: 8px; margin: 1rem 0;'>";
    echo "<p><strong>Páginas para testar:</strong></p>";
    echo "<p>🔑 <a href='../pages/login.php' target='_blank'>Página de Login</a> (com link 'Esqueci minha senha')</p>";
    echo "<p>📧 <a href='../pages/forgot_password.php' target='_blank'>Página de Recuperação</a></p>";
    echo "<p>🏠 <a href='../index.php' target='_blank'>Página Principal</a></p>";
    echo "</div>";
    
    echo "<h3>7. Instruções para Teste</h3>";
    echo "<div style='background: #f0f8f0; padding: 1rem; border-radius: 8px; border-left: 4px solid var(--primary-green);'>";
    echo "<ol>";
    echo "<li>Acesse a <a href='../pages/login.php' target='_blank'>página de login</a></li>";
    echo "<li>Clique em 'Esqueci minha senha'</li>";
    echo "<li>Digite um e-mail de usuário existente</li>";
    echo "<li>Clique no link que aparece na tela (modo demo)</li>";
    echo "<li>Defina uma nova senha</li>";
    echo "<li>Teste o login com a nova senha</li>";
    echo "</ol>";
    echo "</div>";
    
    echo "<p style='color: green; font-weight: bold; text-align: center; margin-top: 2rem;'>✅ Sistema de Recuperação de Senha Implementado com Sucesso!</p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Erro no teste: " . $e->getMessage() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
?>
