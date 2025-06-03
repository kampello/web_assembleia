
<?php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../index.php");
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM secoes WHERE criador_id = ? ORDER BY data_inicio DESC");
$stmt->execute([$_SESSION['admin_id']]);
$secoes = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Painel do Administrador</title>
    <link href="https://cdn.jsdelivr.net/npm/quasar@2.14.3/dist/quasar.prod.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/btn.css">
</head>
<body class="bg-grey-1">

<div id="app">
    <q-layout view="hHh lpR fFf">
        <!-- Cabeçalho -->
        <q-header class="bg-primary text-white">
            <q-toolbar>
                <q-toolbar-title>Painel do Administrador</q-toolbar-title>
                <q-btn
                        @click="irParaCriar"
                        class="q-ml-sm"
                        label="Criar seção"
                />

            </q-toolbar>

        </q-header>

        <q-page-container>
            <div> <!-- Barra de pesquisa -->
                <q-input
                        filled
                        v-model="termoPesquisa"
                        label="Pesquisar seções"

                        debounce="300"
                        clearable
                        :prefix="`${secoesFiltradas.length} encontradas`"
                /></div>
            <q-page class="q-pa-md" style="max-width: 1000px; margin-left: 0;">



                <!-- Seções existentes -->
                <template v-if="secoesFiltradas.length">
                    <div class="text-h6 q-mb-md">Seções existentes</div>

                    <q-card
                            v-for="secao in secoesFiltradas"
                            :key="secao.codigo"
                            class="q-mb-lg"
                    >
                        <q-card-section class="q-pa-sm">
                            <div class="text-subtitle1">
                                <strong>{{ secao.titulo }}</strong>
                                <span class="text-grey">({{ secao.codigo }})</span>
                            </div>
                        </q-card-section>

                        <q-card-section class="q-pa-sm" v-html="formatDescricao(secao.descricao)"></q-card-section>

                        <div class="q-pa-sm">
                            <a href="#" class="lista_Admin" @click="navegar('gerir_cadeiras.php?codigo=' + encodeURIComponent(secao.codigo))">🪑 Gerir cadeiras</a>
                            <a href="#" class="lista_Admin" @click="navegar('criar_votacao.php?codigo=' + encodeURIComponent(secao.codigo))">🗳️ Adicionar votos</a>
                            <a href="#" class="lista_Admin" @click="navegar('../processa/eliminar_votacao.php?codigo=' + encodeURIComponent(secao.codigo))">🚮 Eliminar</a>
                            <a href="#" class="lista_Admin" @click="navegar('criar_qr.php?codigo=' + encodeURIComponent(secao.codigo))">🤳 entrar com Qr</a>
                            <a href="#" class="lista_Admin" @click="navegar('bigpicture.php?codigo=' + encodeURIComponent(secao.codigo) + '&id=1')">📱 BigPicture</a>


                        </div>


                        <q-card-section class="q-pa-sm">
                            <div class="text-caption text-grey">
                                {{ formatData(secao.data_inicio) }} →
                                {{ formatData(secao.data_fim) }}
                            </div>
                        </q-card-section>

                        <q-card-actions align="left" class="q-gutter-sm q-pa-md"></q-card-actions>
                        <q-separator inset />
                    </q-card>
                </template>

                <!-- Nenhuma seção -->
                <q-card v-else class="q-pa-lg text-center">
                    <q-card-section>
                        <div class="text-h6">Nenhuma seção encontrada.</div>
                        <q-btn
                                unelevated
                                color="primary"
                                class="q-mt-md"
                                label="Criar nova seção"
                                @click="irParaCriar"
                        />
                    </q-card-section>
                </q-card>

            </q-page>
        </q-page-container>
    </q-layout>
</div>
<a href="../index.php">
    <button class="Btn">

        <div class="sign">
            <svg viewBox="0 0 512 512">
                <path d="M377.9 105.9L500.7 228.7c7.2 7.2 11.3 17.1 11.3 27.3s-4.1 20.1-11.3 27.3L377.9 406.1c-6.4 6.4-15 9.9-24 9.9c-18.7 0-33.9-15.2-33.9-33.9l0-62.1-128 0c-17.7 0-32-14.3-32-32l0-64c0-17.7 14.3-32 32-32l128 0 0-62.1c0-18.7 15.2-33.9 33.9-33.9c9 0 17.6 3.6 24 9.9zM160 96L96 96c-17.7 0-32 14.3-32 32l0 256c0 17.7 14.3 32 32 32l64 0c17.7 0 32 14.3 32 32s-14.3 32-32 32l-64 0c-53 0-96-43-96-96L0 128C0 75 43 32 96 32l64 0c17.7 0 32 14.3 32 32s-14.3 32-32 32z"></path></svg></div>

        <div class="text">Logout</div>
    </button>
</a>
<script src="https://cdn.jsdelivr.net/npm/vue@3/dist/vue.global.prod.js"></script>
<script src="https://cdn.jsdelivr.net/npm/quasar@2.14.3/dist/quasar.umd.prod.js"></script>
<script>
    const { createApp } = Vue

    createApp({
        data() {
            return {
                termoPesquisa: '',
                secoes: <?= json_encode($secoes) ?>
            }
        },
        computed: {
            secoesFiltradas() {
                if (!this.termoPesquisa) return this.secoes;
                const termo = this.termoPesquisa.toLowerCase();
                return this.secoes.filter(secao =>
                    secao.titulo.toLowerCase().includes(termo) ||
                    secao.codigo.toLowerCase().includes(termo) ||
                    (secao.descricao || '').toLowerCase().includes(termo)
                );
            }
        },
        methods: {
            formatData(data) {
                const d = new Date(data)
                return d.toLocaleDateString('pt-PT') + ' ' +
                    d.toLocaleTimeString('pt-PT', { hour: '2-digit', minute: '2-digit' })
            },
            formatDescricao(desc) {
                return desc?.replace(/\n/g, '<br>') ?? ''
            },
            irParaCriar() {
                window.location.href = 'criar_secao.php'
            },
            navegar(url) {
                window.location.href = url
            }
        }
    })
        .use(Quasar)
        .mount('#app')
</script>

</body>
</html>
