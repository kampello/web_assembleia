<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['encerrar_votacao'])) {
    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    if ($id > 0) {
        $stmt = $pdo->prepare("UPDATE votacoes SET ativa = 0 WHERE id = ?");
        $stmt->execute([$id]);
        header("Location: bigpicture.php?codigo=" . urlencode($_GET['codigo']) . "&id=" . $id);
        exit;
    }
}

session_start();
require_once '../includes/db.php';

$codigo = $_GET['codigo'] ?? '';
$id_votacao = isset($_GET['id']) ? (int) $_GET['id'] : 0;

// Obter a secção
$stmt = $pdo->prepare("SELECT * FROM secoes WHERE codigo = ?");
$stmt->execute([$codigo]);
$secao = $stmt->fetch();

if (!$secao) {
    header("Location: dashboard.php");
    exit;
}

// Buscar todas as votações da mesma secção
$stmt = $pdo->prepare("SELECT * FROM votacoes WHERE id_secao = ? ORDER BY id DESC");
$stmt->execute([$secao['id']]);
$votacoes = $stmt->fetchAll();

if (!$votacoes) {
    header("Location: dashboard.php");
    exit;
}

// Procurar a votação correspondente
$index_votacao = null;
foreach ($votacoes as $i => $v) {
    if ($v['id'] == $id_votacao) {
        $index_votacao = $i;
        break;
    }
}

// Se não foi passado ID ou o ID é inválido, usar a primeira votação
if ($index_votacao === null) {
    $votacao = $votacoes[0];
    $id_votacao = $votacao['id'];
    $index_votacao = 0;
} else {
    $votacao = $votacoes[$index_votacao];
}

// Obter votos
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
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Votação: <?= htmlspecialchars($votacao['titulo']) ?></title>
    <link rel="stylesheet" href="../css/expesial.css" />
    <link rel="stylesheet" href="../css/btn.css" />
</head>
<body>

<header>
    <h1 style="color: white"><?= htmlspecialchars($votacao['titulo']) ?></h1>
</header>

<div class="container">
    <p><?= nl2br(htmlspecialchars($votacao['descricao'])) ?></p>

    <p class="status-votacao">
        <?php if ($votacao['ativa']): ?>
            🟢 A votação está aberta
            <?php if ($votacao['ativa']): ?>
            <form method="post" action="../processa/encerrar_bigpic.php" onsubmit="return confirmarEncerramento();" style="margin-top: 1em;">
                <input type="hidden" name="id_votacao" value="<?= htmlspecialchars($id_votacao) ?>">
                <input type="hidden" name="codigo" value="<?= htmlspecialchars($codigo) ?>">
                <button type="submit" class="btn-danger">Encerrar Votação</button>
            </form>

            <?php endif; ?>
        <?php else: ?>
            🔒 Esta votação já foi encerrada.
        <?php endif; ?>
    </p>

    <?php if ($total_geral > 0): ?>
        <h3>Resultados:</h3>
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

    <!-- Navegação entre votações -->
    <div class="botoes-navegacao">
        <?php if ($index_votacao > 0): ?>
            <button class="voltar-btn" onclick="window.location.href='bigpicture.php?codigo=<?= urlencode($codigo) ?>&id=<?= $votacoes[$index_votacao - 1]['id'] ?>'">◀ Voltar</button>
        <?php endif; ?>
        <?php if ($index_votacao < count($votacoes) - 1): ?>
            <button class="proximo-btn" onclick="window.location.href='bigpicture.php?codigo=<?= urlencode($codigo) ?>&id=<?= $votacoes[$index_votacao + 1]['id'] ?>'">Próximo ▶</button>
        <?php endif; ?>
    </div>

    <a class="link-azul" href="./dashboard.php">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1024 1024" width="16" height="16">
            <path d="M874.7 495.5c0 11.3-9.2 20.5-20.5 20.5H249.4l188.1 188.1c8 8 8 21 0 29-4 4-9.2 6-14.5 6s-10.5-2-14.5-6L185.4 510.6c-3.8-3.8-6-9-6-14.5s2.2-10.6 6-14.5L408.4 258.6c8-8 21-8 29 0s8 21 0 29L249.4 475h604.8c11.3 0 20.5 9.2 20.5 20.5z"/>
        </svg>
        <span>Back</span>
    </a>
</div>

<script>
    const idVotacao = <?= (int)$id_votacao ?>;
    const checkInterval = 2000;
    function confirmarEncerramento() {
        return confirm("Tem a certeza que deseja encerrar esta votação?");
    }
    function verificarStatus() {
        fetch(`../processa/api_votacao.php?id=${idVotacao}&acao=verificar`)
            .then(response => {
                if (!response.ok) throw new Error("Erro ao verificar status");
                return response.json();
            })
            .then(data => {
                if (data) {
                    const status = document.querySelector('.status-votacao');
                    if (status) {
                        if (data.ativa) {
                            status.innerHTML = '🟢 A votação está aberta';
                        } else {
                            status.innerHTML = '🔒 A votação foi encerrada.';
                        }
                    }
                }
            })
            .catch(err => {
                console.error("Erro ao verificar status da votação:", err);
            });
    }

    function atualizarResultados() {
        fetch(`../processa/api_votacao.php?id=${idVotacao}&acao=resultados`)
            .then(response => {
                if (!response.ok) throw new Error("Erro ao obter resultados");
                return response.json();
            })
            .then(data => {
                if (data && data.total > 0 && data.percentagens) {
                    const favor = document.querySelector(".segmento.favor");
                    const contra = document.querySelector(".segmento.contra");
                    const abstencao = document.querySelector(".segmento.abstencao");

                    if (favor) {
                        favor.style.width = data.percentagens["A Favor"] + "%";
                        favor.textContent = Math.round(data.percentagens["A Favor"]) + "%";
                    }

                    if (contra) {
                        contra.style.width = data.percentagens["Contra"] + "%";
                        contra.textContent = Math.round(data.percentagens["Contra"]) + "%";
                    }

                    if (abstencao) {
                        abstencao.style.width = data.percentagens["Abstenção"] + "%";
                        abstencao.textContent = Math.round(data.percentagens["Abstenção"]) + "%";
                    }
                }
            })
            .catch(err => {
                console.error("Erro ao atualizar resultados da votação:", err);
            });
    }

    setInterval(() => {
        verificarStatus();
        atualizarResultados();
    }, checkInterval);
</script>

</body>
</html>
