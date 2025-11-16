<?php
session_start();
// Inclui a conexão com o banco de dados.
include 'includes/config.php';

// Nome da tabela de alocação no banco de dados
$tabela_alocacao = 'horario'; 
$tabela_periodo = 'periodo_aula'; 

// ID de Sala Padrão: Usado internamente para satisfazer a restrição de FK na tabela 'horario',
// já que a sala não será gerenciada pelo usuário nesta tela.
define('DEFAULT_SALA_ID', 1); 

$mensagem_sucesso = '';
$mensagem_erro = '';

// Verifica mensagens passadas via GET (para o Padrão Post/Redirect/Get)
if (isset($_GET['msg_sucesso'])) { $mensagem_sucesso = htmlspecialchars($_GET['msg_sucesso']); }
if (isset($_GET['msg_erro'])) { $mensagem_erro = htmlspecialchars($_GET['msg_erro']); }


// --- DEFINIÇÕES DE DIAS ---
$dias_semana = ['Segunda', 'Terça', 'Quarta', 'Quinta', 'Sexta'];

// --- CARREGAR PERIODOS DE AULA DO BANCO DE DADOS ---
$periodos_aula = [];
$sql_periodos = "SELECT id_periodo, horario AS horario_str FROM {$tabela_periodo} ORDER BY id_periodo ASC";
$result_periodos = mysqli_query($conexao, $sql_periodos);

if ($result_periodos) {
    $periodos_aula = mysqli_fetch_all($result_periodos, MYSQLI_ASSOC);
    mysqli_free_result($result_periodos);
} else {
    // Lista Estática de Períodos (Fallback)
    $periodos_aula = [
        ['id_periodo' => 1, 'horario_str' => '07:30 - 08:20'],
        ['id_periodo' => 2, 'horario_str' => '08:20 - 09:10'],
        ['id_periodo' => 3, 'horario_str' => '09:10 - 10:00'], 
        ['id_periodo' => 4, 'horario_str' => '10:20 - 11:10'],
        ['id_periodo' => 5, 'horario_str' => '11:10 - 12:00'],
        ['id_periodo' => 6, 'horario_str' => '12:00 - 12:50'], 
        ['id_periodo' => 7, 'horario_str' => '13:30 - 14:20'], 
        ['id_periodo' => 8, 'horario_str' => '14:20 - 15:10'],
        ['id_periodo' => 9, 'horario_str' => '15:10 - 16:00'], 
        ['id_periodo' => 10, 'horario_str' => '16:10 - 17:00'], 
    ];
    $mensagem_erro .= (empty($mensagem_erro) ? '' : '<br>') . "Aviso: Não foi possível carregar a tabela 'periodo_aula'. Usando horários estáticos.";
}
$mapa_periodos_str_to_id = array_column($periodos_aula, 'id_periodo', 'horario_str');

// --- LÓGICA DE CARREGAMENTO DE DADOS (Professor, Disciplina, Turma) ---

$professores = [];
$disciplinas = [];
$turmas = [];

// Carregar Professores
$sql_prof = "SELECT id_prof, nome FROM professor ORDER BY nome ASC";
$result_prof = mysqli_query($conexao, $sql_prof);
if ($result_prof) { $professores = mysqli_fetch_all($result_prof, MYSQLI_ASSOC); mysqli_free_result($result_prof); }

// Carregar Disciplinas
$sql_disc = "SELECT id_disc, nome_disc AS nome FROM disciplina ORDER BY nome_disc ASC"; 
$result_disc = mysqli_query($conexao, $sql_disc);
if ($result_disc) { 
    $disciplinas = mysqli_fetch_all($result_disc, MYSQLI_ASSOC); 
    mysqli_free_result($result_disc); 
}

// Carregar Turmas
$sql_turma = "SELECT id_turma, nome_turma AS nome_turma FROM turma ORDER BY nome_turma ASC";
$result_turma = mysqli_query($conexao, $sql_turma);
if ($result_turma) { $turmas = mysqli_fetch_all($result_turma, MYSQLI_ASSOC); mysqli_free_result($result_turma); }

// Mapear Turmas, Professores e Disciplinas por ID para fácil consulta no HTML/JS
$turmas_map = array_column($turmas, 'nome_turma', 'id_turma');
$professores_map = array_column($professores, 'nome', 'id_prof');
$disciplinas_map = array_column($disciplinas, 'nome', 'id_disc'); 


