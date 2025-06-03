<?php
session_start();
$erro_login = $_SESSION['erro_login'] ?? null;
unset($_SESSION['erro_login']);
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Assembleia Virtual</title>
    <link href="https://cdn.jsdelivr.net/npm/quasar@2.14.3/dist/quasar.prod.css" rel="stylesheet">
</head>
<body class="q-pa-xl bg-grey-2">

<div id="app">
    <q-layout view="hHh lpR fFf">
        <q-header class="bg-primary text-white">
            <q-toolbar>
                <q-toolbar-title>Assembleia Virtual</q-toolbar-title>
            </q-toolbar>
        </q-header>

        <q-page-container>
            <q-page class="q-pa-md flex flex-center">

                <q-card class="q-pa-lg shadow-2" style="max-width: 500px; width: 100%;">
                    <q-card-section>
                        <div class="text-h6">
                            {{ modo === 'login' ? 'Entrar como Administrador' : 'Registrar novo Administrador' }}
                        </div>
                        <div v-if="erro" class="text-negative q-mt-sm">{{ erro }}</div>
                    </q-card-section>

                    <q-card-section>
                        <form :action="modo === 'login' ? 'processa/login.php' : 'processa/registar.php'" method="post">
                            <q-input
                                    label="Nome de utilizador"
                                    v-model="email"
                                    name="email"
                                    filled
                                    class="q-mb-md"
                                    required
                            ></q-input>

                            <q-input
                                    type="password"
                                    label="Senha"
                                    v-model="senha"
                                    name="senha"
                                    filled
                                    class="q-mb-md"
                                    required
                            ></q-input>

                            <q-btn
                                    type="submit"
                                    :label="modo === 'login' ? 'Entrar' : 'Registrar'"
                                    :color="modo === 'login' ? 'primary' : 'green'"
                                    unelevated
                                    class="full-width"
                            ></q-btn>
                        </form>

                        <q-btn
                                flat
                                class="q-mt-sm full-width"
                                :label="modo === 'login' ? 'Quero me registrar' : 'Já tenho conta'"
                                @click="alternarModo"
                        ></q-btn>

                        <div class="text-h6 q-mt-lg">Entrar com código</div>
                        <form action="guest/escolher_cadeira.php" method="get">
                            <q-btn type="submit" label="Entrar com código" color="secondary" unelevated class="full-width"></q-btn>
                        </form>
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

    createApp({
        data() {
            return {
                erro: <?php echo json_encode($erro_login ?? ''); ?>, // Corrigido para passar corretamente a variável PHP
                email: '',
                senha: '',
                modo: 'login' // ou 'register'
            }
        },
        methods: {
            alternarModo() {
                this.modo = this.modo === 'login' ? 'register' : 'login'
            }
        }
    })
        .use(Quasar)
        .mount('#app')
</script>

</body>
</html>
