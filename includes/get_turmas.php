<?php
// Arquivo: includes/get_turmas.php
// Retorna a lista completa de turmas com id_serie para preencher o SELECT filtrado.

session_start();
include 'config.php'; 
header('Content-Type: application/json');

$response = ['success' => false, 'lista' => [], 'error' => ''];

// Retornando id_serie também para o filtro funcionar
$sql = "SELECT id_turma, nome_turma, id_serie FROM turma ORDER BY nome_turma ASC";

if ($result = mysqli_query($conexao, $sql)) {
    $response['success'] = true;
    $response['lista'] = mysqli_fetch_all($result, MYSQLI_ASSOC);
    mysqli_free_result($result);
} else {
    $response['error'] = "Erro ao buscar turmas: " . mysqli_error($conexao);
}

echo json_encode($response);
?>
