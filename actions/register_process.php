<?php
session_start();
include_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../pages/register.php');
    exit;
}

// Capturar dados do formulário
$name = trim($_POST['name']);
$email = trim($_POST['email']);
$username = trim($_POST['username']);
$password = $_POST['password'];
$confirmPassword = $_POST['confirm_password'];
$cpf = preg_replace('/[^0-9]/', '', $_POST['cpf']);
$phone = preg_replace('/[^0-9]/', '', $_POST['phone']);
$address = trim($_POST['address']);
$city = trim($_POST['city']);
$state = trim($_POST['state']);
$zipCode = preg_replace('/[^0-9]/', '', $_POST['zip_code']);

// Validações básicas
if (empty($name) || empty($email) || empty($username) || empty($password) || 
    empty($cpf) || empty($phone) || empty($address) || empty($city) || 
    empty($state) || empty($zipCode)) {
    $_SESSION['register_error'] = 'Todos os campos são obrigatórios';
    header('Location: ../pages/register.php');
    exit;
}

// Validar confirmação de senha
if ($password !== $confirmPassword) {
    $_SESSION['register_error'] = 'As senhas não coincidem';
    header('Location: ../pages/register.php');
    exit;
}

// Validar CPF
function validateCPF($cpf) {
    if (strlen($cpf) != 11 || preg_match('/(\d)\1{10}/', $cpf)) {
        return false;
    }
    
    for ($t = 9; $t < 11; $t++) {
        for ($d = 0, $c = 0; $c < $t; $c++) {
            $d += $cpf[$c] * (($t + 1) - $c);
        }
        $d = ((10 * $d) % 11) % 10;
        if ($cpf[$c] != $d) {
            return false;
        }
    }
    return true;
}

if (!validateCPF($cpf)) {
    $_SESSION['register_error'] = 'CPF inválido';
    header('Location: ../pages/register.php');
    exit;
}

// Validar email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['register_error'] = 'E-mail inválido';
    header('Location: ../pages/register.php');
    exit;
}

// Validar senha
if (strlen($password) < 6) {
    $_SESSION['register_error'] = 'A senha deve ter pelo menos 6 caracteres';
    header('Location: ../pages/register.php');
    exit;
}

try {
    // Log para debug
    error_log("Tentativa de cadastro - Email: $email, Username: $username, CPF: $cpf");
    
    // Verificar se já existe usuário com o mesmo email, username ou CPF
    $existingUser = fetchOne("SELECT id FROM users WHERE email = ? OR username = ? OR cpf = ?", 
                             [$email, $username, $cpf]);
    
    if ($existingUser) {
        error_log("Cadastro falhou - Usuário já existe");
        $_SESSION['register_error'] = 'Já existe um usuário com esse e-mail, nome de usuário ou CPF';
        header('Location: ../pages/register.php');
        exit;
    }
    
    // Hash da senha
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    
    // Formatar CPF e telefone para exibição
    $formattedCpf = substr($cpf, 0, 3) . '.' . substr($cpf, 3, 3) . '.' . substr($cpf, 6, 3) . '-' . substr($cpf, 9, 2);
    $formattedPhone = '(' . substr($phone, 0, 2) . ') ' . substr($phone, 2, 5) . '-' . substr($phone, 7, 4);
    $formattedZip = substr($zipCode, 0, 5) . '-' . substr($zipCode, 5, 3);
    
    // Inserir usuário
    error_log("Tentando inserir usuário no banco de dados");
    executeQuery("INSERT INTO users (name, email, username, password, cpf, phone, address, city, state, zip_code, user_type) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'customer')", 
                 [$name, $email, $username, $hashedPassword, $formattedCpf, $formattedPhone, $address, $city, $state, $formattedZip]);
    
    error_log("Usuário inserido com sucesso - Email: $email");
    $_SESSION['register_success'] = 'Cadastro realizado com sucesso! Faça login para continuar.';
    header('Location: ../pages/login.php');
    exit;
    
} catch (Exception $e) {
    error_log("Erro no cadastro: " . $e->getMessage());
    $_SESSION['register_error'] = 'Erro interno do servidor. Tente novamente.';
    header('Location: ../pages/register.php');
    exit;
}
?>
