<?php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../index.php");
    exit;
}

if (!isset($_GET['codigo'])) {
    die("Código da seção não fornecido.");
}

$codigo = $_GET['codigo'];

$stmt = $pdo->prepare("SELECT * FROM secoes WHERE codigo = ?");
$stmt->execute([$codigo]);
$secao = $stmt->fetch();

if (!$secao) {
    die("Seção não encontrada.");
}

$stmtVotacoes = $pdo->prepare("SELECT * FROM votacoes WHERE id_secao = ?");
$stmtVotacoes->execute([$secao['id']]);
$votacoes = $stmtVotacoes->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Criar Votação</title>
    <link rel="stylesheet" href="../css/secao.css">
    <link rel="stylesheet" href="../css/btn.css">
</head>
<body>
<div class="card">
    <h2>Criar Nova Votação para a Seção: <?= htmlspecialchars($secao['titulo']) ?></h2>

    <div class="lista-votacoes">
        <ul>
            <?php foreach ($votacoes as $votacao): ?>
                <li>
                    <strong><?= htmlspecialchars($votacao['titulo']) ?></strong><br>
                    <?= nl2br(htmlspecialchars($votacao['descricao'])) ?><br>
                    Estado: <em><?= $votacao['ativa'] ? 'Ativa' : 'Encerrada' ?></em><br>

                    <?php if ($votacao['ativa']): ?>
                        <form action="../processa/encerrar_votacao.php" method="post">
                            <input type="hidden" name="id_votacao" value="<?= $votacao['id'] ?>">
                            <input type="hidden" name="codigo" value="<?= htmlspecialchars($codigo) ?>">
                            <button class="btn" type="submit">Encerrar Votação</button>
                        </form>
                    <?php else: ?>
                        <a class="voltar" href="../admin/resultados.php?id=<?= $votacao['id'] ?>&codigo=<?= urlencode($codigo) ?>">Ver Resultados</a>
                    <?php endif; ?>
                </li>
                <hr>
            <?php endforeach; ?>
        </ul>
    </div>


    <form action="../processa/criar_votacao.php" method="post">
        <input type="hidden" name="codigo" value="<?= htmlspecialchars($codigo) ?>">
        <input type="hidden" name="id" value="<?= htmlspecialchars($secao['id']) ?>">

        <label for="titulo">Título:</label>
        <input type="text" name="titulo" required>

        <label for="descricao">Descrição:</label>
        <textarea name="descricao" rows="5" required></textarea>

        <button class="btn" type="submit">Criar Votação</button>
    </form>

    <center>
        <a class="link-azul" href="./dashboard.php">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1024 1024" width="16" height="16">
                <path d="M874.7 495.5c0 11.3-9.2 20.5-20.5 20.5H249.4l188.1 188.1c8 8 8 21 0 29-4 4-9.2 6-14.5 6s-10.5-2-14.5-6L185.4 510.6c-3.8-3.8-6-9-6-14.5s2.2-10.6 6-14.5L408.4 258.6c8-8 21-8 29 0s8 21 0 29L249.4 475h604.8c11.3 0 20.5 9.2 20.5 20.5z"/>
            </svg>
            <span>Back</span>
        </a>
    </center>
</div>
</body>
</html>
