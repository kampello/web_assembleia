<?php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../index.php");
    exit;
}

$mensagem = '';

function gerarCodigoSecao(): string {
    $letras = strtoupper(chr(rand(65, 90)) . chr(rand(65, 90))); // A-Z
    $numeros = str_pad(strval(rand(0, 999)), 3, '0', STR_PAD_LEFT); // 000-999
    return $letras . $numeros;
}

$data_inicio = date('Y-m-d H:i:s'); // Data atual formatada corretamente

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = trim($_POST['titulo'] ?? '');
    $descricao = trim($_POST['descricao'] ?? '');
    $data_fim_raw = $_POST['data_fim'] ?? null;
    $codigo = gerarCodigoSecao();
    $criador_id = $_SESSION['admin_id'];

    if ($titulo && $data_fim_raw) {
        $data_fim = date('Y-m-d H:i:s', strtotime($data_fim_raw));

        if ($data_fim <= $data_inicio) {
            $mensagem = "⚠️ A data de expiração deve ser posterior ao momento atual.";
        } else {
            try {
                $stmt = $pdo->prepare("INSERT INTO secoes (titulo, codigo, descricao, data_inicio, data_fim, criador_id) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->execute([$titulo, $codigo, $descricao, $data_inicio, $data_fim, $criador_id]);
                $mensagem = "✅ Seção criada com sucesso! Código: <strong>$codigo</strong>";
            } catch (PDOException $e) {
                $mensagem = "❌ Erro ao criar seção: " . $e->getMessage();
            }
        }
    } else {
        $mensagem = "⚠️ Preenche todos os campos obrigatórios.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-PT">
<head>
    <meta charset="UTF-8">
    <title>Criar Nova Seção</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/secao.css">
    <link rel="stylesheet" href="../css/btn.css">
</head>
<body>
<div class="card">
    <h2>Criar Nova Seção</h2>

    <?php if ($mensagem): ?>
        <div class="mensagem"><?= $mensagem ?></div>
    <?php endif; ?>

    <!-- Substituir os campos de data por apenas a data_fim -->
    <form method="post">
        <label for="titulo">Título</label>
        <input type="text" id="titulo" name="titulo" placeholder="Título da seção" required>

        <label for="descricao">Descrição</label>
        <textarea id="descricao" name="descricao" rows="4" placeholder="Breve descrição..."></textarea>

        <!-- Campo oculto para data de início -->
        <input type="hidden" name="data_inicio" value="<?= date('Y-m-d\TH:i') ?>">

        <label for="data_fim">Data de expiração</label>
        <input type="datetime-local" id="data_fim" name="data_fim" required min="<?= date('Y-m-d\TH:i') ?>">

        <button type="submit" class="btn">➕ Criar Seção</button>
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
