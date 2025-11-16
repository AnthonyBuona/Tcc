<?php
// ====================================================================
// --- CONFIGURAÇÃO DE RETORNO JSON (CRÍTICO PARA AJAX) ---
header('Content-Type: application/json');
// ====================================================================

// --- CONFIGURAÇÃO DE DEPURACÃO ---
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// ====================================================================

session_start();
// Assume-se que 'includes/config.php' define a variável $conexao
include 'config.php';
// Array de resposta padrão 
$response = [
    'status' => 'error',
    'msg' => 'Erro desconhecido ao iniciar o processamento.',
    'total_inseridos' => 0,
    'turmas_ignoradas' => []
];

// 1. --- VERIFICAÇÃO CRÍTICA DA CONEXÃO E MÉTODO ---
if (!isset($conexao) || $conexao === false || mysqli_connect_errno()) {
    $response['msg'] = "Erro Crítico: Falha na Conexão com o Banco de Dados.";
    echo json_encode($response);
    exit();
}

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    $response['msg'] = "Método de requisição inválido. Esperado POST.";
    echo json_encode($response);
    exit();
}

// 2. Coleta e valida os dados principais
$id_disc = filter_input(INPUT_POST, 'id_disc', FILTER_VALIDATE_INT);
$id_prof_padrao = filter_input(INPUT_POST, 'id_prof_padrao', FILTER_VALIDATE_INT);
$turmas = $_POST['turmas'] ?? []; 

if (!$id_disc || !$id_prof_padrao) {
    $response['msg'] = "Dados essenciais (Disciplina ou Professor) estão faltando ou são inválidos.";
    echo json_encode($response);
    exit();
}

// ----------------------------------------------------
// 3. INÍCIO DA TRANSAÇÃO
// ----------------------------------------------------
mysqli_begin_transaction($conexao);

try {
    // Exclui todos os relacionamentos existentes para esta disciplina
    $sql_delete = "DELETE FROM relacionamento_disciplina WHERE id_disc = ?";
    $stmt_delete = mysqli_prepare($conexao, $sql_delete);
    mysqli_stmt_bind_param($stmt_delete, "i", $id_disc);
    
    if (!mysqli_stmt_execute($stmt_delete)) {
        throw new Exception("Erro ao limpar relacionamentos antigos: " . mysqli_error($conexao));
    }
    mysqli_stmt_close($stmt_delete);

    $total_inseridos = 0;
    $turmas_ignoradas = [];

    // 4. Itera e insere os novos relacionamentos
    foreach ($turmas as $id_turma => $turma_data) {
        
        if (isset($turma_data['incluir']) && $turma_data['incluir'] == 1) {
            
            $id_turma_valido = filter_var($id_turma, FILTER_VALIDATE_INT); 
            $aulas_semanais = filter_var($turma_data['aulas_semanais'], FILTER_VALIDATE_INT);
            $carga_horaria = filter_var($turma_data['carga_horaria'], FILTER_VALIDATE_INT);
            
            if (!$id_turma_valido || $aulas_semanais <= 0 || $carga_horaria <= 0) {
                $turmas_ignoradas[] = "ID " . $id_turma_valido;
                continue; 
            }

            // 5. Query de Inserção
            $sql_insert = "INSERT INTO relacionamento_disciplina (
                                 id_disc, 
                                 id_turma, 
                                 id_prof_padrao, 
                                 aulas_semanais, 
                                 carga_horaria_total
                            ) VALUES (?, ?, ?, ?, ?)";
            
            $stmt_insert = mysqli_prepare($conexao, $sql_insert);
            
            if (!$stmt_insert) {
                 throw new Exception("Erro de preparação da query: " . mysqli_error($conexao));
            }
            
            mysqli_stmt_bind_param($stmt_insert, "iiiii", 
                $id_disc, 
                $id_turma_valido, 
                $id_prof_padrao, 
                $aulas_semanais, 
                $carga_horaria
            );

            if (!mysqli_stmt_execute($stmt_insert)) {
                throw new Exception("Erro ao inserir relacionamento da Turma " . $id_turma_valido . ": " . mysqli_error($conexao));
            }
            
            mysqli_stmt_close($stmt_insert);
            $total_inseridos++;
        }
    }

    // 6. Finaliza a transação e prepara a resposta JSON
    mysqli_commit($conexao);
    
    $response['status'] = 'success';
    $response['total_inseridos'] = $total_inseridos;
    $response['turmas_ignoradas'] = $turmas_ignoradas;
    $response['msg'] = "Relacionamento e cargas horárias salvos com sucesso. Total de **$total_inseridos turmas** atualizadas.";
    
    if (!empty($turmas_ignoradas)) {
        $response['status'] = 'warning';
        $response['msg'] .= " **AVISO:** As seguintes turmas foram selecionadas, mas **ignoradas** por terem cargas horárias inválidas: " . implode(', ', $turmas_ignoradas) . ".";
    }

} catch (Exception $e) {
    // 7. Em caso de erro, desfaz a transação (ROLLBACK)
    mysqli_rollback($conexao);
    $response['status'] = 'error';
    $response['msg'] = "Erro Crítico ao salvar. Nenhum dado foi alterado. Detalhe: " . $e->getMessage();
}

// 8. Envia a resposta JSON
mysqli_close($conexao);
echo json_encode($response);
exit();
?>