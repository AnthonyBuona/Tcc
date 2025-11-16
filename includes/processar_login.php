<?php
// Certifique-se de que o arquivo config.php está correto e inicia a conexão $conexao
include 'config.php'; 
// Inicia a sessão para armazenar dados do usuário logado
session_start();
header('Content-Type: application/json');

$cpf = $_POST['cpf'] ?? '';
$senha = $_POST['senha'] ?? '';

// Verifica a conexão
if (!isset($conexao) || !is_object($conexao) || !@mysqli_ping($conexao)) {
    echo json_encode(['status' => 'erro', 'mensagem' => 'Erro interno: Conexão com o banco de dados falhou.']);
    exit;
}

// Limpeza
$cpf_limpo = preg_replace('/[^0-9]/', '', $cpf);

$usuario = null;
$tipo_usuario = null;

// ----------------------------------------------------
// BUSCA USUÁRIO
// ----------------------------------------------------
// Tenta buscar na tabela 'aluno'
$sql_aluno = "SELECT id_aluno AS id, nome, status_aprovacao, senha FROM aluno WHERE cpf = ?";
$stmt_aluno = mysqli_prepare($conexao, $sql_aluno);
mysqli_stmt_bind_param($stmt_aluno, "s", $cpf_limpo);
mysqli_stmt_execute($stmt_aluno);
$result_aluno = mysqli_stmt_get_result($stmt_aluno);

if ($row = mysqli_fetch_assoc($result_aluno)) {
    $usuario = $row;
    $tipo_usuario = 'aluno';
} else {
    // Se não encontrou aluno, tenta buscar na tabela 'professor'
    $sql_prof = "SELECT id_prof AS id, nome, status_aprovacao, senha FROM professor WHERE cpf = ?";
    $stmt_prof = mysqli_prepare($conexao, $sql_prof);
    mysqli_stmt_bind_param($stmt_prof, "s", $cpf_limpo);
    mysqli_stmt_execute($stmt_prof);
    $result_prof = mysqli_stmt_get_result($stmt_prof);

    if ($row = mysqli_fetch_assoc($result_prof)) {
        $usuario = $row;
        $tipo_usuario = 'professor';
    }
}

// ----------------------------------------------------
// VERIFICAÇÃO FINAL
// ----------------------------------------------------
if ($usuario) {
    // 1. Verifica a senha
    if (!password_verify($senha, $usuario['senha'])) {
        echo json_encode(['status' => 'erro', 'mensagem' => 'CPF ou senha incorretos.']);
        exit;
    }

    // 2. Verifica status de aprovação (se está PENDENTE)
    if ($usuario['status_aprovacao'] === 'PENDENTE') {
        echo json_encode([
            'status' => 'aguardando', 
            'mensagem' => 'Seu cadastro está aguardando aprovação do administrador.'
        ]);
        exit;
    }
    
    // 3. Se estiver APROVADO, inicia a sessão
    if ($usuario['status_aprovacao'] === 'APROVADO') {
        
        // Padroniza sessão
        $_SESSION['usuario'] = [
            'id' => $usuario['id'],
            'nome' => $usuario['nome'],
            'tipo' => $tipo_usuario
        ];
        
        // ===============================================
        // CHECAR POR NOTIFICAÇÕES NÃO LIDAS
        // ===============================================
        $sql_notif = "SELECT COUNT(*) as total FROM notificacoes 
                      WHERE id_usuario = ? AND tipo_usuario = ? AND lida = 0";
        
        $stmt_notif = mysqli_prepare($conexao, $sql_notif);
        mysqli_stmt_bind_param($stmt_notif, "is", $usuario['id'], $tipo_usuario);
        mysqli_stmt_execute($stmt_notif);
        $result_notif = mysqli_stmt_get_result($stmt_notif);
        $total_notif = mysqli_fetch_assoc($result_notif)['total'];
        
        $redirecionar_para = ($tipo_usuario === 'aluno') ? 'aluno.php' : 'professor.php';

        // Retorna sucesso para o JavaScript, incluindo a contagem de notificação
        echo json_encode([
            'status' => 'sucesso',
            'notificacoes' => (int)$total_notif, 
            'redirecionar' => $redirecionar_para
        ]);
        exit;
    }

} else {
    // Usuário não encontrado em nenhuma tabela
    echo json_encode(['status' => 'erro', 'mensagem' => 'CPF ou senha incorretos.']);
    exit;
}

// ----------------------------------------------------
// LOGIN ADMINISTRADOR (fixo)
// ----------------------------------------------------
if ($cpf_limpo === '00000000000' && $senha === 'admin123') {
    $_SESSION['usuario'] = [
        'id' => 0,
        'nome' => 'Administrador',
        'tipo' => 'admin'
    ];
    echo json_encode([
        'status' => 'sucesso',
        'redirecionar' => 'dashboard_adm.php'
    ]);
    exit;
}
?>