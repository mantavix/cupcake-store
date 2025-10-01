<?php
/**
 * Script de configuração para produção
 * Execute este arquivo após fazer deploy para configurar automaticamente
 */

// Verificar se está em produção
$isProduction = !empty($_SERVER['HTTP_HOST']) && $_SERVER['HTTP_HOST'] !== 'localhost' && $_SERVER['HTTP_HOST'] !== '127.0.0.1';

if (!$isProduction) {
    die("Este script deve ser executado apenas em produção!");
}

echo "<h1>🚀 Configuração de Produção - Cupcake Store</h1>";

// Verificar PHP
echo "<h2>✅ Verificando PHP</h2>";
echo "Versão PHP: " . PHP_VERSION . "<br>";
if (version_compare(PHP_VERSION, '7.4.0') >= 0) {
    echo "✅ Versão PHP compatível<br>";
} else {
    echo "❌ PHP 7.4+ necessário<br>";
}

// Verificar extensões
echo "<h2>✅ Verificando Extensões</h2>";
$required_extensions = ['pdo', 'pdo_mysql', 'json', 'session'];
foreach ($required_extensions as $ext) {
    if (extension_loaded($ext)) {
        echo "✅ $ext: Carregada<br>";
    } else {
        echo "❌ $ext: NÃO carregada<br>";
    }
}

// Verificar arquivos
echo "<h2>✅ Verificando Arquivos</h2>";
$required_files = [
    'config/database.php',
    'config/env_loader.php',
    '.htaccess',
    'index.php'
];

foreach ($required_files as $file) {
    if (file_exists("../$file")) {
        echo "✅ $file: Existe<br>";
    } else {
        echo "❌ $file: NÃO existe<br>";
    }
}

// Verificar permissões
echo "<h2>✅ Verificando Permissões</h2>";
$writable_dirs = [
    '../assets/img',
    '../cache',
    '../logs'
];

foreach ($writable_dirs as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
    
    if (is_writable($dir)) {
        echo "✅ $dir: Gravável<br>";
    } else {
        echo "❌ $dir: NÃO gravável<br>";
    }
}

// Testar conexão com banco
echo "<h2>🗄️ Testando Conexão com Banco</h2>";
try {
    require_once '../config/database.php';
    echo "✅ Conexão com banco estabelecida<br>";
    
    // Verificar se tabelas existem
    $tables = ['users', 'products', 'cart', 'orders'];
    foreach ($tables as $table) {
        $stmt = $pdo->query("SHOW TABLES LIKE '$table'");
        if ($stmt->rowCount() > 0) {
            echo "✅ Tabela $table: Existe<br>";
        } else {
            echo "❌ Tabela $table: NÃO existe<br>";
        }
    }
    
} catch (Exception $e) {
    echo "❌ Erro na conexão: " . $e->getMessage() . "<br>";
    echo "<p><strong>Verifique suas configurações de banco de dados!</strong></p>";
}

// Verificar variáveis de ambiente
echo "<h2>🔧 Verificando Variáveis de Ambiente</h2>";
$env_vars = ['DB_HOST', 'DB_NAME', 'DB_USER', 'DB_PASS'];
foreach ($env_vars as $var) {
    $value = $_ENV[$var] ?? getenv($var);
    if ($value) {
        echo "✅ $var: Configurada<br>";
    } else {
        echo "❌ $var: NÃO configurada<br>";
    }
}

// Configurações de segurança
echo "<h2>🛡️ Verificando Segurança</h2>";

// Verificar se display_errors está off
if (ini_get('display_errors')) {
    echo "⚠️ display_errors está ON - Recomendado OFF em produção<br>";
} else {
    echo "✅ display_errors está OFF<br>";
}

// Verificar se expose_php está off
if (ini_get('expose_php')) {
    echo "⚠️ expose_php está ON - Recomendado OFF em produção<br>";
} else {
    echo "✅ expose_php está OFF<br>";
}

// Verificar HTTPS
if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
    echo "✅ HTTPS habilitado<br>";
} else {
    echo "⚠️ HTTPS não detectado - Recomendado para produção<br>";
}

echo "<h2>🎉 Configuração Concluída!</h2>";
echo "<p>Se todos os itens estão ✅, sua aplicação está pronta para uso!</p>";
echo "<p><a href='../index.php'>🏠 Ir para a página inicial</a></p>";
echo "<p><a href='../admin/dashboard.php'>⚙️ Acessar painel administrativo</a></p>";

// Log da configuração
$log_message = date('Y-m-d H:i:s') . " - Configuração de produção executada\n";
file_put_contents('../logs/production_setup.log', $log_message, FILE_APPEND | LOCK_EX);
?>