<?php
require_once '../includes/db.php';

$id_votacao = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$stmt = $pdo->prepare("SELECT opcao, COUNT(*) as total FROM votos WHERE id_votacao = ? GROUP BY opcao");
$stmt->execute([$id_votacao]);
$votos = $stmt->fetchAll();

$total_geral = 0;
$contagem = ['A Favor' => 0, 'Contra' => 0, 'Abstenção' => 0];
foreach ($votos as $voto) {
    $contagem[$voto['opcao']] = $voto['total'];
    $total_geral += $voto['total'];
}

$percentagens = [];
foreach ($contagem as $opcao => $total) {
    $percentagens[$opcao] = $total_geral > 0 ? round(($total / $total_geral) * 100, 1) : 0;
}

echo json_encode([
    'total' => $total_geral,
    'percentagens' => $percentagens
]);
