<?php
// Arquivo: includes/processar_usuario.php
// Lida com a aprovação ou reprovação de alunos/professores via AJAX, com segurança e notificação.

session_start();
include 'config.php';
header('Content-Type: application/json');

// --- 1. Verificação de método ---
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Método de requisição inválido.']);
    exit;
}

// --- 2. Validação dos dados ---
$id = isset($_POST['id']) ? (int)$_POST['id'] : null;
$tipo = isset($_POST['tipo']) ? strtolower(trim($_POST['tipo'])) : null;
$acao = isset($_POST['acao']) ? strtolower(trim($_POST['acao'])) : null;

if (!$id || !in_array($tipo, ['aluno','professor']) || !in_array($acao, ['aprovar','reprovar'])) {
    echo json_encode(['success' => false, 'message' => 'Dados incompletos ou inválidos.']);
    exit;
}

$tabela = ($tipo === 'aluno') ? 'aluno' : 'professor';
$campo_id = ($tipo === 'aluno') ? 'id_aluno' : 'id_prof';

// --- 3. Aprovar ou reprovar ---
if ($acao === 'aprovar') {
    $novo_status = 'APROVADO';
    $stmt = mysqli_prepare($conexao, "UPDATE $tabela SET status_aprovacao = ? WHERE $campo_id = ? AND status_aprovacao = 'PENDENTE'");
    if (!$stmt) {
        echo json_encode(['success' => false, 'message' => 'Erro interno: ' . mysqli_error($conexao)]);
        exit;
    }
    mysqli_stmt_bind_param($stmt, "si", $novo_status, $id);
    $ok = mysqli_stmt_execute($stmt);
    $rows = mysqli_stmt_affected_rows($stmt);
    mysqli_stmt_close($stmt);

    if ($ok && $rows > 0) {
        // --- 4. Registrar notificação ---
        $mensagem_notificacao = "Seu cadastro no PlanIt foi aprovado! Clique aqui para acessar o sistema.";
        $url_login = "http://localhost/proj_site/login.php";
        $stmt2 = mysqli_prepare($conexao, "INSERT INTO notificacoes (tipo_usuario, id_usuario, mensagem, url_destino) VALUES (?, ?, ?, ?)");
        if ($stmt2) {
            mysqli_stmt_bind_param($stmt2, "siss", $tipo, $id, $mensagem_notificacao, $url_login);
            mysqli_stmt_execute($stmt2);
            mysqli_stmt_close($stmt2);
        }
        echo json_encode(['success' => true, 'message' => ucfirst($tipo) . ' aprovado com sucesso!', 'acao' => $acao, 'tipo' => $tipo]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Nenhuma alteração feita. Usuário já aprovado ou ID inválida.']);
    }
} else if ($acao === 'reprovar') {
    $stmt = mysqli_prepare($conexao, "DELETE FROM $tabela WHERE $campo_id = ?");
    if (!$stmt) {
        echo json_encode(['success' => false, 'message' => 'Erro interno: ' . mysqli_error($conexao)]);
        exit;
    }
    mysqli_stmt_bind_param($stmt, "i", $id);
    $ok = mysqli_stmt_execute($stmt);
    $rows = mysqli_stmt_affected_rows($stmt);
    mysqli_stmt_close($stmt);
    if ($ok && $rows > 0) {
        echo json_encode(['success' => true, 'message' => ucfirst($tipo) . ' reprovado e removido com sucesso.', 'acao' => $acao, 'tipo' => $tipo]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erro ao reprovar/remover ou ID inválida.']);
    }
}

mysqli_close($conexao);
?>