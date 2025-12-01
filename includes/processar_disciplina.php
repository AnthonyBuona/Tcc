<?php
// Arquivo: includes/processar_disciplina.php
session_start();
include 'config.php';
header('Content-Type: application/json');

$response = ['success' => false, 'message' => 'Ação inválida.'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao'])) {
    
    // --- EXCLUIR DISCIPLINA ---
    if ($_POST['acao'] === 'excluir') {
        $id = (int)$_POST['id'];
        
        if ($id > 0) {
            // Verifica se a disciplina está em uso (opcional, mas recomendado)
            // Aqui tentamos excluir direto. Se houver FK (chave estrangeira), o banco retornará erro.
            $stmt = $conexao->prepare("DELETE FROM disciplina WHERE id_disc = ?");
            $stmt->bind_param("i", $id);
            
            if ($stmt->execute()) {
                $response = ['success' => true, 'message' => 'Disciplina excluída com sucesso.'];
            } else {
                // Captura erro de chave estrangeira (ex: disciplina usada em turmas)
                if ($conexao->errno == 1451) {
                    $response = ['success' => false, 'message' => 'Não é possível excluir: Esta disciplina está vinculada a turmas ou horários.'];
                } else {
                    $response = ['success' => false, 'message' => 'Erro ao excluir: ' . $stmt->error];
                }
            }
            $stmt->close();
        }
    }
    
    // --- EDITAR DISCIPLINA ---
    elseif ($_POST['acao'] === 'editar') {
        $id = (int)$_POST['id_disc'];
        $nome = trim($_POST['nome_disc']);
        $area = trim($_POST['area']);
        
        if ($id > 0 && !empty($nome) && !empty($area)) {
            $stmt = $conexao->prepare("UPDATE disciplina SET nome_disc = ?, area = ? WHERE id_disc = ?");
            $stmt->bind_param("ssi", $nome, $area, $id);
            
            if ($stmt->execute()) {
                $response = ['success' => true, 'message' => 'Disciplina atualizada com sucesso.'];
            } else {
                $response = ['success' => false, 'message' => 'Erro ao atualizar: ' . $stmt->error];
            }
            $stmt->close();
        } else {
            $response = ['success' => false, 'message' => 'Preencha todos os campos obrigatórios.'];
        }
    }
}

echo json_encode($response);
exit;
?>