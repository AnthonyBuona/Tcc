<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Teste de Cadastro</title>
</head>
<body>

<h2>Cadastro de Aluno</h2>
<form action="cadastro.php" method="POST">
    <input type="hidden" name="tipo" value="aluno">

    Nome: <input type="text" name="nome" required><br>
    CPF: <input type="text" name="cpf" required><br>
    Senha: <input type="password" name="senha" required><br>

    Série:
    <select name="id_serie" required>
        <option value="4">1º Ano</option>
        <option value="5">2º Ano</option>
        <option value="6">3º Ano</option>
    </select><br>

    Turma:
    <select name="id_turma" required>
        <option value="4">Desenvolvimento de Sistemas</option>
        <option value="5">Mecatrônica</option>
        <option value="6">Linguagens</option>
    </select><br>

    <button type="submit">Cadastrar Aluno</button>
</form>

<hr>

<h2>Cadastro de Professor</h2>
<form action="cadastro.php" method="POST">
    <input type="hidden" name="tipo" value="professor">

    Nome: <input type="text" name="nome" required><br>
    CPF: <input type="text" name="cpf" required><br>
    Senha: <input type="password" name="senha" required><br>

    <!-- id_serie opcional, caso queira associar depois -->
    <button type="submit">Cadastrar Professor</button>
</form>

</body>
</html>
