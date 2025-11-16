<?php
// Arquivo: includes/processar_acao_crud.php
// Lida com DELETE (Excluir) genérico para Aluno e Professor.

session_start();
// Garanta que este arquivo (config.php) contém a sua variável $conexao
include 'config.php'; 
header('Content-Type: application/json');

$response = ['success' => false, 'error' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Coleta e validação de dados
    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
    $tipo = filter_input(INPUT_POST, 'tipo', FILTER_SANITIZE_STRING);
    $acao = filter_input(INPUT_POST, 'acao', FILTER_SANITIZE_STRING);

    if (!$id || !in_array($tipo, ['aluno', 'professor']) || $acao !== 'excluir') {
        $response['error'] = "Dados inválidos fornecidos ou ação não suportada.";
        echo json_encode($response);
        exit;
    }

    // 2. Definição de tabela e coluna de ID
    if ($tipo === 'aluno') {
        $tabela = 'aluno';
        $coluna_id = 'id_aluno';
    } else {
        $tabela = 'professor';
        $coluna_id = 'id_prof';
    }

    // 3. Preparação e execução da query
    $sql = "DELETE FROM $tabela WHERE $coluna_id = ?";
    
    if ($stmt = mysqli_prepare($conexao, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $id);
        
        if (mysqli_stmt_execute($stmt)) {
            $response['success'] = true;
            $response['message'] = strtoupper($tipo) . " excluído(a) com sucesso.";
        } else {
            $response['error'] = "Erro ao executar a exclusão: " . mysqli_error($conexao);
        }
        mysqli_stmt_close($stmt);
    } else {
        $response['error'] = "Erro na preparação da query: " . mysqli_error($conexao);
    }
} else {
    $response['error'] = "Método de requisição inválido.";
}

echo json_encode($response);
?>