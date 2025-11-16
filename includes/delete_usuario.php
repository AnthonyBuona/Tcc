<?php
// Arquivo: includes/delete_usuario.php
// Lida com a exclusão de professores ou alunos via AJAX.
require_once 'config.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'], $_POST['tipo'])) {
    $id = (int)$_POST['id'];
    $tipo = strtolower(trim($_POST['tipo']));
    $tabela = '';
    $campo_id = '';
    if ($tipo === 'professor') {
        $tabela = 'professor';
        $campo_id = 'id_prof';
    } elseif ($tipo === 'aluno') {
        $tabela = 'aluno';
        $campo_id = 'id_aluno';
    } else {
        echo json_encode(['success' => false, 'error' => 'Tipo de usuário inválido para exclusão.']);
        mysqli_close($conexao);
        exit;
    }
    if ($id <= 0) {
        echo json_encode(['success' => false, 'error' => 'ID inválido']);
        mysqli_close($conexao);
        exit;
    }
    $sql = "DELETE FROM $tabela WHERE $campo_id = ?";
    if ($stmt = mysqli_prepare($conexao, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        if (mysqli_stmt_affected_rows($stmt) > 0) {
            echo json_encode(['success' => true, 'message' => ucfirst($tipo) . ' excluído com sucesso.']);
        } else {
            echo json_encode(['success' => false, 'error' => ucfirst($tipo) . ' não encontrado para o ID fornecido.']);
        }
        mysqli_stmt_close($stmt);
    } else {
        echo json_encode(['success' => false, 'error' => 'Erro ao excluir do banco de dados: ' . mysqli_error($conexao)]);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Requisição inválida: ID ou tipo ausente.']);
}

mysqli_close($conexao);
?>