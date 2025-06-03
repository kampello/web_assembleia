<?php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../index.php");
    exit;
}

$codigoSecao = $_GET['codigo'] ?? '';

if (empty($codigoSecao)) {
    header("Location: ../admin/dashboard.php?erro=Código inválido");
    exit;
}

// Elimina a seção com base no código
$stmt = $pdo->prepare("DELETE FROM secoes WHERE codigo = ?");
$stmt->execute([$codigoSecao]);

header("Location: ../admin/dashboard.php?sucesso=Seção eliminada");
exit;
