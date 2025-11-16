<?php
// Arquivo: includes/update_aluno.php
// Processa o UPDATE dos dados do aluno no banco de dados.

session_start();
include 'config.php'; 
header('Content-Type: application/json');

$response = ['success' => false, 'error' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Coleta e validação de dados do formulário
    $id_aluno = filter_input(INPUT_POST, 'id_aluno', FILTER_VALIDATE_INT);
    $nome = filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_STRING);
    // CPF é coletado/enviado, mas será ignorado no UPDATE
    $id_serie = filter_input(INPUT_POST, 'id_serie', FILTER_VALIDATE_INT);
    $id_turma = filter_input(INPUT_POST, 'id_turma', FILTER_VALIDATE_INT);

    // Validação básica
    if (!$id_aluno || empty($nome) || $id_serie === null || $id_turma === null) {
        $response['error'] = "Todos os campos obrigatórios (Nome, Série, Turma) devem ser preenchidos.";
        echo json_encode($response);
        exit;
    }

    // 2. Preparação e execução da query de UPDATE
    // NOTA: O CPF foi removido da query de UPDATE.
    $sql = "UPDATE aluno SET nome = ?, id_serie = ?, id_turma = ? WHERE id_aluno = ?";
    
    if ($stmt = mysqli_prepare($conexao, $sql)) {
        // Tipos: 's' para string (nome), 'i' para inteiros (id_serie, id_turma, id_aluno)
        mysqli_stmt_bind_param($stmt, "siii", $nome, $id_serie, $id_turma, $id_aluno);
        
        if (mysqli_stmt_execute($stmt)) {
            // Verifica se a linha foi afetada para dar um feedback mais preciso
            if (mysqli_stmt_affected_rows($stmt) > 0) {
                $response['success'] = true;
            } else {
                $response['success'] = true; // Se não houver alteração, ainda é um sucesso
                $response['message'] = "Nenhuma alteração detectada. Aluno não modificado.";
            }
        } else {
            $response['error'] = "Erro ao executar a atualização: " . mysqli_error($conexao);
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