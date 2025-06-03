<?php
session_start();
require_once '../includes/db.php';

// Verifica se está logado
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../index.php");
    exit;
}

// Busca as votações existentes
$stmt = $pdo->query("SELECT * FROM votacoes ORDER BY id DESC");
$votacoes = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Painel do Administrador</title>
    <link rel="stylesheet" href="../css/estilo.css">
</head>
<body>
<h1>Painel do Administrador</h1>

<p><a href="criar_votacao.php">➕ Criar nova votação</a></p>
<p><a href="gerir_cadeiras.php">➕ Criar cadeiras</a></p>

<h2>Votações existentes:</h2>


<?php if (count($votacoes) > 0): ?>
    <ul>
        <?php foreach ($votacoes as $votacao): ?>
            <li>
                <strong><?= htmlspecialchars($votacao['titulo']) ?></strong><br>
                <?= nl2br(htmlspecialchars($votacao['descricao'])) ?><br>
                Estado: <em><?= $votacao['ativa'] ? 'Ativa' : 'Encerrada' ?></em><br>

                <?php if ($votacao['ativa']): ?>
                    <form action="../processa/encerrar_votacao.php" method="post">
                        <input type="hidden" name="id_votacao" value="<?= $votacao['id'] ?>">
                        <button type="submit">Encerrar Votação</button>
                    </form>
                <?php else: ?>
                    <a href="../admin/resultados.php?id=<?= $votacao['id'] ?>">Ver Resultados</a>
                <?php endif; ?>
            </li>
            <hr>
        <?php endforeach; ?>
    </ul>
<?php else: ?>
    <p>Nenhuma votação criada ainda.</p>
<?php endif; ?>

</body>
</html>
