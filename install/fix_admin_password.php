<?php
// Script para corrigir a senha do administrador

// Configurações do banco
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'cupcake_store';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$database", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<h2>Corrigindo senha do administrador</h2>";
    
    // Gerar hash correto para a senha "admin123"
    $newPassword = 'admin123';
    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
    
    echo "<p>✓ Nova senha hash gerada para: <strong>$newPassword</strong></p>";
    echo "<p>Hash: <code>$hashedPassword</code></p>";
    
    // Atualizar a senha do admin
    $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE username = 'admin'");
    $stmt->execute([$hashedPassword]);
    
    echo "<p>✓ Senha do admin atualizada no banco de dados</p>";
    
    // Verificar se o usuário admin existe
    $admin = $pdo->query("SELECT id, username, email, user_type FROM users WHERE username = 'admin'")->fetch();
    
    if ($admin) {
        echo "<h3 style='color: green;'>✓ Usuário admin encontrado:</h3>";
        echo "<ul>";
        echo "<li><strong>ID:</strong> {$admin['id']}</li>";
        echo "<li><strong>Username:</strong> {$admin['username']}</li>";
        echo "<li><strong>Email:</strong> {$admin['email']}</li>";
        echo "<li><strong>Tipo:</strong> {$admin['user_type']}</li>";
        echo "</ul>";
        
        // Testar a nova senha
        $testPassword = password_verify($newPassword, $hashedPassword);
        echo "<p>✓ <strong>Teste de senha:</strong> " . ($testPassword ? "PASSOU" : "FALHOU") . "</p>";
        
    } else {
        echo "<h3 style='color: red;'>❌ Usuário admin NÃO encontrado!</h3>";
        echo "<p>Criando usuário admin...</p>";
        
        // Criar usuário admin se não existir
        $stmt = $pdo->prepare("INSERT INTO users (name, email, username, password, cpf, phone, address, city, state, zip_code, user_type) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            'Administrador',
            'admin@cupcakestore.com',
            'admin',
            $hashedPassword,
            '000.000.000-00',
            '(11) 99999-9999',
            'Rua Principal, 123',
            'São Paulo',
            'SP',
            '01000-000',
            'admin'
        ]);
        
        echo "<p>✓ Usuário admin criado com sucesso!</p>";
    }
    
    echo "<h3 style='color: green;'>✓ Correção concluída!</h3>";
    echo "<h4>Dados para login:</h4>";
    echo "<ul>";
    echo "<li><strong>Usuário:</strong> admin</li>";
    echo "<li><strong>Senha:</strong> admin123</li>";
    echo "<li><strong>URL Admin:</strong> <a href='../admin/dashboard.php'>Painel Admin</a></li>";
    echo "<li><strong>URL Login:</strong> <a href='../pages/login.php'>Fazer Login</a></li>";
    echo "</ul>";
    
} catch (PDOException $e) {
    echo "<h3 style='color: red;'>Erro:</h3>";
    echo "<p>" . $e->getMessage() . "</p>";
    echo "<h4>Possíveis soluções:</h4>";
    echo "<ul>";
    echo "<li>Verifique se o MySQL está rodando</li>";
    echo "<li>Execute primeiro o <a href='setup_database.php'>instalador principal</a></li>";
    echo "<li>Confirme as credenciais do banco</li>";
    echo "</ul>";
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Correção de Login - Cupcake Store</title>
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
        p {
            background: white;
            padding: 10px;
            border-radius: 5px;
            margin: 5px 0;
        }
        code {
            background: #f0f0f0;
            padding: 2px 5px;
            border-radius: 3px;
            font-family: monospace;
            word-break: break-all;
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
