<?php
// Arquivo: includes/processar_restricoes.php
session_start();
include 'config.php';
header('Content-Type: application/json');

$response = ['success' => false, 'message' => 'Ação inválida.'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao'])) {
    
    // --- LÓGICA 1: SALVAR RESTRIÇÕES (CHECKBOXES) ---
    if ($_POST['acao'] === 'salvar_restricoes') {
        $id_prof = (int)$_POST['id_prof_selecionado'];
        $restricoes = isset($_POST['restricoes']) ? $_POST['restricoes'] : [];
        
        if ($id_prof > 0) {
            // 1. Limpar restrições antigas
            $stmt_del = $conexao->prepare("DELETE FROM disponibilidade_prof WHERE id_prof = ?");
            $stmt_del->bind_param("i", $id_prof);
            
            if ($stmt_del->execute()) {
                $stmt_del->close();
                
                // 2. Inserir novas (se houver)
                if (!empty($restricoes)) {
                    $stmt_ins = $conexao->prepare("INSERT INTO disponibilidade_prof (id_prof, dia, horario) VALUES (?, ?, ?)");
                    $erro_insert = false;
                    
                    foreach ($restricoes as $res) {
                        list($dia, $horario) = explode('_', $res, 2);
                        $stmt_ins->bind_param("iss", $id_prof, $dia, $horario);
                        if (!$stmt_ins->execute()) $erro_insert = true;
                    }
                    $stmt_ins->close();
                    
                    if ($erro_insert) {
                        $response = ['success' => false, 'message' => 'Algumas restrições não puderam ser salvas.'];
                    } else {
                        $response = ['success' => true, 'message' => 'Restrições de horário atualizadas com sucesso!'];
                    }
                } else {
                    $response = ['success' => true, 'message' => 'Todas as restrições foram removidas com sucesso.'];
                }
            } else {
                $response = ['success' => false, 'message' => 'Erro ao limpar restrições anteriores: ' . $conexao->error];
            }
        } else {
            $response = ['success' => false, 'message' => 'Professor inválido.'];
        }
    }
    
    // --- LÓGICA 2: SALVAR PERFIL (OUTRO TRABALHO) ---
    elseif ($_POST['acao'] === 'salvar_perfil') {
        $id_prof = (int)$_POST['id_prof_selecionado_perfil'];
        $trabalha = isset($_POST['trabalha_outro_lugar']) ? (int)$_POST['trabalha_outro_lugar'] : 0;
        $horario_saida = trim($_POST['horario_saida_outro_lugar']);
        
        // Se não trabalha, o horário deve ser NULL no banco
        $horario_db = ($trabalha == 1 && !empty($horario_saida)) ? $horario_saida : NULL;
        
        if ($id_prof > 0) {
            $stmt = $conexao->prepare("UPDATE professor SET trabalha_outro_lugar = ?, horario_saida_outro_lugar = ? WHERE id_prof = ?");
            $stmt->bind_param("isi", $trabalha, $horario_db, $id_prof);
            
            if ($stmt->execute()) {
                $response = ['success' => true, 'message' => 'Perfil do professor atualizado com sucesso.'];
            } else {
                $response = ['success' => false, 'message' => 'Erro ao atualizar perfil: ' . $stmt->error];
            }
            $stmt->close();
        } else {
            $response = ['success' => false, 'message' => 'Professor inválido.'];
        }
    }
}

echo json_encode($response);
exit;
?>