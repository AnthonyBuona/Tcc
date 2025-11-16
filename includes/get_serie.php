<?php
// Arquivo: includes/get_series.php
// Retorna a lista completa de séries para preencher o SELECT.

session_start();
// O 'config.php' deve ter a conexão $conexao definida
include 'config.php'; 
header('Content-Type: application/json');

$response = ['success' => false, 'lista' => [], 'error' => ''];

// USANDO SUA ESTRUTURA: id_serie e nome_serie
$sql = "SELECT id_serie, nome_serie FROM serie ORDER BY nome_serie ASC";

if ($result = mysqli_query($conexao, $sql)) {
    $response['success'] = true;
    $response['lista'] = mysqli_fetch_all($result, MYSQLI_ASSOC);
    mysqli_free_result($result);
} else {
    $response['error'] = "Erro ao buscar séries: " . mysqli_error($conexao);
}

echo json_encode($response);
?>