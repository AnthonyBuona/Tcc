<?php
include 'config.php';
header('Content-Type: application/json');
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(['status'=>'erro','mensagem'=>'Formulário não enviado corretamente!']);
    exit;
}

$tipo = $_POST['tipo'] ?? '';
$nome = mysqli_real_escape_string($conexao, $_POST['nome'] ?? '');
$cpf  = mysqli_real_escape_string($conexao, $_POST['cpf'] ?? '');
$senha_raw = $_POST['senha'] ?? '';
$confirmar_senha = $_POST['confirmar_senha'] ?? '';

if ($senha_raw !== $confirmar_senha) {
    echo json_encode(['status'=>'erro','mensagem'=>'As senhas não coincidem!']);
    exit;
}

if (strlen($senha_raw) < 6) {
    echo json_encode(['status'=>'erro','mensagem'=>'A senha deve ter pelo menos 6 caracteres.']);
    exit;
}

$senha = password_hash($senha_raw, PASSWORD_DEFAULT);

if ($tipo == 'professor') {
    $areas = $_POST['areas'] ?? [];
    $areas_str = is_array($areas) ? implode(',', $areas) : $areas;

    if (empty($nome) || empty($cpf) || empty($senha) || empty($areas_str)) {
        echo json_encode(['status'=>'erro','mensagem'=>'Preencha todos os campos!']);
        exit;
    }

    $checkCpf = mysqli_query($conexao, "SELECT id FROM professor WHERE cpf='$cpf'");
    if (mysqli_num_rows($checkCpf) > 0) {
        echo json_encode(['status'=>'erro','mensagem'=>'Já existe um professor com esse CPF!']);
        exit;
    }

    // ALTERAÇÃO 1: Adiciona 'status_aprovacao' e o valor 'PENDENTE'
    $sql = "INSERT INTO professor (nome, cpf, senha, areas, status_aprovacao)
            VALUES ('$nome', '$cpf', '$senha', '$areas_str', 'PENDENTE')";

} elseif ($tipo == 'aluno') {
    $id_turma = $_POST['id_turma'] ?? '';

    if (empty($nome) || empty($cpf) || empty($senha) || empty($id_turma)) {
        echo json_encode(['status'=>'erro','mensagem'=>'Preencha todos os campos!']);
        exit;
    }

    $checkCpf = mysqli_query($conexao, "SELECT id_aluno FROM aluno WHERE cpf='$cpf'");
    if (mysqli_num_rows($checkCpf) > 0) {
        echo json_encode(['status'=>'erro','mensagem'=>'Já existe um aluno com esse CPF!']);
        exit;
    }

    // ALTERAÇÃO 2: Adiciona 'status_aprovacao' e o valor 'PENDENTE'
    $sql = "INSERT INTO aluno (nome, cpf, senha, id_turma, status_aprovacao)
            VALUES ('$nome', '$cpf', '$senha', '$id_turma', 'PENDENTE')";
} else {
    echo json_encode(['status'=>'erro','mensagem'=>'Tipo inválido!']);
    exit;
}

// ALTERAÇÃO 3: Nova Mensagem de Sucesso
// Executa insert
if (mysqli_query($conexao, $sql)) {
    // Nova mensagem para o usuário
    $mensagem_final = 'Cadastro realizado! Aguardando aprovação do administrador.';

    echo json_encode([
        'status' => 'sucesso',
        'mensagem' => $mensagem_final,
        'redirect' => 'login.php'
    ]);
    exit;
} else {
    $erro = mysqli_error($conexao);
    if (strpos($erro, "Duplicate entry") !== false) {
        $erro = "Já existe um registro com esse CPF!";
    } else {
        $erro = "Erro ao cadastrar: ".$erro;
    }
    echo json_encode(['status'=>'erro','mensagem'=>$erro]);
    exit;
}