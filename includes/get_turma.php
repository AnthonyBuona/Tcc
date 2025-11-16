<?php
// Arquivo: includes/get_turmas.php
// Retorna a lista completa de turmas para preencher o SELECT.

session_start();
include 'config.php'; 
header('Content-Type: application/json');

$response = ['success' => false, 'lista' => [], 'error' => ''];

// USANDO SUA ESTRUTURA: id_turma e nome_turma
$sql = "SELECT id_turma, nome_turma FROM turma ORDER BY nome_turma ASC";

if ($result = mysqli_query($conexao, $sql)) {
    $response['success'] = true;
    $response['lista'] = mysqli_fetch_all($result, MYSQLI_ASSOC);
    mysqli_free_result($result);
} else {
    $response['error'] = "Erro ao buscar turmas: " . mysqli_error($conexao);
}

echo json_encode($response);
?>