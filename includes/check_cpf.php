<?php
include 'config.php';
header('Content-Type: application/json');
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);

$cpf_input = $_GET['cpf'] ?? '';
$cpf = trim($cpf_input);

if (empty($cpf)) {
    echo json_encode(["existe" => false, "mensagem" => "CPF inválido"]);
    exit;
}

// Consultar tabela de alunos
$sqlAluno = "SELECT nome FROM aluno WHERE cpf = '$cpf' LIMIT 1";
$resultAluno = mysqli_query($conexao, $sqlAluno);
$rowAluno = mysqli_fetch_assoc($resultAluno);

// Consultar tabela de professores
$sqlProf = "SELECT nome FROM professor WHERE cpf = '$cpf' LIMIT 1";
$resultProf = mysqli_query($conexao, $sqlProf);
$rowProf = mysqli_fetch_assoc($resultProf);

// Retornar resultado
if ($rowAluno) {
    echo json_encode([
        "existe" => true,
        "mensagem" => "CPF já cadastrado como aluno: " . $rowAluno['nome']
    ]);
} elseif ($rowProf) {
    echo json_encode([
        "existe" => true,
        "mensagem" => "CPF já cadastrado como professor: " . $rowProf['nome']
    ]);
} else {
    echo json_encode([
        "existe" => false,
        "mensagem" => "CPF não cadastrado"
    ]);
}
?>
