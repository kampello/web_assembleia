<?php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../index.php");
    exit;
}

if (!isset($_GET['id'])) {
    die("ID da votação não fornecido.");
}

$id_votacao = $_GET['id'];

// Buscar a votação e sua seção
$stmt = $pdo->prepare("SELECT v.*, s.titulo AS titulo_secao, s.codigo FROM votacoes v JOIN secoes s ON v.id_secao = s.id WHERE v.id = ?");
$stmt->execute([$id_votacao]);
$votacao = $stmt->fetch();

if (!$votacao) {
    die("Votação não encontrada.");
}

// Buscar e agrupar votos diretamente (assumindo campo 'voto' na tabela votos)
$stmtVotos = $pdo->prepare("SELECT votos, COUNT(*) AS total FROM votos WHERE id_votacao = ? GROUP BY votos ORDER BY total DESC");
$stmtVotos->execute([$id_votacao]);
$resultados = $stmtVotos->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Resultados da Votação</title>
    <link rel="stylesheet" href="../css/estilo.css">
    <link rel="stylesheet" href="../css/btn.css">
</head>
<body>
<h1>Resultados da Votação: <?= htmlspecialchars($votacao['titulo']) ?></h1>
<p><strong>Seção:</strong> <?= htmlspecialchars($votacao['titulo_secao']) ?></p>
<p><strong>Descrição:</strong><br><?= nl2br(htmlspecialchars($votacao['descricao'])) ?></p>
<p><strong>Status:</strong> <?= $votacao['ativa'] ? 'Ativa' : 'Encerrada' ?></p>

<h2>Votos:</h2>
<ul>
    <?php foreach ($resultados as $resultado): ?>
        <li><?= htmlspecialchars(ucfirst($resultado['voto'])) ?> — <strong><?= $resultado['total'] ?> voto(s)</strong></li>
    <?php endforeach; ?>
</ul>

<center>
    <a class="link-azul" href="./dashboard.php">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1024 1024" width="16" height="16">
            <path d="M874.7 495.5c0 11.3-9.2 20.5-20.5 20.5H249.4l188.1 188.1c8 8 8 21 0 29-4 4-9.2 6-14.5 6s-10.5-2-14.5-6L185.4 510.6c-3.8-3.8-6-9-6-14.5s2.2-10.6 6-14.5L408.4 258.6c8-8 21-8 29 0s8 21 0 29L249.4 475h604.8c11.3 0 20.5 9.2 20.5 20.5z"/>
        </svg>
        <span>Back</span>
    </a>
</center>
</body>
</html>
