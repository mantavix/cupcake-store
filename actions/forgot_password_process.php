<?php
session_start();
include_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../pages/forgot_password.php');
    exit;
}

// Capturar e validar email
$email = trim($_POST['email']);

if (empty($email)) {
    $_SESSION['forgot_error'] = 'Por favor, digite seu e-mail';
    header('Location: ../pages/forgot_password.php');
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['forgot_error'] = 'E-mail inválido';
    header('Location: ../pages/forgot_password.php');
    exit;
}

try {
    // Verificar se o usuário existe
    $user = fetchOne("SELECT id, name, email FROM users WHERE email = ?", [$email]);
    
    if (!$user) {
        // Por segurança, não revelamos se o email existe ou não
        $_SESSION['forgot_success'] = 'Se o e-mail estiver cadastrado, você receberá as instruções em breve.';
        header('Location: ../pages/forgot_password.php');
        exit;
    }
    
    // Gerar token único
    $token = bin2hex(random_bytes(32));
    
    // Definir expiração (1 hora)
    $expiresAt = date('Y-m-d H:i:s', strtotime('+1 hour'));
    
    // Invalidar tokens antigos para este usuário
    executeQuery("UPDATE password_resets SET used = TRUE WHERE user_id = ? AND used = FALSE", [$user['id']]);
    
    // Inserir novo token
    executeQuery("INSERT INTO password_resets (user_id, token, expires_at) VALUES (?, ?, ?)", 
                 [$user['id'], $token, $expiresAt]);
    
    // Gerar link de reset
    $resetLink = "http://localhost/cupcake-store/pages/reset_password.php?token=" . $token;
    
    // Log para debug (em produção, aqui seria enviado o email)
    error_log("Reset de senha solicitado para: $email");
    error_log("Link de reset: $resetLink");
    
    // Em um ambiente de produção, aqui seria enviado o email
    // Por enquanto, vamos simular enviando o link na própria sessão
    $_SESSION['forgot_success'] = 'Instruções enviadas! Por favor, verifique seu e-mail.';
    $_SESSION['reset_link_demo'] = $resetLink; // Para fins de demonstração
    
    header('Location: ../pages/forgot_password.php');
    exit;
    
} catch (Exception $e) {
    error_log("Erro no processo de recuperação de senha: " . $e->getMessage());
    $_SESSION['forgot_error'] = 'Erro interno. Tente novamente mais tarde.';
    header('Location: ../pages/forgot_password.php');
    exit;
}
?>
