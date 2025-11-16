<?php
// Arquivo: includes/delete_usuario.php
// Lida com a exclusão de professores ou alunos via AJAX.

require_once 'config.php'; 
header('Content-Type: application/json');

// O JS deve enviar o ID e o TIPO (professor ou aluno)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'], $_POST['tipo'])) {
    
    // Sanitiza e obtém os dados
    $id = (int)$_POST['id'];
    $tipo = strtolower(trim($_POST['tipo'])); // 'professor' ou 'aluno'

    // Define a tabela e o campo ID com base no tipo
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
    
    // Verifica se o ID é válido
    if ($id <= 0) {
        echo json_encode(['success' => false, 'error' => 'ID inválido']);
        mysqli_close($conexao);
        exit;
    }
    
    // Query de Exclusão
    $sql = "DELETE FROM $tabela WHERE $campo_id = ?";
    if ($stmt = mysqli_prepare($conexao, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        
        // Verifica se alguma linha foi afetada
        if (mysqli_stmt_affected_rows($stmt) > 0) {
            echo json_encode(['success' => true, 'message' => ucfirst($tipo) . ' excluído com sucesso.']);
        } else {
            echo json_encode(['success' => false, 'error' => ucfirst($tipo) . ' não encontrado para o ID fornecido.']);
        }
        
        mysqli_stmt_close($stmt);
    } else {
        echo json_encode([
            'success' => false,
            'error' => 'Erro ao excluir do banco de dados: ' . mysqli_error($conexao)
        ]);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Requisição inválida: ID ou tipo ausente.']);
}

mysqli_close($conexao);
?>