// --- LÓGICA DE FILTRO E CARREGAMENTO DA GRADE ---
$modo_visualizacao = isset($_REQUEST['modo']) ? $_REQUEST['modo'] : 'turma';

$id_turma_selecionada = 0; // Turma filter for 'turma' mode
$id_prof_selecionado = 0; // Professor filter for 'professor' mode

if ($modo_visualizacao === 'turma') {
    // Filtro para o modo 'turma'
    $id_turma_selecionada = isset($_REQUEST['id_turma']) ? (int)$_REQUEST['id_turma'] : (isset($turmas[0]['id_turma']) ? (int)$turmas[0]['id_turma'] : 0);
} 
else if ($modo_visualizacao === 'professor') {
    // Professor selecionado no filtro
    $id_prof_selecionado = isset($_REQUEST['id_prof']) ? (int)$_REQUEST['id_prof'] : 0; 
} 


$grade_visualizada = []; // Array que armazenará as aulas para exibição

$where_clause = "";
$bind_types = "";
$bind_params = [];

// 1. Definição do Filtro SQL
if ($modo_visualizacao === 'turma' && $id_turma_selecionada > 0) {
    // Filtra pela Turma selecionada
    $where_clause = "WHERE h.id_turma = ?";
    $bind_types = "i";
    $bind_params = [&$id_turma_selecionada];
} else if ($modo_visualizacao === 'professor' && $id_prof_selecionado > 0) {
    // Filtra pelo Professor selecionado
    $where_clause = "WHERE h.id_prof = ?";
    $bind_types = "i";
    $bind_params = [&$id_prof_selecionado];
} else if ($modo_visualizacao === 'geral') {
    $where_clause = ""; // Sem filtro no modo geral (queremos todos os dados)
}

// 2. Execução da Consulta (Se houver filtro válido ou modo geral)
$pode_consultar_grade = ($modo_visualizacao === 'geral' || ($where_clause !== "" && ($id_turma_selecionada > 0 || $id_prof_selecionado > 0))); 

if ($pode_consultar_grade) {
    
    // Consulta SQL completa
    $sql_aulas = "
        SELECT
            h.dia, h.id_periodo, h.id_prof, 
            h.id_disc AS id_disciplina, h.id_turma, h.id_sala,
            p.nome AS nome_prof, d.nome_disc AS nome_disc, t.nome_turma AS nome_turma,
            p_a.horario AS horario_str
        FROM {$tabela_alocacao} h
        JOIN professor p ON h.id_prof = p.id_prof
        JOIN disciplina d ON h.id_disc = d.id_disc 
        JOIN turma t ON h.id_turma = t.id_turma
        JOIN {$tabela_periodo} p_a ON h.id_periodo = p_a.id_periodo
        {$where_clause}
    ";
    
    $stmt_aulas = $conexao->prepare($sql_aulas);
    
    if ($stmt_aulas) {
        // Vincula os parâmetros de forma dinâmica
        if (!empty($bind_params)) {
            $stmt_aulas->bind_param($bind_types, ...$bind_params);
        }
        $stmt_aulas->execute();
        $result_aulas = $stmt_aulas->get_result();
        
        while ($row = $result_aulas->fetch_assoc()) {
            $key_day_period = $row['dia'] . '_' . $row['id_periodo']; 
            
            if ($modo_visualizacao === 'geral') {
                // Modo Geral (Turma como Pivot): Key é Dia_Periodo_Turma.
                $key = $key_day_period . '_' . $row['id_turma'];
                $grade_visualizada[$key] = $row;
            } else {
                // Modos TURMA e PROFESSOR: Key é Dia_Periodo.
                $grade_visualizada[$key_day_period] = $row;
            }
        }
        $stmt_aulas->close();
    } else {
        $mensagem_erro .= (empty($mensagem_erro) ? '' : '<br>') . "Erro de preparação da consulta: " . $conexao->error;
    }
}


