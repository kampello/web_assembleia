<?php
session_start();
require_once '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id_votacao'], $_POST['voto'])) {
    $votacao_id = (int) $_POST['id_votacao'];
    $opcao = $_POST['voto'];
    echo "Você precisa escolher uma cadeira antes de votar.";
    if (!isset($_SESSION['cadeira_id'])) {
        echo "Você precisa escolher uma cadeira antes de votar.";
        exit;
    }

    $stmt = $pdo->prepare("SELECT * FROM votacoes WHERE id = ?");
    $stmt->execute([$votacao_id]);
    $votacao = $stmt->fetch();

    if (!$votacao) {
        echo "Votação não encontrada.";
        exit;
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO votos (id_votacao, id_cadeira, opcao) VALUES (?, ?, ?)");
        $stmt->execute([$votacao_id, $_SESSION['cadeira_id'], $opcao]);

        header('Location: ../guest/listar_votacoes.php');
        exit;
    } catch (PDOException $e) {
        echo "Erro ao registrar o voto: " . $e->getMessage();
        exit;
    }
} else {
    echo "Dados inválidos.";
    exit;
}
?>
