<?php
session_start();
require_once '../includes/db.php';

/*// Verificar se o utilizador escolheu uma cadeira
if (!isset($_SESSION['cadeira_id'])) {
    header("Location: escolher_cadeira.php");
    exit;
}*/
// Validar código da seção
$codigo = $_POST['codigo'] ?? $_SESSION['codigo_secao'] ?? '';
$stmt = $pdo->prepare("SELECT * FROM secoes WHERE codigo = ?");
$stmt->execute([$codigo]);
$secao = $stmt->fetch();

// ID da cadeira do usuário
$cadeira_id = $_SESSION['cadeira_id'];

// Buscar todas as votações ativas
$stmt = $pdo->prepare("SELECT * FROM votacoes WHERE ativa = 1 AND id_secao = ? ORDER BY id DESC");
$stmt->execute([$secao['id']]);
$votacoes = $stmt->fetchAll(PDO::FETCH_ASSOC); // Isso vai retornar um array associativo


// Filtrar votações para mostrar apenas as que o usuário ainda não votou
$votacoes_disponiveis = [];

foreach ($votacoes as $votacao) {
    // Verificar se o usuário já votou nesta votação
    $stmt = $pdo->prepare("SELECT 1 FROM votos WHERE id_votacao = ? AND id_cadeira = ?");
    $stmt->execute([$votacao['id'], $cadeira_id]);
    $voto_existente = $stmt->fetch();

    if (!$voto_existente) {
        // Se o usuário ainda não votou nesta votação, adicionar à lista
        $votacoes_disponiveis[] = $votacao;
    }
}

// Se houver votações disponíveis, redirecionar automaticamente para a próxima
if (count($votacoes_disponiveis) > 0) {
    // Redirecionar para a primeira votação disponível
    header("Location: votar.php?id=" . $votacoes_disponiveis[0]['id']);
    exit;
}

?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Votações Disponíveis</title>
    <link rel="stylesheet" href="../css/secao.css">
    <link rel="stylesheet" href="../css/btn.css">
</head>
<body class = "meu1">
<div>
<h1>Votações Abertas</h1>

<?php if (count($votacoes_disponiveis) > 0): ?>
    <ul>
        <?php foreach ($votacoes_disponiveis as $votacao): ?>
            <li>
                <strong><?= htmlspecialchars($votacao['titulo']) ?></strong><br>
                <?= nl2br(htmlspecialchars($votacao['descricao'])) ?><br>
                <a href="votar.php?id=<?= $votacao['id'] ?>">🗳️ Votar nesta votação</a>
            </li>
            <hr>
        <?php endforeach; ?>
    </ul>
<?php else: ?>
    <p>✅ Você já votou em todas as votações disponíveis.</p>
<?php endif; ?>

    <center>
        <a class="link-azul" href="../index.php">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1024 1024" width="16" height="16">
                <path d="M874.7 495.5c0 11.3-9.2 20.5-20.5 20.5H249.4l188.1 188.1c8 8 8 21 0 29-4 4-9.2 6-14.5 6s-10.5-2-14.5-6L185.4 510.6c-3.8-3.8-6-9-6-14.5s2.2-10.6 6-14.5L408.4 258.6c8-8 21-8 29 0s8 21 0 29L249.4 475h604.8c11.3 0 20.5 9.2 20.5 20.5z"/>
            </svg>
            <span>Back</span>
        </a>
    </center>
</div>
</body>
</html>

