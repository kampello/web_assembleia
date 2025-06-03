<?php
session_start();
require_once '../includes/db.php';

$codigo = $_GET['codigo'] ?? $_POST['codigo'] ?? $_SESSION['codigo_secao'] ?? '';

if (empty($codigo)) {
    ?>
    <!DOCTYPE html>
    <html lang="pt">
    <head>
        <meta charset="UTF-8">
        <title>Entrar na Sala</title>
        <link href="https://cdn.jsdelivr.net/npm/quasar@2.14.3/dist/quasar.prod.css" rel="stylesheet">
        <link rel="stylesheet" href="../css/btn.css">
        <style>
            .pagina-especifica {
                background-image: url('../img/img2.png'); /* Caminho correto da imagem */
                background-size: cover;
                background-position: center;
            }

        </style>
    </head>
    <body  class="pagina-especifica">>
    <div id="app">
        <q-layout view="hHh lpR fFf">
            <q-header class="bg-primary text-white">
                <q-toolbar>
                    <q-toolbar-title>Entrar na Sala</q-toolbar-title>
                </q-toolbar>
            </q-header>

            <q-page-container class="pagina-especifica">

            <q-page class="q-pa-md flex flex-center">
                    <q-card class="q-pa-lg" style="max-width: 400px; width: 100%;">
                        <q-card-section>
                            <div class="text-h6">Código da Sala</div>
                        </q-card-section>
                        <q-card-section>
                            <form method="post">
                                <q-input
                                        label="Código"
                                        filled
                                        v-model="codigo"
                                        name="codigo"
                                        required
                                        class="q-mb-md"
                                ></q-input>
                                <input type="hidden" name="codigo" :value="codigo">
                                <q-btn type="submit" label="Entrar" color="primary" class="full-width"></q-btn>
                            </form>
                            <br>
                            <center>
                                <a class="link-azul" href="../">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1024 1024" width="16" height="16">
                                        <path d="M874.7 495.5c0 11.3-9.2 20.5-20.5 20.5H249.4l188.1 188.1c8 8 8 21 0 29-4 4-9.2 6-14.5 6s-10.5-2-14.5-6L185.4 510.6c-3.8-3.8-6-9-6-14.5s2.2-10.6 6-14.5L408.4 258.6c8-8 21-8 29 0s8 21 0 29L249.4 475h604.8c11.3 0 20.5 9.2 20.5 20.5z"/>
                                    </svg>
                                    <span>Back</span>
                                </a>
                            </center>
                        </q-card-section>
                    </q-card>
                </q-page>
            </q-page-container>
        </q-layout>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/vue@3/dist/vue.global.prod.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/quasar@2.14.3/dist/quasar.umd.prod.js"></script>
    <script>
        const { createApp } = Vue
        const app = createApp({
            data() {
                return {
                    codigo: ''
                }
            }
        })
        app.use(Quasar)
        app.mount('#app')
    </script>
    </body>
    </html>
    <?php
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM secoes WHERE codigo = ?");
$stmt->execute([$codigo]);
$secao = $stmt->fetch();

if (!$secao) {
    die("Código inválido ou sala não encontrada.");
}

$_SESSION['codigo_secao'] = $codigo;

$stmt = $pdo->prepare("SELECT * FROM cadeiras WHERE id_secao = ?");
$stmt->execute([$secao['id']]);
$mesas = $stmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['mesa'])) {
    $id_mesa = $_POST['mesa'];
    $stmt = $pdo->prepare("UPDATE cadeiras SET ocupado = 1 WHERE id = ? AND id_secao = ?");
    $stmt->execute([$id_mesa, $secao['id']]);
    $_SESSION['cadeira_id'] = $id_mesa;
    header("Location: listar_votacoes.php?codigo=" . urlencode($codigo));
    exit;
}

$colunas = 6;
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Escolher Cadeira</title>
    <link href="https://cdn.jsdelivr.net/npm/quasar@2.14.3/dist/quasar.prod.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/btn.css">
</head>
<body class="bg-grey-1">
<div id="app">

    <q-layout view="hHh lpR fFf">
        <q-header class="bg-primary text-white">
            <q-toolbar>
                <q-toolbar-title>Escolher Cadeira - Sala <?= htmlspecialchars($codigo) ?></q-toolbar-title>
            </q-toolbar>
        </q-header>

        <q-page-container>
            <q-page class="q-pa-md">
                <form method="post">
                    <div class="q-gutter-md row justify-start items-start">
                        <?php foreach ($mesas as $mesa): ?>
                            <q-card class="q-pa-md col-12 col-sm-4 col-md-2"
                                    style="min-height: 140px; background-color: <?= $mesa['ocupado'] ? '#e53935' : '#43a047' ?>; color: white;">
                                <div class="text-subtitle1"><?= htmlspecialchars($mesa['nome']) ?></div>
                                <div class="q-mt-sm">
                                    <?php if (!$mesa['ocupado']): ?>
                                        <button type="submit" name="mesa" value="<?= $mesa['id'] ?>" class="lista_Admin">
                                            ➡️🚪 Escolher
                                        </button>
                                    <?php else: ?>
                                        Ocupada
                                    <?php endif; ?>
                                </div>
                            </q-card>
                        <?php endforeach; ?>
                    </div>
                </form>

            </q-page>
        </q-page-container>
    </q-layout>
    <a href="../index.php">
        <button class="Btn">

            <div class="sign">
                <svg viewBox="0 0 512 512">
                    <path d="M377.9 105.9L500.7 228.7c7.2 7.2 11.3 17.1 11.3 27.3s-4.1 20.1-11.3 27.3L377.9 406.1c-6.4 6.4-15 9.9-24 9.9c-18.7 0-33.9-15.2-33.9-33.9l0-62.1-128 0c-17.7 0-32-14.3-32-32l0-64c0-17.7 14.3-32 32-32l128 0 0-62.1c0-18.7 15.2-33.9 33.9-33.9c9 0 17.6 3.6 24 9.9zM160 96L96 96c-17.7 0-32 14.3-32 32l0 256c0 17.7 14.3 32 32 32l64 0c17.7 0 32 14.3 32 32s-14.3 32-32 32l-64 0c-53 0-96-43-96-96L0 128C0 75 43 32 96 32l64 0c17.7 0 32 14.3 32 32s-14.3 32-32 32z"></path></svg></div>

            <div class="text">Sair!!</div>
        </button>
    </a>
</div>

<script src="https://cdn.jsdelivr.net/npm/vue@3/dist/vue.global.prod.js"></script>
<script src="https://cdn.jsdelivr.net/npm/quasar@2.14.3/dist/quasar.umd.prod.js"></script>
<script>
    const { createApp } = Vue
    const app = createApp({})
    app.use(Quasar)
    app.mount('#app')
</script>
</body>
</html>
