<?php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../index.php");
    exit;
}
$secao_id = $_POST['id'] ?? '';
$titulo = $_POST['titulo'] ?? '';
$descricao = $_POST['descricao'] ?? '';
$idSecao = $_POST['id'];
$codigo = $_POST['codigo'];

if (empty($titulo) || empty($descricao)) {
    header("Location: ../admin/criar_votacao.php");
    exit;
}

$stmt = $pdo->prepare("INSERT INTO votacoes (titulo, descricao, id_secao, ativa) VALUES (?, ?, ?, 1)");
$stmt->execute([$titulo, $descricao, $idSecao]);

header("Location: ../admin/criar_votacao.php?codigo=" . urlencode($codigo));
exit;
