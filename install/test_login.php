<?php
// Script para testar o sistema de login

include_once '../config/database.php';

echo "<h2>Diagnóstico do Sistema de Login</h2>";

try {
    echo "<h3>✓ Conexão com Banco de Dados</h3>";
    echo "<p>Banco: cupcake_store conectado com sucesso</p>";
    
    // Testar se a tabela users existe
    $tables = $pdo->query("SHOW TABLES LIKE 'users'")->fetchAll();
    if (empty($tables)) {
        echo "<p style='color: red;'>❌ Tabela 'users' não encontrada!</p>";
        echo "<p><a href='setup_database.php'>Clique aqui para criar o banco</a></p>";
        exit;
    }
    echo "<p>✓ Tabela 'users' encontrada</p>";
    
    // Verificar usuário admin
    echo "<h3>Verificação do Usuário Admin</h3>";
    $admin = fetchOne("SELECT * FROM users WHERE username = 'admin'");
    
    if (!$admin) {
        echo "<p style='color: red;'>❌ Usuário admin não encontrado!</p>";
        echo "<p><a href='fix_admin_password.php'>Clique aqui para criar/corrigir admin</a></p>";
        exit;
    }
    
    echo "<p>✓ Usuário admin encontrado</p>";
    echo "<ul>";
    echo "<li><strong>ID:</strong> {$admin['id']}</li>";
    echo "<li><strong>Nome:</strong> {$admin['name']}</li>";
    echo "<li><strong>Username:</strong> {$admin['username']}</li>";
    echo "<li><strong>Email:</strong> {$admin['email']}</li>";
    echo "<li><strong>Tipo:</strong> {$admin['user_type']}</li>";
    echo "</ul>";
    
    // Testar senha
    echo "<h3>Teste de Senha</h3>";
    $testPassword = 'admin123';
    $passwordValid = password_verify($testPassword, $admin['password']);
    
    if ($passwordValid) {
        echo "<p style='color: green;'>✓ Senha 'admin123' é válida</p>";
    } else {
        echo "<p style='color: red;'>❌ Senha 'admin123' é inválida</p>";
        echo "<p><a href='fix_admin_password.php'>Clique aqui para corrigir a senha</a></p>";
    }
    
    // Testar função de login simulada
    echo "<h3>Simulação de Login</h3>";
    
    $username = 'admin';
    $password = 'admin123';
    
    // Buscar usuário
    $user = fetchOne("SELECT * FROM users WHERE username = ? OR email = ?", [$username, $username]);
    
    if (!$user) {
        echo "<p style='color: red;'>❌ Usuário não encontrado na simulação</p>";
    } else {
        echo "<p>✓ Usuário encontrado na simulação</p>";
        
        // Verificar senha
        if (password_verify($password, $user['password'])) {
            echo "<p style='color: green;'>✓ Login simulado: SUCESSO!</p>";
            echo "<p><strong>Resultado:</strong> Login deveria funcionar normalmente</p>";
        } else {
            echo "<p style='color: red;'>❌ Login simulado: FALHOU!</p>";
            echo "<p><strong>Problema:</strong> Senha não confere</p>";
        }
    }
    
    // Verificar sessões
    echo "<h3>Verificação de Sessões</h3>";
    if (session_status() === PHP_SESSION_ACTIVE) {
        echo "<p>✓ Sessões PHP funcionando</p>";
    } else {
        session_start();
        echo "<p>✓ Sessão iniciada para teste</p>";
    }
    
    // Contador de usuários
    echo "<h3>Estatísticas do Banco</h3>";
    $totalUsers = fetchOne("SELECT COUNT(*) as total FROM users")['total'];
    $totalAdmins = fetchOne("SELECT COUNT(*) as total FROM users WHERE user_type = 'admin'")['total'];
    $totalCustomers = fetchOne("SELECT COUNT(*) as total FROM users WHERE user_type = 'customer'")['total'];
    
    echo "<ul>";
    echo "<li><strong>Total de usuários:</strong> $totalUsers</li>";
    echo "<li><strong>Administradores:</strong> $totalAdmins</li>";
    echo "<li><strong>Clientes:</strong> $totalCustomers</li>";
    echo "</ul>";
    
    echo "<h3 style='color: green;'>Resultado do Diagnóstico</h3>";
    
    if ($passwordValid && $user) {
        echo "<p style='background: #d4edda; padding: 15px; border-radius: 5px; color: #155724;'>";
        echo "<strong>✓ TUDO OK!</strong><br>";
        echo "O sistema de login deveria estar funcionando corretamente.<br>";
        echo "Use: <strong>admin</strong> / <strong>admin123</strong>";
        echo "</p>";
        
        echo "<h4>Testar agora:</h4>";
        echo "<p><a href='../pages/login.php' style='background: #6B8E6B; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Fazer Login</a></p>";
        
    } else {
        echo "<p style='background: #f8d7da; padding: 15px; border-radius: 5px; color: #721c24;'>";
        echo "<strong>❌ PROBLEMAS ENCONTRADOS!</strong><br>";
        echo "Execute as correções sugeridas acima.";
        echo "</p>";
        
        echo "<h4>Corrigir problemas:</h4>";
        echo "<p><a href='fix_admin_password.php' style='background: #dc3545; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Corrigir Senha Admin</a></p>";
    }
    
} catch (Exception $e) {
    echo "<h3 style='color: red;'>❌ Erro no Diagnóstico</h3>";
    echo "<p>Erro: " . $e->getMessage() . "</p>";
    echo "<p><a href='setup_database.php'>Executar instalador completo</a></p>";
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teste de Login - Cupcake Store</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background-color: #f5f5f5;
        }
        h2 {
            color: #6B8E6B;
            text-align: center;
        }
        h3 {
            color: #6B8E6B;
            border-bottom: 2px solid #6B8E6B;
            padding-bottom: 5px;
        }
        p {
            background: white;
            padding: 10px;
            border-radius: 5px;
            margin: 5px 0;
        }
        ul {
            background: white;
            padding: 15px;
            border-radius: 5px;
            margin: 5px 0;
        }
        a {
            color: #6B8E6B;
            text-decoration: none;
            font-weight: bold;
        }
        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <!-- O conteúdo PHP será exibido aqui -->
</body>
</html>
