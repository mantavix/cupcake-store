<?php
include_once '../config/database.php';
header('Content-Type: text/html; charset=utf-8');

echo "<h2>Configuração do Sistema de Recuperação de Senha</h2>";

try {
    // Conectar ao banco
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8",
        DB_USER,
        DB_PASS,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    echo "<p style='color: green;'>✓ Conexão com banco de dados estabelecida</p>";
    
    // Criar tabela password_resets
    echo "<h3>1. Criando tabela password_resets</h3>";
    $sql = "
    CREATE TABLE IF NOT EXISTS password_resets (
        id INT PRIMARY KEY AUTO_INCREMENT,
        user_id INT NOT NULL,
        token VARCHAR(255) NOT NULL UNIQUE,
        expires_at DATETIME NOT NULL,
        used BOOLEAN DEFAULT FALSE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        INDEX idx_token (token),
        INDEX idx_expires (expires_at)
    )";
    
    $pdo->exec($sql);
    echo "<p style='color: green;'>✓ Tabela 'password_resets' criada com sucesso</p>";
    
    // Verificar estrutura da tabela
    echo "<h3>2. Verificando estrutura da tabela</h3>";
    $stmt = $pdo->query("DESCRIBE password_resets");
    $columns = $stmt->fetchAll();
    echo "<p style='color: green;'>✓ Tabela criada com " . count($columns) . " colunas:</p>";
    echo "<ul>";
    foreach ($columns as $column) {
        echo "<li>{$column['Field']} - {$column['Type']}</li>";
    }
    echo "</ul>";
    
    // Verificar se há tokens existentes
    echo "<h3>3. Verificando tokens existentes</h3>";
    $tokenCount = fetchOne("SELECT COUNT(*) as count FROM password_resets");
    echo "<p style='color: green;'>✓ Tokens de reset existentes: {$tokenCount['count']}</p>";
    
    // Limpar tokens expirados
    echo "<h3>4. Limpando tokens expirados</h3>";
    executeQuery("DELETE FROM password_resets WHERE expires_at < NOW()");
    $cleanedCount = fetchOne("SELECT COUNT(*) as count FROM password_resets WHERE expires_at < NOW()");
    echo "<p style='color: green;'>✓ Tokens expirados removidos</p>";
    
    echo "<h3>5. Sistema configurado com sucesso!</h3>";
    echo "<p><strong>Funcionalidades disponíveis:</strong></p>";
    echo "<ul>";
    echo "<li>✓ Solicitação de recuperação de senha</li>";
    echo "<li>✓ Geração de tokens seguros</li>";
    echo "<li>✓ Validação de expiração (1 hora)</li>";
    echo "<li>✓ Redefinição de senha</li>";
    echo "<li>✓ Invalidação automática de tokens</li>";
    echo "</ul>";
    
    echo "<h3>6. Links para teste</h3>";
    echo "<p><a href='../pages/login.php'>Página de Login (com link 'Esqueci minha senha')</a></p>";
    echo "<p><a href='../pages/forgot_password.php'>Página de Recuperação de Senha</a></p>";
    echo "<p><a href='../index.php'>Voltar ao início</a></p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Erro na configuração: " . $e->getMessage() . "</p>";
    echo "<p><strong>Detalhes do erro:</strong></p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
?>
