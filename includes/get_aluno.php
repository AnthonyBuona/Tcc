<?php
// Arquivo: includes/get_aluno.php
// Busca os dados de um aluno específico para o modal de edição.

session_start();
include 'config.php'; 
header('Content-Type: application/json');

$response = ['success' => false, 'aluno' => null, 'error' => ''];

// O ID é recebido via GET na URL
$id_aluno = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id_aluno) {
    $response['error'] = "ID do aluno inválido.";
    echo json_encode($response);
    exit;
}

// Adapte a sua query SQL se a sua tabela ou colunas tiverem nomes diferentes.
// Você pode precisar de um JOIN se as séries e turmas forem listadas por nome no futuro.
$sql = "SELECT id_aluno, nome, cpf, id_serie, id_turma 
        FROM aluno 
        WHERE id_aluno = ?";

if ($stmt = mysqli_prepare($conexao, $sql)) {
    mysqli_stmt_bind_param($stmt, "i", $id_aluno);
    
    if (mysqli_stmt_execute($stmt)) {
        $result = mysqli_stmt_get_result($stmt);
        
        if ($aluno = mysqli_fetch_assoc($result)) {
            $response['success'] = true;
            $response['aluno'] = $aluno;
        } else {
            $response['error'] = "Aluno não encontrado.";
        }
    } else {
        $response['error'] = "Erro ao executar a busca: " . mysqli_error($conexao);
    }
    mysqli_stmt_close($stmt);
} else {
    $response['error'] = "Erro na preparação da query: " . mysqli_error($conexao);
}

echo json_encode($response);
?>