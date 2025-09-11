<?php
session_start();
include_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../pages/login.php');
    exit;
}

// Capturar dados do formulário
$token = trim($_POST['token']);
$password = $_POST['password'];
$confirmPassword = $_POST['confirm_password'];

// Validações básicas
if (empty($token) || empty($password) || empty($confirmPassword)) {
    $_SESSION['reset_error'] = 'Todos os campos são obrigatórios';
    header('Location: ../pages/reset_password.php?token=' . urlencode($token));
    exit;
}

if ($password !== $confirmPassword) {
    $_SESSION['reset_error'] = 'As senhas não coincidem';
    header('Location: ../pages/reset_password.php?token=' . urlencode($token));
    exit;
}

if (strlen($password) < 6) {
    $_SESSION['reset_error'] = 'A senha deve ter pelo menos 6 caracteres';
    header('Location: ../pages/reset_password.php?token=' . urlencode($token));
    exit;
}

try {
    // Verificar se o token é válido
    $resetData = fetchOne("
        SELECT pr.*, u.id as user_id, u.email, u.name 
        FROM password_resets pr 
        JOIN users u ON pr.user_id = u.id 
        WHERE pr.token = ? AND pr.expires_at > NOW() AND pr.used = FALSE
    ", [$token]);
    
    if (!$resetData) {
        $_SESSION['reset_error'] = 'Token inválido ou expirado';
        header('Location: ../pages/reset_password.php?token=' . urlencode($token));
        exit;
    }
    
    // Hash da nova senha
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    
    // Atualizar senha do usuário
    executeQuery("UPDATE users SET password = ? WHERE id = ?", 
                 [$hashedPassword, $resetData['user_id']]);
    
    // Marcar token como usado
    executeQuery("UPDATE password_resets SET used = TRUE WHERE id = ?", 
                 [$resetData['id']]);
    
    // Log para auditoria
    error_log("Senha redefinida com sucesso para usuário: " . $resetData['email']);
    
    // Redirecionar para login com mensagem de sucesso
    $_SESSION['login_success'] = 'Senha redefinida com sucesso! Faça login com sua nova senha.';
    header('Location: ../pages/login.php');
    exit;
    
} catch (Exception $e) {
    error_log("Erro ao redefinir senha: " . $e->getMessage());
    $_SESSION['reset_error'] = 'Erro interno. Tente novamente mais tarde.';
    header('Location: ../pages/reset_password.php?token=' . urlencode($token));
    exit;
}
?>
