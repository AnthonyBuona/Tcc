<?php
// Inclui o arquivo de conexão, que deve conter $conexao
require_once 'config.php';
header('Content-Type: application/json'); // Retorna em JSON

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_prof'], $_POST['nome'], $_POST['areas'])) {
    $id = (int)$_POST['id_prof'];
    $nome = trim($_POST['nome']);
    $areas = trim($_POST['areas']);
    
    $sql = "UPDATE professor SET nome = ?, areas = ? WHERE id_prof = ?";
    if ($stmt = mysqli_prepare($conexao, $sql)) {
        mysqli_stmt_bind_param($stmt, "ssi", $nome, $areas, $id);
        if (mysqli_stmt_execute($stmt)) {
            echo json_encode(['success' => true, 'message' => 'Professor atualizado com sucesso!']);
        } else {
            echo json_encode(['success' => false, 'error' => 'Erro ao executar o UPDATE: ' . mysqli_error($conexao)]);
        }
        mysqli_stmt_close($stmt);
    } else {
        echo json_encode(['success' => false, 'error' => 'Erro na preparação da query: ' . mysqli_error($conexao)]);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Dados incompletos ou método de requisição inválido.']);
}

mysqli_close($conexao);
?>