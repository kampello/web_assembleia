<?php
session_start();
require_once '../includes/db.php';

$nome = $_POST['email'] ?? '';
$senha = $_POST['senha'] ?? '';

$stmt = $pdo->prepare("SELECT * FROM usuarios WHERE nome = ? AND tipo = 'admin'");
$stmt->execute([$nome]);
$admin = $stmt->fetch();

if ($admin && password_verify($senha, $admin['senha'])) {
    $_SESSION['admin_id'] = $admin['id'];
    header("Location: ../admin/dashboard.php");
    exit;
} else {
    $_SESSION['erro_login'] = "Login inválido.";
    header("Location: ../index.php");
    exit;
}