// --- LÓGICA DE SALVAR UMA ÚNICA ATRIBUIÇÃO (POST da Célula) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao']) && $_POST['acao'] === 'salvar_aula') {
    
    $id_turma = (int)$_POST['modal_id_turma'];
    $id_disciplina_param = (int)$_POST['modal_id_disciplina']; // O valor do select é o ID da disciplina (id_disc)
    $id_prof = (int)$_POST['modal_id_prof'];
    $dia = trim($_POST['modal_dia']);
    $horario_str = trim($_POST['modal_horario']);

    $id_periodo = $mapa_periodos_str_to_id[$horario_str] ?? 0;
    
    // FORÇA O USO DA SALA PADRÃO
    $id_sala = DEFAULT_SALA_ID;


    if ($id_turma <= 0 && $id_disciplina_param > 0) {
         $mensagem_erro = "Erro: Turma inválida (ID não pode ser zero). Selecione a turma.";
    } elseif ($id_prof <= 0 && $id_disciplina_param > 0) {
        $mensagem_erro = "Erro: Professor inválido (ID não pode ser zero). Selecione o professor.";
    } elseif ($id_disciplina_param > 0 && $id_prof > 0 && $id_periodo > 0 && $id_turma > 0) {
        
        // REPLACE INTO para alocar/substituir
        $sql_replace = "
            REPLACE INTO {$tabela_alocacao} (id_disc, id_periodo, id_prof, id_turma, dia, id_sala)
            VALUES (?, ?, ?, ?, ?, ?)
        ";
        
        $stmt_replace = $conexao->prepare($sql_replace);

        if (!$stmt_replace) {
            $mensagem_erro = "Erro de preparação do REPLACE: " . $conexao->error;
        } else {
            // "iiiisi" -> id_disc, id_periodo, id_prof, id_turma, dia, id_sala 
            $stmt_replace->bind_param("iiiisi", $id_disciplina_param, $id_periodo, $id_prof, $id_turma, $dia, $id_sala);
            
            if ($stmt_replace->execute()) {
                $mensagem_sucesso = "Aula alocada/substituída com sucesso para {$dia} ({$horario_str}) na Turma: {$turmas_map[$id_turma]}.";
            } else {
                $mensagem_erro = "Erro ao alocar aula: " . $stmt_replace->error;
            }
            $stmt_replace->close();
        }
        
    } elseif ($id_turma > 0 && $id_periodo > 0 && $id_prof > 0) {
        // Lógica de remoção: se a disciplina for 0, deleta a alocação.
        if ($id_disciplina_param == 0) {
             // O DELETE usa a chave composta: id_turma, id_periodo, dia, id_sala.
             $sql_delete = "DELETE FROM {$tabela_alocacao} WHERE id_turma = ? AND id_periodo = ? AND dia = ? AND id_sala = ?";
             $stmt_delete = $conexao->prepare($sql_delete);
             
             if ($stmt_delete) {
                 $stmt_delete->bind_param("iisi", $id_turma, $id_periodo, $dia, $id_sala);
                 if ($stmt_delete->execute()) {
                     $mensagem_sucesso = "Aula removida com sucesso para {$dia} ({$horario_str}) da Turma: {$turmas_map[$id_turma]}.";
                 } else {
                     $mensagem_erro = "Erro ao remover aula: " . $stmt_delete->error;
                 }
                 $stmt_delete->close();
             }
        } else {
             $mensagem_erro = "Erro: Para remover a aula, selecione 'Disciplina (0)'.";
        }
        
    } else {
        $mensagem_erro = "Erro: Dados insuficientes para salvar ou remover aula.";
    }
    
    // Redireciona APÓS POST (Padrão Post/Redirect/Get)
    $modo_visualizacao_redirect = $_POST['modo_visualizacao'] ?? 'turma';
    
    // Mantém os IDs de filtro originais
    $redirect_id = '';
    
    if ($modo_visualizacao_redirect === 'turma') {
        $id_filtro_turma = $_POST['filtro_id_turma'] ?? $id_turma; 
        $redirect_id = ($id_filtro_turma > 0) ? "&id_turma={$id_filtro_turma}" : "";
    } elseif ($modo_visualizacao_redirect === 'professor') {
        $id_filtro_prof = $_POST['filtro_id_prof'] ?? $id_prof; 
        $redirect_id = ($id_filtro_prof > 0) ? "&id_prof={$id_filtro_prof}" : ""; 
    }

    $url_base = 'atribuicao_aulas.php';
    if (isset($_SERVER['HTTP_HOST'])) {
         $url_base = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://{$_SERVER['HTTP_HOST']}{$_SERVER['PHP_SELF']}";
    }
    header("Location: {$url_base}?modo={$modo_visualizacao_redirect}{$redirect_id}&msg_sucesso=" . urlencode($mensagem_sucesso) . "&msg_erro=" . urlencode($mensagem_erro));
    exit();
}

