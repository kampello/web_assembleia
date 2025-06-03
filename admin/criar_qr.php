<?php
// Obtém o código da URL
$codigo = $_GET['codigo'] ?? '';

if (empty($codigo)) {
    die("Código não fornecido.");
}
$minha_url  ="http://localhost:80/";
// URL final que o QR Code apontará
$urlFinal = $minha_url."guest/escolher_cadeira.php?codigo=" . urlencode($codigo);

// Gera o link do QR Code com API do Google Chart
$qrCodeUrl = "https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=" . urlencode($urlFinal);
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <link rel="stylesheet" href="../css/secao.css">
    <link rel="stylesheet" href="../css/btn.css">
    <meta charset="UTF-8">
    <title>QR Code para <?= htmlspecialchars($codigo) ?></title>
    <style>
        body {
            font-family: sans-serif;
            text-align: center;
            margin-top: 50px;
        }
        img {
            border: 4px solid #000;
            padding: 10px;
            background: #fff;
        }
    </style>
</head>
<body>
<div class="card">
    <h1>QR Code para o código <strong><?= htmlspecialchars($codigo) ?></strong></h1>
    <p>Escaneie para abrir:</p>
    <img src="<?= $qrCodeUrl ?>" alt="QR Code">
    <p><a href="<?= $urlFinal ?>" target="_blank"><?= $urlFinal ?></a></p>
    <center>
        <a class="link-azul" href="./dashboard.php">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1024 1024" width="16" height="16">
                <path d="M874.7 495.5c0 11.3-9.2 20.5-20.5 20.5H249.4l188.1 188.1c8 8 8 21 0 29-4 4-9.2 6-14.5 6s-10.5-2-14.5-6L185.4 510.6c-3.8-3.8-6-9-6-14.5s2.2-10.6 6-14.5L408.4 258.6c8-8 21-8 29 0s8 21 0 29L249.4 475h604.8c11.3 0 20.5 9.2 20.5 20.5z"/>
            </svg>
            <span>Back</span>
        </a>
    </center>
   <!-- <a class="voltar" href="./dashboard.php">← Voltar à Dashboard</a>-->
</div>
</body>

</html>
