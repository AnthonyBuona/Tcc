<?php
include 'includes/config.php';

$id_turma = intval($_GET['id_turma'] ?? 0);

$sql = "SELECT nome, cpf FROM aluno WHERE id_turma = $id_turma ORDER BY nome ASC";
$result = mysqli_query($conexao, $sql);

if (mysqli_num_rows($result) > 0) {
    echo "<table class='tabela-alunos-turma'>
            <thead><tr><th>Nome</th><th>CPF</th></tr></thead><tbody>";
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<tr><td>{$row['nome']}</td><td>{$row['cpf']}</td></tr>";
    }
    echo "</tbody></table>";
} else {
    echo "<p>Nenhum aluno encontrado nesta turma.</p>";
}
?>
