<?php
include_once '../config/database.php';
header('Content-Type: text/html; charset=utf-8');

echo "<h2>Diagnóstico do Sistema de Cadastro</h2>";

try {
    echo "<h3>1. Verificação da Conexão com o Banco</h3>";
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8",
        DB_USER,
        DB_PASS,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    echo "<p style='color: green;'>✓ Conexão com banco de dados estabelecida</p>";
    
    echo "<h3>2. Verificação da Tabela 'users'</h3>";
    $stmt = $pdo->query("DESCRIBE users");
    $columns = $stmt->fetchAll();
    echo "<p style='color: green;'>✓ Tabela 'users' encontrada com " . count($columns) . " colunas:</p>";
    echo "<ul>";
    foreach ($columns as $column) {
        echo "<li>{$column['Field']} - {$column['Type']}</li>";
    }
    echo "</ul>";
    
    echo "<h3>3. Verificação dos Usuários Existentes</h3>";
    $users = fetchData("SELECT id, name, email, username, user_type, created_at FROM users ORDER BY created_at DESC LIMIT 10");
    echo "<p style='color: green;'>✓ Encontrados " . count($users) . " usuários no banco:</p>";
    if (count($users) > 0) {
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr><th>ID</th><th>Nome</th><th>Email</th><th>Username</th><th>Tipo</th><th>Criado em</th></tr>";
        foreach ($users as $user) {
            echo "<tr>";
            echo "<td>{$user['id']}</td>";
            echo "<td>{$user['name']}</td>";
            echo "<td>{$user['email']}</td>";
            echo "<td>{$user['username']}</td>";
            echo "<td>{$user['user_type']}</td>";
            echo "<td>{$user['created_at']}</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p style='color: orange;'>⚠ Nenhum usuário encontrado no banco</p>";
    }
    
    echo "<h3>4. Teste de Inserção (simulação)</h3>";
    echo "<p><strong>Campos necessários para cadastro:</strong></p>";
    echo "<ul>";
    echo "<li>name (obrigatório)</li>";
    echo "<li>email (obrigatório, único)</li>";
    echo "<li>username (obrigatório, único)</li>";
    echo "<li>password (obrigatório, hash)</li>";
    echo "<li>cpf (obrigatório, único, formatado)</li>";
    echo "<li>phone (obrigatório, formatado)</li>";
    echo "<li>address (obrigatório)</li>";
    echo "<li>city (obrigatório)</li>";
    echo "<li>state (obrigatório)</li>";
    echo "<li>zip_code (obrigatório, formatado)</li>";
    echo "<li>user_type ('customer')</li>";
    echo "</ul>";
    
    echo "<h3>5. Teste das Funções Helper</h3>";
    
    // Testar executeQuery
    echo "<p><strong>Testando executeQuery:</strong></p>";
    try {
        executeQuery("SELECT 1 as test");
        echo "<p style='color: green;'>✓ Função executeQuery funcionando</p>";
    } catch (Exception $e) {
        echo "<p style='color: red;'>✗ Erro na função executeQuery: " . $e->getMessage() . "</p>";
    }
    
    // Testar fetchOne
    echo "<p><strong>Testando fetchOne:</strong></p>";
    try {
        $result = fetchOne("SELECT COUNT(*) as total FROM users");
        echo "<p style='color: green;'>✓ Função fetchOne funcionando - Total de usuários: {$result['total']}</p>";
    } catch (Exception $e) {
        echo "<p style='color: red;'>✗ Erro na função fetchOne: " . $e->getMessage() . "</p>";
    }
    
    echo "<h3>6. Verificação dos Logs de Erro</h3>";
    $errorLog = error_get_last();
    if ($errorLog) {
        echo "<p style='color: orange;'>⚠ Último erro PHP:</p>";
        echo "<pre>" . print_r($errorLog, true) . "</pre>";
    } else {
        echo "<p style='color: green;'>✓ Nenhum erro PHP recente</p>";
    }
    
    echo "<h3>7. Links Úteis</h3>";
    echo "<p><a href='../pages/register.php'>Ir para página de Cadastro</a></p>";
    echo "<p><a href='../pages/login.php'>Ir para página de Login</a></p>";
    echo "<p><a href='../index.php'>Voltar ao início</a></p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Erro no diagnóstico: " . $e->getMessage() . "</p>";
    echo "<p><strong>Detalhes do erro:</strong></p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
?>
