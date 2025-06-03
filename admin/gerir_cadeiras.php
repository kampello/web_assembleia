<?php
session_start();
require_once '../includes/db.php';

// Verifica se está logado
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../index.php");
    exit;
}

// Verifica se foi passado um código de seção
if (!isset($_GET['codigo'])) {
    echo "Código da seção não fornecido.";
    exit;
}

$codigo_secao = $_GET['codigo'];

// Busca a seção correspondente
$stmt = $pdo->prepare("SELECT * FROM secoes WHERE codigo = ?");
$stmt->execute([$codigo_secao]);
$secao = $stmt->fetch();

if (!$secao) {
    echo "Seção não encontrada.";
    exit;
}

// Busca cadeiras já existentes
$stmtCadeiras = $pdo->prepare("SELECT nome, linha, coluna, ocupado FROM cadeiras WHERE id_secao = ?");
$stmtCadeiras->execute([$secao['id']]);
$cadeirasExistentes = $stmtCadeiras->fetchAll(PDO::FETCH_ASSOC);
$cadeirasJson = json_encode($cadeirasExistentes);
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Gerir Cadeiras - <?= htmlspecialchars($secao['titulo']) ?></title>
    <link rel="stylesheet" href="../css/secao.css">
    <link rel="stylesheet" href="../css/btn.css">
    <style>

    </style>
</head>
<body>
<div class="container">
    <h1>Gerir Cadeiras - <?= htmlspecialchars($secao['titulo']) ?></h1>

    <p>Total de cadeiras: <span id="contador">0</span></p>

    <div class="controls">
        <button onclick="addRow()">+ Linha</button>
        <button onclick="addCol()">+ Coluna</button>
        <button onclick="save()">Salvar</button>
    </div>

    <div id="grid"></div>

    <center>
        <a class="link-azul" href="./dashboard.php">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1024 1024" width="16" height="16">
                <path d="M874.7 495.5c0 11.3-9.2 20.5-20.5 20.5H249.4l188.1 188.1c8 8 8 21 0 29-4 4-9.2 6-14.5 6s-10.5-2-14.5-6L185.4 510.6c-3.8-3.8-6-9-6-14.5s2.2-10.6 6-14.5L408.4 258.6c8-8 21-8 29 0s8 21 0 29L249.4 475h604.8c11.3 0 20.5 9.2 20.5 20.5z"/>
            </svg>
            <span>Back</span>
        </a>
    </center>
</div>

<script>
    const cadeirasExistentes = <?= $cadeirasJson ?>;
    let linhas = 0;
    let colunas = 0;
    let grid = [];

    function inicializarGrid() {
        let maxLinha = 0, maxColuna = 0;
        cadeirasExistentes.forEach(c => {
            if (c.linha > maxLinha) maxLinha = c.linha;
            if (c.coluna > maxColuna) maxColuna = c.coluna;
        });

        linhas = maxLinha + 1 || 2;
        colunas = maxColuna + 1 || 2;

        grid = Array.from({length: linhas}, () => Array(colunas).fill(0));
        cadeirasExistentes.forEach(c => {
            grid[c.linha][c.coluna] = parseInt(c.ocupado);
        });

        desenharGrid();
    }

    function desenharGrid() {
        const container = document.getElementById('grid');
        container.innerHTML = '';
        container.style.gridTemplateColumns = `repeat(${colunas}, 50px)`;

        let contador = 0;

        grid.forEach((linha, i) => {
            linha.forEach((celula, j) => {
                const div = document.createElement('div');
                div.className = 'cell';
                const nome = String.fromCharCode(65 + i) + (j + 1);
                div.innerText = nome;
                if (celula === 1) div.classList.add('assento');
                div.onclick = () => {
                    grid[i][j] = grid[i][j] ? 0 : 1;
                    desenharGrid();
                };
                container.appendChild(div);
                contador++;
            });
        });

        document.getElementById('contador').innerText = contador;
    }

    function addRow() {
        linhas++;
        grid.push(Array(colunas).fill(0));
        desenharGrid();
    }

    function addCol() {
        colunas++;
        grid.forEach(linha => linha.push(0));
        desenharGrid();
    }

    function save() {
        const cadeiras = [];

        grid.forEach((linha, i) => {
            linha.forEach((celula, j) => {
                const nome = String.fromCharCode(65 + i) + (j + 1);
                const ocupado = celula === 1 ? 1 : 0;
                cadeiras.push({
                    nome: nome,
                    linha: i,
                    coluna: j,
                    ocupado: ocupado
                });
            });
        });

        if (cadeiras.length === 0) {
            alert("Por favor, selecione ao menos uma cadeira!");
            return;
        }

        fetch('../processa/adicionar_cadeira.php?codigo=<?= urlencode($codigo_secao) ?>', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ cadeiras: cadeiras })
        })
            .then(response => response.text())
            .then(data => {
                alert('Cadeiras salvas com sucesso!');
                console.log('Resposta do servidor:', data);
            })
            .catch(error => {
                console.error('Erro ao salvar as cadeiras:', error);
            });
    }

    inicializarGrid();
</script>
</body>
</html>
