<?php
require_once '../includes/db.php';
session_start();

// Verificar se o admin está logado
if (!isset($_SESSION['admin_id'])) {
    header('Location: ../index.php');
    exit;
}

// Verificar se veio o código da seção via GET
if (!isset($_GET['codigo'])) {
    die('Erro: Código da seção não fornecido.');
}

$codigo_secao = $_GET['codigo'];

// Buscar o ID da seção correspondente ao código
$stmt = $pdo->prepare("SELECT id FROM secoes WHERE codigo = ?");
$stmt->execute([$codigo_secao]);
$secao = $stmt->fetch();

if (!$secao) {
    die('Erro: Seção não encontrada.');
}

$id_secao = $secao['id'];

// Receber os dados JSON do frontend
$dados = json_decode(file_get_contents('php://input'), true);

// Verificar se o array 'cadeiras' foi enviado
if (!isset($dados['cadeiras']) || !is_array($dados['cadeiras'])) {
    die('Erro: Nenhuma cadeira recebida.');
}

$cadeiras = $dados['cadeiras'];

// Apagar todas as cadeiras da seção antes de inserir novas
$pdo->prepare("DELETE FROM cadeiras WHERE id_secao = ?")->execute([$id_secao]);

// Inserir as novas cadeiras
$stmt = $pdo->prepare("INSERT INTO cadeiras (id_secao, nome, linha, coluna, ocupado) VALUES (?, ?, ?, ?, ?)");

foreach ($cadeiras as $cadeira) {
    $nome = trim($cadeira['nome']);
    $linha = $cadeira['linha'];
    $coluna = $cadeira['coluna'];
    $ocupado = $cadeira['ocupado'];

    if (!empty($nome)) {
        $stmt->execute([$id_secao, $nome, $linha, $coluna, $ocupado]);
    }
}

// Retorno de sucesso (pode ser usado pelo fetch)
echo "Cadeiras salvas com sucesso.";
