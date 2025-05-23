<?php
// public/register.php (CONTROLLER) <--- Este é o controller

require_once '../includes/db.php';
require_once '../includes/mailer.php';

$errors = []; // Esta variável será passada para a view

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $birth_date = $_POST['birth_date'] ?? '';
    $nationality = $_POST['nationality'] ?? '';
    $country = $_POST['country'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm'] ?? '';

    // Validação simples
    if (!$name || !$birth_date || !$email || !$password) {
        $errors[] = "Todos os campos obrigatórios devem ser preenchidos.";
    }
    if ($password !== $confirm) {
        $errors[] = "As passwords não coincidem.";
    }

    // Verifica se o email já existe
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        $errors[] = "Este email já está registado.";
    }

    if (empty($errors)) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $token = bin2hex(random_bytes(16));

        $stmt = $pdo->prepare("INSERT INTO users (name, birth_date, nationality, country, email, phone, password, verification_token)
                               VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$name, $birth_date, $nationality, $country, $email, $phone, $hash, $token]);

        if (sendVerificationEmail($email, $name, $token)) {
            header("Location: ../views/auth/sucesso_registo.php");
            exit;
        } else {
            $errors[] = "Erro ao enviar email de verificação.";
        }
    }
}

// No final do controller, inclua a view.
// As variáveis como $errors estarão disponíveis na view.
// Já está correto!
include '../includes/header.php'; // Se tiver um header comum
include '../views/auth/register.php'; // A view específica do registo
include '../includes/footer.php'; // Se tiver um footer comum