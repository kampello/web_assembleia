<?php
session_start();
require_once '../includes/db.php';

$nome = trim($_POST['email'] ?? '');
$senha = $_POST['senha'] ?? '';

// Verifica se os campos foram preenchidos
if (!$nome || !$senha) {
    $_SESSION['erro_login'] = "Preencha todos os campos.";
    header("Location: ../index.php");
    exit;
}

// Criptografa a senha
$senhaHash = password_hash($senha, PASSWORD_DEFAULT);

try {
    // Insere o novo administrador no banco
    $stmt = $pdo->prepare("INSERT INTO usuarios (nome, senha) VALUES (?, ?)");
    $stmt->execute([$nome, $senhaHash]);

    // Redireciona para login após o registro
    $_SESSION['erro_login'] = "Registro bem-sucedido. Faça login.";
    header("Location: ../index.php");
    exit;
} catch (Exception $e) {
    $_SESSION['erro_login'] = "Erro ao registrar: " . $e->getMessage();
    header("Location: ../index.php");
    exit;
}
