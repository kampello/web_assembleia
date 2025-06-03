<?php
session_start();
require_once '../includes/db.php';
// Validar código da seção
$codigo = $_POST['codigo'] ?? $_SESSION['codigo_secao'] ?? '';
$stmt = $pdo->prepare("SELECT * FROM secoes WHERE codigo = ?");
$stmt->execute([$codigo]);
$secao = $stmt->fetch();

$id_votacao = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($id_votacao <= 0) {
    header("Location: listar_votacoes.php");
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM votacoes WHERE id = ?");
$stmt->execute([$id_votacao]);
$votacao = $stmt->fetch();

if (!$votacao) {
    header("Location: listar_votacoes.php");
    exit;
}

// Obter os votos
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
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Votação: <?= htmlspecialchars($votacao['titulo']) ?></title>
    <link rel="stylesheet" href="../css/expesial.css">
    <link rel="stylesheet" href="../css/btn.css">
    <style>

    </style>
</head>

<body>

<header>
    <h1 style="color: white"><?= htmlspecialchars($votacao['titulo']) ?></h1>
</header>

<div class="container">
    <p><?= nl2br(htmlspecialchars($votacao['descricao'])) ?></p>

    <?php if ($total_geral > 0): ?>
        <h3>Resultados até agora:</h3>
        <div class="barra-votacao">
            <div class="segmento favor" style="width: <?= $percentagens['A Favor'] ?>%">
                <?= round($percentagens['A Favor']) ?>%
            </div>
            <div class="segmento contra" style="width: <?= $percentagens['Contra'] ?>%">
                <?= round($percentagens['Contra']) ?>%
            </div>
            <div class="segmento abstencao" style="width: <?= $percentagens['Abstenção'] ?>%">
                <?= round($percentagens['Abstenção']) ?>%
            </div>
        </div>

        <div class="legenda">
            <p><span class="bolinha favor-bg"></span> A Favor: <?= $contagem['A Favor'] ?> voto(s)</p>
            <p><span class="bolinha contra-bg"></span> Contra: <?= $contagem['Contra'] ?> voto(s)</p>
            <p><span class="bolinha abstencao-bg"></span> Abstenção: <?= $contagem['Abstenção'] ?> voto(s)</p>
            <p><strong>Total de votos:</strong> <?= $total_geral ?></p>
        </div>
    <?php else: ?>
        <p>Ainda não houve votos nesta votação.</p>
    <?php endif; ?>

    <?php if ($votacao['ativa']): ?>
        <p class="aberta">🟢 A votação está aberta</p>
        <form action="../processa/votar.php" method="post" onsubmit="votoEnviado = true;">
            <label for="voto">Escolha a sua opção:</label>
            <div class="radio-opcao"><input type="radio" name="voto" value="A Favor" required> A Favor</div>
            <div class="radio-opcao"><input type="radio" name="voto" value="Contra" required> Contra</div>
            <div class="radio-opcao"><input type="radio" name="voto" value="Abstenção" required> Abstenção</div>
            <input type="hidden" name="id_votacao" value="<?= $votacao['id'] ?>">
            <button type="submit">Votar</button>
        </form>
    <?php else: ?>
        <p><strong>🔒 Esta votação já foi encerrada.</strong></p>
    <?php endif; ?>


</div>



</body>
</html>
