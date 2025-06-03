<?php
require_once '../includes/db.php';

header('Content-Type: application/json');

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$acao = $_GET['acao'] ?? '';

if ($id <= 0 || !in_array($acao, ['verificar', 'resultados'])) {
    echo json_encode(['erro' => 'Parâmetros inválidos.']);
    exit;
}

if ($acao === 'verificar') {
    // Verifica se a votação ainda está ativa
    $stmt = $pdo->prepare("SELECT ativa FROM votacoes WHERE id = ?");
    $stmt->execute([$id]);
    $votacao = $stmt->fetch();

    if ($votacao) {
        echo json_encode(['ativa' => (bool) $votacao['ativa']]);
    } else {
        echo json_encode(['erro' => 'Votação não encontrada.']);
    }
    exit;
}

if ($acao === 'resultados') {
    // Obtem resultados da votação
    $stmt = $pdo->prepare("SELECT opcao, COUNT(*) as total FROM votos WHERE id_votacao = ? GROUP BY opcao");
    $stmt->execute([$id]);
    $votos = $stmt->fetchAll();

    $total_geral = 0;
    $contagem = ['A Favor' => 0, 'Contra' => 0, 'Abstenção' => 0];

    foreach ($votos as $voto) {
        $contagem[$voto['opcao']] = (int) $voto['total'];
        $total_geral += (int) $voto['total'];
    }

    $percentagens = [];
    foreach ($contagem as $opcao => $total) {
        $percentagens[$opcao] = $total_geral > 0 ? round(($total / $total_geral) * 100, 1) : 0;
    }

    echo json_encode([
        'total' => $total_geral,
        'percentagens' => $percentagens
    ]);
    exit;
}
