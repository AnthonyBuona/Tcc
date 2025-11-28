<?php
session_start();
// A verificação de sessão é mantida.
// A linha de debug (ex: $_SESSION['id_aluno'] = 26;) FOI REMOVIDA.

// Verifica se o aluno está logado
if (!isset($_SESSION['id_aluno'])) {
    header('Location: login.php'); 
    exit();
}

// Inclui a conexão com o banco de dados
include 'includes/config.php'; 

// ID do Aluno Logado é obtido da sessão criada pelo login.
$id_aluno_logado = $_SESSION['id_aluno'];

// --- LÓGICA PHP PARA DADOS DO ALUNO E TURMA (Sem alterações na lógica SQL) ---
$sql_aluno = "
    SELECT 
        a.nome, 
        t.id_turma,
        t.nome_turma 
    FROM aluno a
    LEFT JOIN turma t ON a.id_turma = t.id_turma
    WHERE a.id_aluno = ?
";
$stmt_aluno = mysqli_prepare($conexao, $sql_aluno);
mysqli_stmt_bind_param($stmt_aluno, "i", $id_aluno_logado);
mysqli_stmt_execute($stmt_aluno);
$result_aluno = mysqli_stmt_get_result($stmt_aluno);
$dados_aluno = mysqli_fetch_assoc($result_aluno);

if (!$dados_aluno || $dados_aluno['nome'] === null) {
    // Redireciona ou mostra erro se o ID na sessão for inválido/não existir mais
    header('Location: logout.php'); // Sugestão: destrói a sessão inválida
    exit();
}
mysqli_stmt_close($stmt_aluno);

$nome_aluno = htmlspecialchars($dados_aluno['nome'] ?? 'Aluno');
$id_turma_aluno = $dados_aluno['id_turma'] ?? null;
$nome_turma_aluno = htmlspecialchars($dados_aluno['nome_turma'] ?? 'Turma Não Atribuída');

// --- DADOS DE EXEMPLO ---
$horario_grade_aluno = [
    ['dia' => 'Segunda', 'horario' => '07:00 - 08:40', 'disciplina' => 'Programação Web', 'professor' => 'Prof. Carlos'],
    ['dia' => 'Terça', 'horario' => '10:30 - 12:10', 'disciplina' => 'Robótica', 'professor' => 'Prof. Ana'],
    ['dia' => 'Quarta', 'horario' => '08:40 - 10:20', 'disciplina' => 'Português', 'professor' => 'Prof. Beatriz'],
];
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>PlanIt - Aluno | Dashboard</title>
    <link rel="stylesheet" href="css/dashboard.css" /> 
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" crossorigin="anonymous" /> 
</head>
<body>

    <header>
        <div class="logo" onclick="mostrarSecao('dashboard-home')" style="cursor: pointer;">PlanIt 🦉</div>
        <div class="profile">
            <span><?= $nome_aluno; ?> (Aluno)</span>
            <img src="img/avatar_aluno.jpg" alt="Avatar" />
        </div>
    </header>

    <aside>
        <nav>
            <ul>
                <li class="menu-title">Meu Perfil</li>
                <ul class="submenu">
                    <li><a href="#home" onclick="mostrarSecao('dashboard-home'); return false;">Home</a></li>
                    <li><a href="#minha-turma" onclick="mostrarSecao('turma-section'); return false;">Minha Turma</a></li>
                    <li><a href="#grade-horarios" onclick="mostrarSecao('grade-section'); return false;">Grade de Horários</a></li>
                </ul>

                <li class="menu-title">Acadêmico</li>
                <ul class="submenu">
                    <li><a href="minhas_disciplinas.php">Minhas Disciplinas</a></li>
                    <li><a href="minhas_notas.php">Notas e Faltas</a></li>
                    <li><a href="material_apoio.php">Material de Apoio</a></li>
                </ul>

<li class="menu-title">Sair</li>
<ul class="submenu">
<li><a href="includes/logout.php">Sair do Sistema</a></li> 
</ul>
            </ul>
        </nav>
    </aside>

    <main>
    
    <div id="dashboard-home">
        <div class="main-header">
            <h1>Bem-vindo(a), Aluno(a) <?= $nome_aluno; ?>!</h1>
            <p>Você está matriculado na turma: **<?= $nome_turma_aluno; ?>** (ID: <?= $id_turma_aluno ?? 'N/A'; ?>)</p>
            <p>Confira seus horários e atividades da semana.</p>
        </div>
    </div>
    
    <div id="turma-section" style="display:none;">
        <div class="main-header">
            <h1>Detalhes da Minha Turma</h1>
            <p>Informações e colegas da turma **<?= $nome_turma_aluno; ?>**.</p>
        </div>
        
        <div class="listagem">
            <p>ID da Turma: **<?= $id_turma_aluno ?? 'N/A'; ?>**</p>
            <p>Nome: **<?= $nome_turma_aluno; ?>**</p>
            <p>Professor Regente: **[Consultar no DB]**</p>
            
            <h3>Colegas de Turma (Exemplo)</h3>
            <table class="tabela-colegas">
                <thead>
                    <tr><th>Nome do Aluno</th><th>Contato (Placeholder)</th></tr>
                </thead>
                <tbody>
                    <tr><td>Maria Silva</td><td>maria.s@escola.com</td></tr>
                    <tr><td>João Pereira</td><td>joao.p@escola.com</td></tr>
                    </tbody>
            </table>
        </div>
    </div>
    
    <div id="grade-section" style="display:none;">
        <div class="main-header">
            <h1>Grade de Horários da Turma <?= $nome_turma_aluno; ?></h1>
            <p>Seu horário de aulas semanal. (Dados de Exemplo)</p>
        </div>
        
        <div class="listagem">
            <?php if (empty($horario_grade_aluno)): ?>
                <p>O horário de aulas da sua turma ainda não foi alocado.</p>
            <?php else: ?>
                <table class="tabela-grade">
                    <thead>
                        <tr>
                            <th>Dia da Semana</th>
                            <th>Horário</th>
                            <th>Disciplina</th>
                            <th>Professor</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($horario_grade_aluno as $item): ?>
                        <tr>
                            <td><?= htmlspecialchars($item['dia']); ?></td>
                            <td><?= htmlspecialchars($item['horario']); ?></td>
                            <td><?= htmlspecialchars($item['disciplina']); ?></td>
                            <td><?= htmlspecialchars($item['professor']); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>

    </main>

<script>
    function mostrarSecao(idSecao) {
        document.getElementById('dashboard-home').style.display = 'none';
        document.getElementById('turma-section').style.display = 'none';
        document.getElementById('grade-section').style.display = 'none';
        
        const secao = document.getElementById(idSecao);
        if (secao) {
            secao.style.display = 'block';
        }
    }
    
    document.addEventListener('DOMContentLoaded', () => {
        mostrarSecao('dashboard-home');
    });
</script>
</body>
</html>
<?php 
if (isset($conexao)) {
    mysqli_close($conexao);
}
?>