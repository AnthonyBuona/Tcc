<?php
include 'config.php';

$id_turma = filter_input(INPUT_GET, 'id_turma', FILTER_VALIDATE_INT);

if (!$id_turma || $id_turma <= 0) {
    echo "<p>ID da turma inválido.</p>";
    exit;
}

$sql = "SELECT nome, cpf FROM aluno WHERE id_turma = ? ORDER BY nome ASC";
if ($stmt = mysqli_prepare($conexao, $sql)) {
    mysqli_stmt_bind_param($stmt, 'i', $id_turma);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) > 0) {
        echo "<table class='tabela-alunos-turma'>
                <thead><tr><th>Nome</th><th>CPF</th></tr></thead><tbody>";
        while ($row = mysqli_fetch_assoc($result)) {
            $nome = htmlspecialchars($row['nome']);
            $cpf = htmlspecialchars($row['cpf']);
            echo "<tr><td>{$nome}</td><td>{$cpf}</td></tr>";
        }
        echo "</tbody></table>";
    } else {
        echo "<p>Nenhum aluno encontrado nesta turma.</p>";
    }

    mysqli_stmt_close($stmt);
} else {
    echo "<p>Erro ao preparar a consulta: " . htmlspecialchars(mysqli_error($conexao)) . "</p>";
}
?>
