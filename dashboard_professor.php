<?php
session_start();
// --- DEBUG TEMPORÁRIO (REMOVER APÓS TESTE) ---
// Define um ID de professor válido para teste.
$_SESSION['id_prof'] = 1; 
// --- FIM DEBUG TEMPORÁRIO ---

// Verifica se o professor está logado
if (!isset($_SESSION['id_prof'])) {
    header('Location: login.php'); 
    exit();
}

// Inclui a conexão com o banco de dados
include 'includes/config.php'; 

// ID do Professor Logado
$id_professor_logado = $_SESSION['id_prof'];

// --- LÓGICA PHP PARA DADOS DO PROFESSOR ---
// ATENÇÃO: Use `id_prof` como condição na sua tabela `professor`
$sql_prof = "SELECT nome, areas FROM professor WHERE id_prof = ?";
$stmt_prof = mysqli_prepare($conexao, $sql_prof);
mysqli_stmt_bind_param($stmt_prof, "i", $id_professor_logado);
mysqli_stmt_execute($stmt_prof);
$result_prof = mysqli_stmt_get_result($stmt_prof);
$dados_professor = mysqli_fetch_assoc($result_prof);
// Verifica se o professor existe
if (!$dados_professor) {
    echo "Erro: Professor não encontrado no banco de dados.";
    exit();
}
mysqli_stmt_close($stmt_prof);

$nome_professor = htmlspecialchars($dados_professor['nome'] ?? 'Professor');
$areas_professor = htmlspecialchars($dados_professor['areas'] ?? 'N/A');

// --- LÓGICA PHP PARA BUSCAR SUAS TURMAS E DISCIPLINAS (Lógica de Exemplo) ---
$sql_minhas_turmas = "
    SELECT DISTINCT
        t.id_turma, 
        t.nome_turma, 
        GROUP_CONCAT(d.nome_disciplina SEPARATOR ', ') AS disciplinas_alocadas
    FROM turma t
    JOIN alocacao_aulas a ON t.id_turma = a.id_turma
    JOIN disciplina d ON a.id_disciplina = d.id_disciplina
    WHERE a.id_prof = ?
    GROUP BY t.id_turma
    ORDER BY t.nome_turma ASC
";

$stmt_turmas = mysqli_prepare($conexao, $sql_minhas_turmas);
// Lidar com falha no prepare, se necessário
if ($stmt_turmas) {
    mysqli_stmt_bind_param($stmt_turmas, "i", $id_professor_logado);
    mysqli_stmt_execute($stmt_turmas);
    $result_minhas_turmas = mysqli_stmt_get_result($stmt_turmas);
    $minhas_turmas = mysqli_fetch_all($result_minhas_turmas, MYSQLI_ASSOC);
    mysqli_stmt_close($stmt_turmas);
} else {
    // Se a tabela 'alocacao_aulas' não existir ou houver erro na query
    $minhas_turmas = []; 
}

$total_turmas = count($minhas_turmas);

// --- LÓGICA PHP PARA BUSCAR HORÁRIO (Dados de Exemplo para Layout) ---
$horario_grade = [
    ['dia' => 'Segunda', 'horario' => '07:00 - 08:40', 'turma' => '3º DS', 'disciplina' => 'Programação Web'],
    ['dia' => 'Terça', 'horario' => '10:30 - 12:10', 'turma' => '2º Mecatrônica', 'disciplina' => 'Robótica'],
];

// O mysqli_close($conexao); será executado ao final.
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>PlanIt - Professor | Dashboard</title>
    <link rel="stylesheet" href="css/dashboard.css" /> 
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" crossorigin="anonymous" /> 
</head>
<body>

    <header>
        <div class="logo" onclick="mostrarSecao('dashboard-home')" style="cursor: pointer;">PlanIt 🦉</div>
        <div class="profile">
            <span><?= $nome_professor; ?> (Professor)</span>
            <img src="img/avatar_prof.jpg" alt="Avatar" />
        </div>
    </header>

    <aside>
        <nav>
            <ul>
                <li class="menu-title">Minhas Informações</li>
                <ul class="submenu">
                    <li><a href="#home" onclick="mostrarSecao('dashboard-home'); return false;">Home</a></li>
                    <li><a href="#minhas-turmas" onclick="mostrarSecao('turmas-section'); return false;">Minhas Turmas (<?= $total_turmas; ?>)</a></li>
                    <li><a href="#grade-horarios" onclick="mostrarSecao('grade-section'); return false;">Grade de Horários</a></li>
                </ul>

                <li class="menu-title">Ferramentas</li>
                <ul class="submenu">
                    <li><a href="restricao_professor.php">Gerenciar Restrições</a></li>
                    <li><a href="material_apoio.php">Material de Apoio</a></li>
                </ul>

                <li class="menu-title">Sair</li>
                <ul class="submenu">
                    <li><a href="logout.php">Sair do Sistema</a></li>
                </ul>
            </ul>
        </nav>
    </aside>

    <main>
    
    <div id="dashboard-home">
        <div class="main-header">
            <h1>Bem-vindo(a), Professor(a) <?= $nome_professor; ?>!</h1>
            <p>Suas áreas de atuação: **<?= $areas_professor; ?>**</p>
            <p>Você está alocado(a) em **<?= $total_turmas; ?>** turma(s) neste período.</p>
        </div>
    </div>
    
    <div id="turmas-section" style="display:none;">
        <div class="main-header">
            <h1>Minhas Turmas e Disciplinas</h1>
            <p>Lista de turmas alocadas para você neste período.</p>
        </div>
        
        <div class="listagem">
            <?php if (empty($minhas_turmas)): ?>
                <p>Você não tem turmas alocadas neste momento.</p>
            <?php else: ?>
                <table class="tabela-minhas-turmas">
                    <thead>
                        <tr>
                            <th>ID Turma</th>
                            <th>Nome da Turma</th>
                            <th>Disciplinas Alocadas</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($minhas_turmas as $turma): ?>
                        <tr>
                            <td><?= htmlspecialchars($turma['id_turma']); ?></td>
                            <td><?= htmlspecialchars($turma['nome_turma']); ?></td>
                            <td><?= htmlspecialchars($turma['disciplinas_alocadas']); ?></td>
                            <td>
                                <a href="turma_detalhes.php?id=<?= $turma['id_turma']; ?>" class="btn-acao btn-ver">
                                    <i class="fa-solid fa-eye"></i> Ver Detalhes
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
    
    <div id="grade-section" style="display:none;">
        <div class="main-header">
            <h1>Grade de Horários Semanal</h1>
            <p>Seu horário de aulas e atividades. (Dados de Exemplo)</p>
        </div>
        
        <div class="listagem">
            <?php if (empty($horario_grade)): ?>
                <p>Nenhum horário de aula alocado ainda.</p>
            <?php else: ?>
                <table class="tabela-grade">
                    <thead>
                        <tr>
                            <th>Dia da Semana</th>
                            <th>Horário</th>
                            <th>Turma</th>
                            <th>Disciplina</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($horario_grade as $item): ?>
                        <tr>
                            <td><?= htmlspecialchars($item['dia']); ?></td>
                            <td><?= htmlspecialchars($item['horario']); ?></td>
                            <td><?= htmlspecialchars($item['turma']); ?></td>
                            <td><?= htmlspecialchars($item['disciplina']); ?></td>
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
        document.getElementById('turmas-section').style.display = 'none';
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
// Fecha a conexão no final do script
if (isset($conexao)) {
    mysqli_close($conexao);
}
?>