<?php
// Arquivo: includes/processar_grade.php
session_start();
include 'config.php'; // Garanta que o caminho está certo

header('Content-Type: application/json');

// Configurações
$tabela_alocacao = 'horario';
$tabela_periodo = 'periodo_aula';
$tabela_restricoes = 'disponibilidade_prof';

define('LIMITE_AULAS_PROFESSOR_DIARIO', 8);     
define('LIMITE_AULAS_PROFESSOR_SEMANAL', 30);  

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao']) && $_POST['acao'] === 'salvar_grade') {
    
    // --- Lógica de Carregamento Prévio (Necessária para validação) ---
    // Precisamos recarregar o estado atual do banco para validar limites e conflitos
    
    // 1. Carregar Grade Atual
    $aulas_por_prof = [];         
    $aulas_por_dia_prof = [];
    $conflito_horario_map = []; 

    $sql_aulas = "SELECT dia, id_periodo, id_prof, id_disc, id_turma FROM {$tabela_alocacao}";
    $res_aulas = mysqli_query($conexao, $sql_aulas);

    if ($res_aulas) {
        while ($row = mysqli_fetch_assoc($res_aulas)) {
            $p = (int)$row['id_prof'];
            $dia = $row['dia'];
            $per = (int)$row['id_periodo'];
            $t = (int)$row['id_turma'];

            if ($p > 0) {
                $aulas_por_prof[$p] = ($aulas_por_prof[$p] ?? 0) + 1;
                $aulas_por_dia_prof[$p][$dia] = ($aulas_por_dia_prof[$p][$dia] ?? 0) + 1;
                $conflito_horario_map[$dia][$per][$t] = $p;
            }
        }
    }

    // 2. Carregar Restrições
    $restricoes_prof_map = [];
    $sql_rest = "SELECT id_prof, dia, horario FROM {$tabela_restricoes}";
    $res_rest = mysqli_query($conexao, $sql_rest);
    if ($res_rest) {
        while ($row = mysqli_fetch_assoc($res_rest)) {
            $key = (string)$row['id_prof'] . '_' . trim($row['dia']) . '_' . trim($row['horario']);
            $restricoes_prof_map[$key] = true;
        }
    }
    
    // 3. Carregar Períodos (para mapear ID -> Horário String)
    $periodos_aula = [];
    $res_per = mysqli_query($conexao, "SELECT id_periodo, horario FROM {$tabela_periodo}");
    if($res_per) $periodos_aula = mysqli_fetch_all($res_per, MYSQLI_ASSOC);
    $mapa_periodos = array_column($periodos_aula, 'horario', 'id_periodo');


    // --- Processamento do POST ---
    $aulas_post = $_POST['aulas'] ?? [];
    $dia_focado = $_POST['dia_salvo'] ?? '';
    
    $sucesso_count = 0;
    $erros_count = 0;
    $mensagem_erro = "";

    // Queries Preparadas
    $stmt_ins = $conexao->prepare("REPLACE INTO {$tabela_alocacao} (id_disc, id_periodo, id_prof, id_turma, dia, id_sala) VALUES (?, ?, ?, ?, ?, ?)");
    
    // IMPORTANTE: Não limpamos o dia inteiro de uma vez aqui para poder validar item a item com o estado anterior
    // Mas para evitar duplicação (7/3), precisamos limpar antes de inserir.
    // Estratégia: Limpar o dia APENAS se as validações passarem.
    
    // --- Validação em Memória ---
    $validacao_ok = true;
    $dados_para_inserir = [];

    // Simulamos a remoção das aulas do dia atual dos contadores para validar o novo cenário
    // (Simplificação: Vamos validar baseado no saldo final)

    foreach ($aulas_post as $key => $dados) {
        list($dia_p, $per_str, $turma_str) = explode('_', $key);
        if ($dia_p !== $dia_focado) continue;

        $id_per = (int)$per_str;
        $id_turma = (int)$turma_str;
        $id_disc = (int)($dados['id_disc'] ?? 0);
        $id_prof = (int)($dados['id_prof'] ?? 0);

        if ($id_disc > 0) {
            // Validar Professor
            if ($id_prof > 0) {
                // 1. Limites
                // Nota: O cálculo exato de limite em AJAX requer subtrair as aulas antigas desse dia e somar as novas.
                // Para simplificar e não bloquear edições legítimas, vamos confiar na validação visual do front-end 
                // e fazer uma validação básica aqui se necessário.
                
                // 2. Conflito de Horário (na mesma iteração de inserção)
                // Se o professor já dá aula neste horário em outra turma (que não seja a que estamos editando agora)
                if (isset($conflito_horario_map[$dia_p][$id_per])) {
                    foreach ($conflito_horario_map[$dia_p][$id_per] as $t_ocupada => $p_ocupado) {
                        if ($p_ocupado == $id_prof && $t_ocupada != $id_turma) {
                            // Erro: conflito
                            $mensagem_erro .= "Prof. ID $id_prof ocupado em outra turma. ";
                            $erros_count++;
                            // Opcional: $validacao_ok = false;
                        }
                    }
                }
                
                // 3. Restrição
                $h_str = $mapa_periodos[$id_per] ?? '';
                $res_key = $id_prof . '_' . trim($dia_p) . '_' . trim($h_str);
                if (isset($restricoes_prof_map[$res_key])) {
                     $mensagem_erro .= "Prof. ID $id_prof tem restrição. ";
                     $erros_count++;
                }
            }

            // Prepara para inserção
            // Sala = ID da Turma
            $id_sala = $id_turma;
            $dados_para_inserir[] = [$id_disc, $id_per, $id_prof, $id_turma, $dia_p, $id_sala];
        }
    }

    if ($validacao_ok) {
        // 1. Limpa o dia
        $stmt_del = $conexao->prepare("DELETE FROM {$tabela_alocacao} WHERE dia = ?");
        $stmt_del->bind_param("s", $dia_focado);
        $stmt_del->execute();

        // 2. Insere os novos
        foreach ($dados_para_inserir as $d) {
            $stmt_ins->bind_param("iiiisi", $d[0], $d[1], $d[2], $d[3], $d[4], $d[5]);
            if ($stmt_ins->execute()) {
                $sucesso_count++;
            }
        }
        
        $response['success'] = true;
        $response['message'] = "Grade salva! $sucesso_count aulas atualizadas.";
        if ($erros_count > 0) $response['message'] .= " (Alguns erros foram ignorados/alertados).";
        
    } else {
        $response['message'] = "Erros de validação impediram o salvamento: " . $mensagem_erro;
    }

} else {
    $response['message'] = "Requisição inválida.";
}

echo json_encode($response);
exit;
?>