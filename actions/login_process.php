<?php
session_start();
include_once '../config/database.php';

// Debug: verificar se chegou ao script
error_log("Login attempt - Script started");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    error_log("Login attempt - Not POST method");
    header('Location: ../pages/login.php');
    exit;
}

$username = trim($_POST['username']);
$password = $_POST['password'];

error_log("Login attempt - Username: " . $username);

if (empty($username) || empty($password)) {
    error_log("Login attempt - Empty fields");
    $_SESSION['login_error'] = 'Por favor, preencha todos os campos';
    header('Location: ../pages/login.php');
    exit;
}

try {
    // Buscar usuário por username ou email
    error_log("Login attempt - Searching user in database");
    $user = fetchOne("SELECT * FROM users WHERE username = ? OR email = ?", [$username, $username]);
    
    if (!$user) {
        error_log("Login attempt - User not found: " . $username);
        $_SESSION['login_error'] = 'Usuário não encontrado';
        header('Location: ../pages/login.php');
        exit;
    }
    
    error_log("Login attempt - User found: " . $user['username'] . " (ID: " . $user['id'] . ")");
    
    // Verificar senha
    $passwordValid = password_verify($password, $user['password']);
    error_log("Login attempt - Password verification: " . ($passwordValid ? 'SUCCESS' : 'FAILED'));
    
    if (!$passwordValid) {
        error_log("Login attempt - Password incorrect for user: " . $username);
        $_SESSION['login_error'] = 'Senha incorreta';
        header('Location: ../pages/login.php');
        exit;
    }
    
    // Login bem-sucedido
    error_log("Login attempt - LOGIN SUCCESS for user: " . $username);
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_name'] = $user['name'];
    $_SESSION['user_type'] = $user['user_type'];
    $_SESSION['username'] = $user['username'];
    
    // Redirecionar baseado no tipo de usuário
    if ($user['user_type'] === 'admin') {
        error_log("Login attempt - Redirecting to admin dashboard");
        header('Location: ../admin/dashboard.php');
    } else {
        error_log("Login attempt - Redirecting to homepage");
        header('Location: ../index.php');
    }
    exit;
    
} catch (Exception $e) {
    error_log("Login attempt - Exception: " . $e->getMessage());
    $_SESSION['login_error'] = 'Erro interno do servidor: ' . $e->getMessage();
    header('Location: ../pages/login.php');
    exit;
}
?>
