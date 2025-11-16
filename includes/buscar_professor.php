<?php
// Arquivo: includes/buscar_professor.php
header('Content-Type: application/json');
session_start();

include 'config.php'; // Inclui a conexão $conexao

if (!isset($conexao)) {
    echo json_encode(['success' => false, 'error' => 'Erro de conexão com o banco de dados.']);
    exit;
}

// Obtém o termo de busca (filtro) do GET ou POST
$termo_busca = isset($_GET['termo']) ? trim($_GET['termo']) : '';

// Prepara a consulta SQL
$sql = "SELECT id_prof, nome, cpf, areas, status_aprovacao FROM professor";
$parametros = [];
$tipos = '';

if (!empty($termo_busca)) {
    // Adiciona a cláusula WHERE para filtrar por nome, CPF ou áreas
    $sql .= " WHERE nome LIKE ? OR cpf LIKE ? OR areas LIKE ?";
    $termo_like = '%' . $termo_busca . '%';
    
    $parametros[] = &$termo_like;
    $parametros[] = &$termo_like;
    $parametros[] = &$termo_like;
    $tipos = 'sss'; // Três strings
}

$sql .= " ORDER BY id_prof DESC";

$dados_professores = [];

// Usando Prepared Statements para segurança
if ($stmt = $conexao->prepare($sql)) {
    
    if (!empty($termo_busca)) {
        // Aplica os parâmetros dinamicamente (necessário para bind_param com array)
        call_user_func_array([$stmt, 'bind_param'], array_merge([$tipos], $parametros));
    }
    
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $dados_professores[] = $row;
    }

    $stmt->close();
    
    echo json_encode(['success' => true, 'data' => $dados_professores]);

} else {
    echo json_encode(['success' => false, 'error' => 'Erro na preparação da consulta: ' . $conexao->error]);
}

$conexao->close();
?>