?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>PlanIt - Admin | Atribuição de Aulas</title>
    <link rel="stylesheet" href="css/dashboard.css" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" crossorigin="anonymous" />
    <style>
        /* Estilos da Grade */
        .grade-atribuicao { width: 100%; border-collapse: collapse; margin-top: 20px; background-color: rgba(255, 255, 255, 0.9); border-radius: 12px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,.1); }
        .grade-atribuicao th, .grade-atribuicao td { padding: 8px 10px; text-align: center; border: 1px solid #e5e7eb; vertical-align: middle; }
        .grade-atribuicao th { background-color: #48ab87; color: #fff; }
        
        /* Estilos In-Cell (Modo Turma - Edição) */
        .in-cell-slot {
            padding: 5px; 
            height: 90px; 
            cursor: default !important;
            min-width: 120px; 
        }
        .in-cell-slot.alocada {
            background-color: #d4edda;
        }
        .in-cell-alocacao-form {
            display: flex;
            flex-direction: column;
            gap: 3px; 
            width: 100%;
        }
        .in-cell-select {
            width: 100%;
            padding: 2px;
            font-size: 0.8em; 
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
            background-color: #fff;
            min-height: 20px;
        }
        .in-cell-select[disabled] {
            opacity: 0.7;
            background-color: #f0f0f0;
        }

        /* Estilos de Visualização */
        .select-container { display: flex; gap: 20px; margin-bottom: 20px; flex-wrap: wrap; }
        .select-container .form-group { flex-grow: 1; min-width: 200px; }
        .form-group .full-width-select { width: 100%; padding: 10px 15px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; background-color: #fff; min-height: 40px; font-size: 14px;}
        
        /* Estilos para Mensagens */
        .mensagem-sucesso { background-color: #d4edda; color: #155724; padding: 10px; border: 1px solid #c3e6cb; border-radius: 4px; margin-bottom: 15px; }
        .mensagem-erro { background-color: #f8d7da; color: #721c24; padding: 10px; border: 1px solid #f5c6cb; border-radius: 4px; margin-bottom: 15px; }
        
    </style>
</head>
<body>
    <header>
        <div class="logo" onclick="window.location.href='dashboard.php';" style="cursor: pointer;">PlanIt 🦉</div>
        <div class="profile">
            <span>Administrador</span>
            <img src="avatar.png" alt="Avatar" />
        </div>
    </header>

    <aside>
        <nav>
            <ul>
                <li class="menu-title">Gerenciamento de Usuários</li>
                <ul class="submenu">
                    <li><a href="dashboard.php#aprovacoes">Aprovações (N)</a></li>
                    <li><a href="dashboard.php#listar-professores">Listar Professores</a></li>
                    <li><a href="dashboard.php#listar-alunos">Listar Alunos</a></li>
                    <li><a href="dashboard.php#listar-turmas">Listar Turmas</a></li>
                </ul>

                <li class="menu-title">Gerenciamento de Disciplinas</li>
                <ul class="submenu">
                    <li><a href="disciplina_cadastro.php">Cadastrar Disciplina</a></li>
                    <li><a href="disciplina_visualizar.php">Visualizar Disciplina</a></li>
                </ul>

                <li class="menu-title">Alocação de Horários</li>
                <ul class="submenu">
                    <li style="background-color: rgba(72,171,135,0.9);"><a href="atribuicao_aulas.php" class="menu-active" style="padding: 10px 20px; display: block; color: #fff; text-decoration: none; border-radius: 8px; font-weight: 500;">Atribuir Aulas</a></li>
                    <li><a href="restricoes_professor.php">Restrições de Professor</a></li>
                    <li><a href="validar_conflitos.php">Validar Conflitos</a></li>
                    <li><a href="grade_horarios.php">Exibir Grade de Horários</a></li>
                </ul>

                <li class="menu-title">Relatórios</li>
                <ul class="submenu">
                    <li><a href="relatorio_turma.php">Horário Semanal</a></li>
                    <li><a href="relatorio_carga.php">Relatório de Carga Horária</a></li>
                </ul>
            </ul>
        </nav>
    </aside>
    
    <main>
        <div class="container">
            <div class="main-header">
                <h1>Atribuição de Aulas</h1>
            </div>

            <?php if (!empty($mensagem_sucesso)): ?>
                <div class="mensagem-sucesso"><?php echo $mensagem_sucesso; ?></div>
            <?php endif; ?>

            <?php if (!empty($mensagem_erro)): ?>
                <div class="mensagem-erro"><?php echo $mensagem_erro; ?></div>
            <?php endif; ?>

            <div class="card">
                <h3>Filtro de Grade</h3>
                <form method="GET" action="" id="filtro-form">
                    <div class="select-container">
                        
                        <div class="form-group">
                            <label for="modo">Modo de Visualização:</label>
                            <select name="modo" id="modo" required class="full-width-select" onchange="this.form.submit()">
                                <option value="turma" <?php echo ($modo_visualizacao === 'turma') ? 'selected' : ''; ?>>Por Turma </option>
                                <option value="professor" <?php echo ($modo_visualizacao === 'professor') ? 'selected' : ''; ?>>Por Professor</option>
                                <option value="geral" <?php echo ($modo_visualizacao === 'geral') ? 'selected' : ''; ?>>Geral</option>
                            </select>
                        </div>

                        <div class="form-group" id="filtro-turma" style="display: <?php echo ($modo_visualizacao === 'turma') ? 'block' : 'none'; ?>;">
                            <label for="id_turma">Turma para Atribuição:</label>
                            <select name="id_turma" id="id_turma" class="full-width-select" onchange="this.form.submit()">
                                <option value="0">Selecione a Turma</option>
                                <?php foreach ($turmas as $turma): ?>
                                    <option value="<?php echo $turma['id_turma']; ?>" <?php echo ($id_turma_selecionada == $turma['id_turma']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($turma['nome_turma']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="form-group" id="filtro-professor" style="display: <?php echo ($modo_visualizacao === 'professor') ? 'block' : 'none'; ?>;">
                            <label for="id_prof">Professor para Atribuição:</label>
                            <select name="id_prof" id="id_prof" class="full-width-select" onchange="this.form.submit()">
                                <option value="0">Selecione o Professor</option>
                                <?php foreach ($professores as $prof): ?>
                                    <option value="<?php echo $prof['id_prof']; ?>" <?php echo ($id_prof_selecionado == $prof['id_prof']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($prof['nome']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                    </div>
                </form>
            </div>
            
            <?php
            // Condição para exibir a grade
            $pode_exibir_grade = ($modo_visualizacao === 'geral' || ($id_turma_selecionada > 0) || ($id_prof_selecionado > 0));

            if ($pode_exibir_grade):
                
                // --- Layout Turma/Professor (Uma Grande Tabela) ---
                if ($modo_visualizacao !== 'geral'):
                    
                    $titulo = "Grade de Aulas - ";
                    if ($modo_visualizacao === 'turma') {
                        $titulo .= "Turma: " . ($turmas_map[$id_turma_selecionada] ?? 'N/A');
                    } else if ($modo_visualizacao === 'professor') {
                        $titulo = "Grade - Professor: " . ($professores_map[$id_prof_selecionado] ?? 'N/A');
                    }
                    ?>
                    <div class="card" style="margin-top: 20px;">
                        <h3><?php echo $titulo; ?></h3>
                        <table class="grade-atribuicao">
                            <thead>
                                <tr>
                                    <th>Horário</th>
                                    <?php foreach ($dias_semana as $dia): ?>
                                        <th><?php echo $dia; ?></th>
                                    <?php endforeach; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                foreach ($periodos_aula as $periodo):
                                    $id_periodo = $periodo['id_periodo'];
                                    $horario_str = $periodo['horario_str'];
                                ?>
                                    <tr>
                                        <td><?php echo $horario_str; ?></td>
                                        <?php 
                                        foreach ($dias_semana as $dia_str):
                                            $key = $dia_str . '_' . $id_periodo;
                                            $info_alocada = $grade_visualizada[$key] ?? null;
                                            $texto_celula = '';
                                            
                                            $current_turma_id = $info_alocada['id_turma'] ?? '';
                                            $current_prof_id = $info_alocada['id_prof'] ?? '';
                                            $current_disc_id = $info_alocada['id_disciplina'] ?? '';
            
                                            $css_class = 'in-cell-slot';
                                            if ($info_alocada) { $css_class .= ' alocada'; }
                                            
                                            $disabled_attr = '';

                                            // Determina a variável que está FIXA no modo de visualização
                                            if ($modo_visualizacao === 'turma') {
                                                // Turma é FIXA: verifica se a turma foi selecionada no filtro
                                                if ($id_turma_selecionada == 0) { $disabled_attr = 'disabled'; }
                                            } elseif ($modo_visualizacao === 'professor') {
                                                // Professor é FIXO: verifica se o professor foi selecionado no filtro
                                                if ($id_prof_selecionado == 0) { $disabled_attr = 'disabled'; }
                                            }
            
                                            // --- INÍCIO DO FORMULÁRIO IN-CELL ---
                                            $texto_celula = '
                                                <form method="POST" action="atribuicao_aulas.php" class="in-cell-alocacao-form">
                                                    <input type="hidden" name="acao" value="salvar_aula">
                                                    <input type="hidden" name="modal_dia" value="' . $dia_str . '">
                                                    <input type="hidden" name="modal_horario" value="' . $horario_str . '">
                                                    <input type="hidden" name="modo_visualizacao" value="' . $modo_visualizacao . '">
                                            ';
                                            
                                            // CAMPOS DE REDIRECIONAMENTO PARA MANTER O FILTRO
                                            if ($modo_visualizacao === 'turma') {
                                                $texto_celula .= '<input type="hidden" name="filtro_id_turma" value="' . $id_turma_selecionada . '">';
                                            } elseif ($modo_visualizacao === 'professor') {
                                                $texto_celula .= '<input type="hidden" name="filtro_id_prof" value="' . $id_prof_selecionado . '">';
                                            }
                                            
                                            if ($modo_visualizacao === 'turma') {
                                                // Modo Turma: Turma é FIXA (Hidden), Prof/Disc são SELECIONÁVEIS
                                                $texto_celula .= '<input type="hidden" name="modal_id_turma" value="' . $id_turma_selecionada . '">';
                                                
                                                // Disciplina Select
                                                $texto_celula .= '
                                                    <select name="modal_id_disciplina" class="in-cell-select" onchange="this.form.submit()" ' . $disabled_attr . '>
                                                        <option value="0">Disciplina</option>';
                                                         foreach ($disciplinas as $disciplina) {
                                                             $selected = ($current_disc_id == $disciplina['id_disc']) ? 'selected' : ''; 
                                                             $texto_celula .= '<option value="' . $disciplina['id_disc'] . '" ' . $selected . '>' . htmlspecialchars($disciplina['nome']) . '</option>';
                                                         }
                                                         $texto_celula .= '
                                                    </select>
                                                    
                                                    <select name="modal_id_prof" class="in-cell-select" onchange="this.form.submit()" ' . $disabled_attr . '>
                                                        <option value="0">Professor</option>';
                                                         foreach ($professores as $prof) {
                                                             $selected = ($current_prof_id == $prof['id_prof']) ? 'selected' : '';
                                                             $texto_celula .= '<option value="' . $prof['id_prof'] . '" ' . $selected . '>' . htmlspecialchars($prof['nome']) . '</option>';
                                                         }
                                                         $texto_celula .= '
                                                    </select>
                                                ';
                                                
                                                if ($id_turma_selecionada == 0) {
                                                    $texto_celula = '<span style="color:red; font-size:10px;">Selecione Turma p/ Alocar</span>';
                                                    $css_class = 'in-cell-slot';
                                                }

                                            } elseif ($modo_visualizacao === 'professor') {
                                                // Modo Professor: Professor é FIXO (Hidden), Turma/Disc são SELECIONÁVEIS
                                                $texto_celula .= '<input type="hidden" name="modal_id_prof" value="' . $id_prof_selecionado . '">';
                                                
                                                // Turma Select (SELECIONÁVEL)
                                                $texto_celula .= '
                                                    <select name="modal_id_turma" class="in-cell-select" onchange="this.form.submit()" ' . $disabled_attr . '>
                                                        <option value="0">Turma</option>';
                                                         foreach ($turmas as $turma) {
                                                             $selected = ($current_turma_id == $turma['id_turma']) ? 'selected' : ''; 
                                                             $texto_celula .= '<option value="' . $turma['id_turma'] . '" ' . $selected . '>' . htmlspecialchars($turma['nome_turma']) . '</option>';
                                                         }
                                                         $texto_celula .= '
                                                    </select>

                                                    <select name="modal_id_disciplina" class="in-cell-select" onchange="this.form.submit()" ' . $disabled_attr . '>
                                                        <option value="0">Disciplina</option>';
                                                         foreach ($disciplinas as $disciplina) {
                                                             $selected = ($current_disc_id == $disciplina['id_disc']) ? 'selected' : ''; 
                                                             $texto_celula .= '<option value="' . $disciplina['id_disc'] . '" ' . $selected . '>' . htmlspecialchars($disciplina['nome']) . '</option>';
                                                         }
                                                         $texto_celula .= '
                                                    </select>
                                                ';
                                                
                                                if ($id_prof_selecionado == 0) {
                                                    $texto_celula = '<span style="color:red; font-size:10px;">Selecione Professor p/ Alocar</span>';
                                                    $css_class = 'in-cell-slot';
                                                }
                                                
                                            }
                                            
                                            if ($disabled_attr === '') {
                                                 // Fechar form para os modos Turma e Professor, se não estiverem desabilitados
                                                $texto_celula .= '</form>';
                                            }

                                        ?>
                                            <td class="<?php echo $css_class; ?>"
                                                data-dia="<?php echo $dia_str; ?>"
                                                data-horario="<?php echo $horario_str; ?>">
                                                <?php echo $texto_celula; ?>
                                            </td>
                                        <?php endforeach; ?>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; // Fim Layout Turma/Professor ?>

                <?php 
                // --- Layout Geral (Tabelas por Dia, Colunas por Turma) ---
                if ($modo_visualizacao === 'geral'):
                    // O layout geral permanece inalterado e correto.
                    ?>
                    <h3>Grade Geral por Turma </h3>
                    
                    <?php
                    // Garante que a grade só é exibida se houver turmas carregadas
                    if (!empty($turmas)): 
                        foreach ($dias_semana as $dia_str): 
                        ?>
                            <div class="card" style="margin-top: 20px; overflow-x: auto;">
                                <h3><?php echo "{$dia_str}"; ?></h3>
                                <table class="grade-atribuicao" style="min-width: 1000px; width: 100%;">
                                    <thead>
                                        <tr>
                                            <th style="width: 100px;">Horário</th>
                                            <?php foreach ($turmas as $turma): // TURMAS COMO COLUNAS ?>
                                                <th style="width: 120px;"><?php echo htmlspecialchars($turma['nome_turma']); ?></th>
                                            <?php endforeach; ?>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        foreach ($periodos_aula as $periodo):
                                            $id_periodo = $periodo['id_periodo'];
                                            $horario_str = $periodo['horario_str'];
                                        ?>
                                            <tr>
                                                <td><?php echo $horario_str; ?></td>
                                                <?php 
                                                // Itera pelas TURMAS (colunas)
                                                foreach ($turmas as $turma):
                                                    $id_turma = $turma['id_turma'];
                                                    $key = $dia_str . '_' . $id_periodo . '_' . $id_turma; // CHAVE: DIA_PERIODO_TURMA
                                                    $info_alocada = $grade_visualizada[$key] ?? null;
                                                    
                                                    $texto_celula = '';
                                                    $css_class = 'in-cell-slot';
                                                    $disabled_attr = '';

                                                    // Pega os IDs atuais
                                                    $turma_id_para_edicao = $id_turma; 
                                                    $current_prof_id = $info_alocada['id_prof'] ?? 0;
                                                    $current_disc_id = $info_alocada['id_disciplina'] ?? 0;
                                                    
                                                    if ($info_alocada) { 
                                                        $css_class .= ' alocada';
                                                    }
                                                    
                                                    // 3. Constrói o Formulário In-Cell (Todos são selecionáveis/visíveis)
                                                    $texto_celula .= '
                                                        <form method="POST" action="atribuicao_aulas.php" class="in-cell-alocacao-form">
                                                            <input type="hidden" name="acao" value="salvar_aula">
                                                            <input type="hidden" name="modal_dia" value="' . $dia_str . '">
                                                            <input type="hidden" name="modal_horario" value="' . $horario_str . '">
                                                            <input type="hidden" name="modo_visualizacao" value="' . $modo_visualizacao . '">
                                                            <input type="hidden" name="modal_id_turma" value="' . $turma_id_para_edicao . '">
                                                            
                                                            <select name="modal_id_disciplina" class="in-cell-select" onchange="this.form.submit()" ' . $disabled_attr . '>
                                                                <option value="0">Disciplina</option>';
                                                                 foreach ($disciplinas as $disciplina) {
                                                                     $selected = ($current_disc_id == $disciplina['id_disc']) ? 'selected' : ''; 
                                                                     $texto_celula .= '<option value="' . $disciplina['id_disc'] . '" ' . $selected . '>' . htmlspecialchars($disciplina['nome']) . '</option>';
                                                                 }
                                                                 $texto_celula .= '
                                                            </select>
                                                            
                                                            <select name="modal_id_prof" class="in-cell-select" onchange="this.form.submit()" ' . $disabled_attr . '>
                                                                <option value="0">Professor</option>';
                                                                 foreach ($professores as $prof) {
                                                                     $selected = ($current_prof_id == $prof['id_prof']) ? 'selected' : '';
                                                                     $texto_celula .= '<option value="' . $prof['id_prof'] . '" ' . $selected . '>' . htmlspecialchars($prof['nome']) . '</option>';
                                                                 }
                                                                 $texto_celula .= '
                                                            </select>
                                                        </form>
                                                        ';
                                                    
                                                    // 4. Se alocada, mostra a informação atual acima do formulário
                                                    if ($info_alocada) {
                                                        $info_html = '<span style="font-size: 0.7em; font-weight: bold; color: #155724;">' . htmlspecialchars($professores_map[$current_prof_id] ?? 'N/A') . '</span><br>';
                                                        $info_html .= '<span style="font-size: 0.7em; color: #48ab87;">' . htmlspecialchars($disciplinas_map[$current_disc_id] ?? 'N/A') . '</span><hr style="margin: 3px 0; border: 0; border-top: 1px dashed #ccc;">';
                                                        $texto_celula = $info_html . $texto_celula;
                                                    }
                                                ?>
                                                    <td class="<?php echo $css_class; ?>"
                                                        data-dia="<?php echo $dia_str; ?>"
                                                        data-horario="<?php echo $horario_str; ?>"
                                                        data-turma-id="<?php echo $turma_id_para_edicao; ?>">
                                                        <?php echo $texto_celula; ?>
                                                    </td>
                                                <?php endforeach; // Fim Turma loop ?>
                                            </tr>
                                        <?php endforeach; // Fim Periodo loop ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endforeach; // Fim Dia loop ?>
                    <?php else: ?>
                        <div class="mensagem-erro">Não há turmas registradas. Registre turmas para usar o modo Geral.</div>
                    <?php endif; ?>

                <?php endif; // Fim Layout Geral ?>

            <?php endif; // Fim if ($pode_exibir_grade) ?>
        </div>
    </main>

    <script>
        // --- Funções de Filtro (JS) ---
        function toggleFiltros(modo) {
            // Esconde todos os filtros de modo
            document.getElementById('filtro-turma').style.display = 'none';
            document.getElementById('filtro-professor').style.display = 'none';

            // Mostra o filtro correto
            if (modo === 'turma') {
                document.getElementById('filtro-turma').style.display = 'block';
            } else if (modo === 'professor') {
                document.getElementById('filtro-professor').style.display = 'block';
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Inicializa a visibilidade do filtro correto na carga da página
            const modoVisualizacao = document.getElementById('modo').value;
            toggleFiltros(modoVisualizacao);
            
            // Adiciona o listener para atualizar a visibilidade do filtro ao mudar o modo
            document.getElementById('modo').addEventListener('change', function() {
                toggleFiltros(this.value);
            });
        });
    </script>
</body>
</html>