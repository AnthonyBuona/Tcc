<?php
// Arquivo: includes/get_series.php
// Retorna a lista completa de séries para preencher o SELECT.

session_start();
include 'config.php'; // Garanta que este caminho e arquivo existam e funcionem.
header('Content-Type: application/json');

$response = ['success' => false, 'lista' => [], 'error' => ''];

// SUA ESTRUTURA: id_serie e nome_serie
$sql = "SELECT id_serie, nome_serie FROM serie ORDER BY nome_serie ASC";

if ($result = mysqli_query($conexao, $sql)) {
    $response['success'] = true;
    $response['lista'] = mysqli_fetch_all($result, MYSQLI_ASSOC);
    mysqli_free_result($result);
} else {
    // Se houver erro de banco de dados, esta mensagem ajudará a debuggar
    $response['error'] = "Erro ao buscar séries: " . mysqli_error($conexao);
}

echo json_encode($response);
?>