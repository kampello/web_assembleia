<?php

session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../index.php");
    exit;
}

$id_votacao = $_POST['id_votacao'] ?? '';
$codigo = $_POST['codigo'] ?? '';

if (empty($id_votacao) || empty($codigo)) {
    header("Location: ../admin/dashboard.php");
    exit;
}

// Atualizar a votação
$stmt = $pdo->prepare("UPDATE votacoes SET ativa = 0 WHERE id = ?");
$stmt->execute([$id_votacao]);

// Redirecionar de volta à bigpicture com o mesmo código e ID
header("Location: ../admin/bigpicture.php?codigo=" . urlencode($codigo) . "&id=" . urlencode($id_votacao));
exit;
