<?php
session_start();
include_once '../config/database.php';

// Verificar se o usuário está logado
if (!isset($_SESSION['user_id'])) {
    header('Location: ../pages/login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../pages/profile.php');
    exit;
}

$userId = $_SESSION['user_id'];

// Capturar dados do formulário
$name = trim($_POST['name']);
$email = trim($_POST['email']);
$phone = preg_replace('/[^0-9]/', '', $_POST['phone']);
$address = trim($_POST['address']);
$city = trim($_POST['city']);
$state = trim($_POST['state']);
$zipCode = preg_replace('/[^0-9]/', '', $_POST['zip_code']);

// Validações básicas
if (empty($name) || empty($email) || empty($phone) || empty($address) || 
    empty($city) || empty($state) || empty($zipCode)) {
    $_SESSION['profile_error'] = 'Todos os campos são obrigatórios';
    header('Location: ../pages/profile.php');
    exit;
}

// Validar email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['profile_error'] = 'E-mail inválido';
    header('Location: ../pages/profile.php');
    exit;
}

try {
    // Verificar se já existe outro usuário com o mesmo email
    $existingUser = fetchOne("SELECT id FROM users WHERE email = ? AND id != ?", [$email, $userId]);
    
    if ($existingUser) {
        $_SESSION['profile_error'] = 'Já existe um usuário com esse e-mail';
        header('Location: ../pages/profile.php');
        exit;
    }
    
    // Formatar telefone e CEP para exibição
    $formattedPhone = '(' . substr($phone, 0, 2) . ') ' . substr($phone, 2, 5) . '-' . substr($phone, 7, 4);
    $formattedZip = substr($zipCode, 0, 5) . '-' . substr($zipCode, 5, 3);
    
    // Atualizar usuário
    executeQuery("UPDATE users SET name = ?, email = ?, phone = ?, address = ?, city = ?, state = ?, zip_code = ?, updated_at = NOW() WHERE id = ?", 
                 [$name, $email, $formattedPhone, $address, $city, $state, $formattedZip, $userId]);
    
    // Atualizar nome na sessão
    $_SESSION['user_name'] = $name;
    
    $_SESSION['profile_success'] = 'Perfil atualizado com sucesso!';
    header('Location: ../pages/profile.php');
    exit;
    
} catch (Exception $e) {
    $_SESSION['profile_error'] = 'Erro interno do servidor. Tente novamente.';
    header('Location: ../pages/profile.php');
    exit;
}
?>
