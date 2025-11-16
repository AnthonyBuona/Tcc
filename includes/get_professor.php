<?php
// Inclui o arquivo de conexão, que deve conter $conexao
require_once 'config.php';
header('Content-Type: application/json'); // Retorna em JSON

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $sql = "SELECT id_prof, nome, areas FROM professor WHERE id_prof = ?";
    if ($stmt = mysqli_prepare($conexao, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        if ($professor = mysqli_fetch_assoc($result)) {
            echo json_encode(['success' => true, 'professor' => $professor]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Professor não encontrado.']);
        }
        mysqli_stmt_close($stmt);
    } else {
        echo json_encode(['success' => false, 'error' => 'Erro na preparação da query: ' . mysqli_error($conexao)]);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'ID não fornecido.']);
}

mysqli_close($conexao);
?>