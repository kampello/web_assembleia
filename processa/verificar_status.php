<?php
require_once '../includes/db.php';

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

$stmt = $pdo->prepare("SELECT ativa FROM votacoes WHERE id = ?");
$stmt->execute([$id]);
$votacao = $stmt->fetch();

echo json_encode(['ativa' => $votacao ? (bool)$votacao['ativa'] : false]);
