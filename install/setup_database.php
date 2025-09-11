<?php
// Script para configurar o banco de dados

// Configurações do banco
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'cupcake_store';

try {
    // Conectar ao MySQL (sem especificar database)
    $pdo = new PDO("mysql:host=$host", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<h2>Configurando banco de dados Cupcake Store</h2>";
    
    // Ler e executar o script SQL
    $sql = file_get_contents('../database/create_database.sql');
    
    // Dividir o SQL em comandos individuais
    $commands = array_filter(array_map('trim', explode(';', $sql)));
    
    foreach ($commands as $command) {
        if (!empty($command)) {
            $pdo->exec($command);
            echo "<p>✓ Comando executado: " . substr($command, 0, 50) . "...</p>";
        }
    }
    
    echo "<h3 style='color: green;'>✓ Banco de dados configurado com sucesso!</h3>";
    echo "<h4>Dados de acesso:</h4>";
    echo "<ul>";
    echo "<li><strong>Admin:</strong> usuário 'admin', senha 'admin123'</li>";
    echo "<li><strong>Banco:</strong> $database criado em $host</li>";
    echo "<li><strong>Produtos:</strong> 11 cupcakes com imagens reais adicionados</li>";
    echo "<li><strong>Imagens:</strong> Logo personalizada e fotos dos cupcakes integradas</li>";
    echo "</ul>";
    
    echo "<p><a href='../index.php' style='background: #6B8E6B; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Ir para a Loja</a></p>";
    echo "<p><a href='../admin/dashboard.php' style='background: #888888; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Painel Admin</a></p>";
    
} catch (PDOException $e) {
    echo "<h3 style='color: red;'>Erro ao configurar banco de dados:</h3>";
    echo "<p>" . $e->getMessage() . "</p>";
    echo "<h4>Verifique:</h4>";
    echo "<ul>";
    echo "<li>Se o MySQL está rodando</li>";
    echo "<li>Se as credenciais estão corretas</li>";
    echo "<li>Se o usuário tem permissões para criar databases</li>";
    echo "</ul>";
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Setup - Cupcake Store</title>
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
    </style>
</head>
<body>
    <!-- O conteúdo PHP será exibido aqui -->
</body>
</html>
