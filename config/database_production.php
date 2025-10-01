<?php
// Configurações do banco de dados para produção
// Use variáveis de ambiente para maior segurança

// Detectar se está em ambiente de produção
$isProduction = !empty($_SERVER['HTTP_HOST']) && $_SERVER['HTTP_HOST'] !== 'localhost';

if ($isProduction) {
    // Configurações para produção (usando variáveis de ambiente)
    define('DB_HOST', $_ENV['DB_HOST'] ?? getenv('DB_HOST') ?? 'localhost');
    define('DB_NAME', $_ENV['DB_NAME'] ?? getenv('DB_NAME') ?? 'cupcake_store');
    define('DB_USER', $_ENV['DB_USER'] ?? getenv('DB_USER') ?? 'root');
    define('DB_PASS', $_ENV['DB_PASS'] ?? getenv('DB_PASS') ?? '');
} else {
    // Configurações para desenvolvimento local
    define('DB_HOST', 'localhost');
    define('DB_NAME', 'cupcake_store');
    define('DB_USER', 'root');
    define('DB_PASS', '');
}

try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4", DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
} catch(PDOException $e) {
    if ($isProduction) {
        // Em produção, não mostrar detalhes do erro
        die("Erro na conexão com o banco de dados. Tente novamente mais tarde.");
    } else {
        // Em desenvolvimento, mostrar erro detalhado
        die("Erro na conexão: " . $e->getMessage());
    }
}

// Função para executar queries
function executeQuery($query, $params = []) {
    global $pdo;
    try {
        $stmt = $pdo->prepare($query);
        $stmt->execute($params);
        return $stmt;
    } catch(PDOException $e) {
        throw new Exception("Erro na query: " . $e->getMessage());
    }
}

// Função para buscar dados
function fetchData($query, $params = []) {
    $stmt = executeQuery($query, $params);
    return $stmt->fetchAll();
}

// Função para buscar um único registro
function fetchOne($query, $params = []) {
    $stmt = executeQuery($query, $params);
    return $stmt->fetch();
}

// Função para inserir dados e retornar o ID
function insertData($query, $params = []) {
    global $pdo;
    $stmt = executeQuery($query, $params);
    return $pdo->lastInsertId();
}
?>