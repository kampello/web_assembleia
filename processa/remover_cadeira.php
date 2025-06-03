<?php
require_once '../includes/db.php';
session_start();

// Verificar se o admin está logado
if (!isset($_SESSION['admin_id'])) {
    header('Location: ../index.php');
    exit;
}

// Verificar se o ID da cadeira foi passado
if (isset($_GET['id'])) {
    $cadeira_id = $_GET['id'];

    // Remover a cadeira do banco de dados
    $stmt = $pdo->prepare("DELETE FROM cadeiras WHERE id = ?");
    $stmt->execute([$cadeira_id]);
}

// Redirecionar para a página de gerenciamento de cadeiras
header('Location: ../admin/gerir_cadeiras.php');
exit;
?>

