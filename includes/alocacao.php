<?php
session_start();
// O arquivo 'includes/config.php' é necessário para a conexão ($conexao)
include 'includes/config.php';

// --- DEFINIÇÕES DE VARIÁVEIS GLOBAIS ---
$tabela_alocacao = 'horario';
$mensagem_sucesso = '';
$mensagem_erro = '';
$dias_semana = ['Segunda', 'Terça', 'Quarta', 'Quinta', 'Sexta'];

// --- CARREGAR DADOS DO BANCO DE DADOS ---

// 1. Períodos de Aula
$periodos_aula = [];
$sql_periodos = "SELECT id_periodo, horario_str FROM periodo_aula ORDER BY id_periodo ASC";
$result_periodos = mysqli_query($conexao, $sql_periodos);
if ($result_periodos) {
    $periodos_aula = mysqli_fetch_all($result_periodos, MYSQLI_ASSOC);
    mysqli_free_result($result_periodos);
}
$mapa_periodos_str_to_id = array_column($periodos_aula, 'id_periodo', 'horario_str');

// 2. Professores, Disciplinas, Turmas e Salas
$professores = [];
$disciplinas = [];
$turmas = [];
$salas = [];

// Carregar Professores
$sql_prof = "SELECT id_prof, nome FROM professor ORDER BY nome ASC";
$result_prof = mysqli_query($conexao, $sql_prof);
if ($result_prof) { $professores = mysqli_fetch_all($result_prof, MYSQLI_ASSOC); mysqli_free_result($result_prof); }

// Carregar Disciplinas
$sql_disc = "SELECT id_disciplina, nome AS nome_disciplina, carga_horaria FROM disciplina ORDER BY nome_disciplina ASC";
$result_disc = mysqli_query($conexao, $sql_disc);
if ($result_disc) { $disciplinas = mysqli_fetch_all($result_disc, MYSQLI_ASSOC); mysqli_free_result($result_disc); }

// Carregar Turmas
$sql_turma = "SELECT id_turma, nome_turma FROM turma ORDER BY nome_turma ASC";
$result_turma = mysqli_query($conexao, $sql_turma);
if ($result_turma) { $turmas = mysqli_fetch_all($result_turma, MYSQLI_ASSOC); mysqli_free_result($result_turma); }

// Carregar Salas
$sql_sala = "SELECT id_sala, nome_sala FROM sala ORDER BY id_sala ASC";
$result_sala = mysqli_query($conexao, $sql_sala);
if ($result_sala) {
    $salas = mysqli_fetch_all($result_sala, MYSQLI_ASSOC);
    mysqli_free_result($result_sala);
}

// Mapeamentos para fácil consulta no HTML/JS
$turmas_map = array_column($turmas, 'nome_turma', 'id_turma');
$professores_map = array_column($professores, 'nome', 'id_prof');
$disciplinas_map = array_column($disciplinas, 'nome_disciplina', 'id_disciplina');
$salas_map = array_column($salas, 'nome_sala', 'id_sala');

// --- CARREGAR RELACIONAMENTOS DISCIPLINA-PROFESSOR PARA FILTRAGEM JS ---
$mapa_disc_prof_json = []; // [id_disc] = [id_prof1, id_prof2, ...]
$mapa_prof_disc_json = []; // [id_prof] = [id_disc1, id_disc2, ...]

$sql_rel = "SELECT DISTINCT id_disc, id_prof_padrao FROM relacionamento_disciplina";
$result_rel = mysqli_query($conexao, $sql_rel);
if ($result_rel) {
    while ($row = mysqli_fetch_assoc($result_rel)) {
        $id_disc = (int)$row['id_disc'];
        $id_prof = (int)$row['id_prof_padrao'];

        if (!isset($mapa_disc_prof_json[$id_disc])) $mapa_disc_prof_json[$id_disc] = [];
        $mapa_disc_prof_json[$id_disc][] = $id_prof;

        if (!isset($mapa_prof_disc_json[$id_prof])) $mapa_prof_disc_json[$id_prof] = [];
        $mapa_prof_disc_json[$id_prof][] = $id_disc;
    }
    mysqli_free_result($result_rel);
}

// Dados a serem injetados no JavaScript
$json_disc_prof = json_encode($mapa_disc_prof_json);
$json_prof_disc = json_encode($mapa_prof_disc_json);
$json_disciplinas = json_encode($disciplinas);
$json_professores = json_encode($professores);


// --- LÓGICA DE FILTRO E CARREGAMENTO DA GRADE ---
$modo_visualizacao = isset($_REQUEST['modo']) ? $_REQUEST['modo'] : 'turma';
$id_turma_selecionada = ($modo_visualizacao === 'turma') ? (isset($_REQUEST['id_turma']) ? (int)$_REQUEST['id_turma'] : 0) : 0;
$id_prof_selecionado = ($modo_visualizacao === 'professor') ? (isset($_REQUEST['id_prof']) ? (int)$_REQUEST['id_prof'] : 0) : 0;

$grade_visualizada = []; // Array que armazenará as aulas para exibição

$where_clause = "";
$bind_types = "";
$bind_params = [];

// 1. Definição do Filtro SQL
if ($modo_visualizacao === 'turma' && $id_turma_selecionada > 0) {
    $where_clause = "WHERE h.id_turma = ?";
    $bind_types = "i";
    $bind_params = [&$id_turma_selecionada];
} else if ($modo_visualizacao === 'professor' && $id_prof_selecionado > 0) {
    $where_clause = "WHERE h.id_prof = ?";
    $bind_types = "i";
    $bind_params = [&$id_prof_selecionado];
} else if ($modo_visualizacao === 'geral') {
    $where_clause = "";
    $bind_types = "";
    $bind_params = [];
}

// 2. Execução da Consulta (Se houver filtro válido ou modo geral)
if ($modo_visualizacao === 'geral' || ($where_clause !== "" && ($id_turma_selecionada > 0 || $id_prof_selecionado > 0))) {
    $sql_aulas = "
        SELECT
            h.dia, h.id_periodo, h.id_prof, h.id_disc, h.id_turma, h.id_sala,
            p.nome AS nome_prof, d.nome AS nome_disc, t.nome_turma,
            p_a.horario_str, s.nome_sala
        FROM {$tabela_alocacao} h
        JOIN professor p ON h.id_prof = p.id_prof
        JOIN disciplina d ON h.id_disc = d.id_disciplina
        JOIN turma t ON h.id_turma = t.id_turma
        JOIN periodo_aula p_a ON h.id_periodo = p_a.id_periodo
        LEFT JOIN sala s ON h.id_sala = s.id_sala
        {$where_clause}
    ";

    $stmt_aulas = $conexao->prepare($sql_aulas);

    if ($stmt_aulas) {
        if (!empty($bind_params)) {
            $stmt_aulas->bind_param($bind_types, ...$bind_params);
        }
        $stmt_aulas->execute();
        $result_aulas = $stmt_aulas->get_result();

        while ($row = $result_aulas->fetch_assoc()) {

            if ($modo_visualizacao === 'turma' || $modo_visualizacao === 'professor') {
                // Grade por Turma/Professor (simplificada)
                $key = $row['dia'] . '_' . $row['id_periodo'];
                $grade_visualizada[$key] = $row;
            } else {
                // Grade Geral (Sala x Horário)
                $dia = $row['dia'];
                $id_sala = $row['id_sala'];
                $id_periodo = $row['id_periodo'];

                if (!isset($grade_visualizada[$dia])) $grade_visualizada[$dia] = [];
                if (!isset($grade_visualizada[$dia][$id_sala])) $grade_visualizada[$dia][$id_sala] = [];
                $grade_visualizada[$dia][$id_sala][$id_periodo] = $row;
            }
        }
        $stmt_aulas->close();
    }
}


// --- LÓGICA DE SALVAR/REMOVER ATRIBUIÇÃO (POST) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao']) && $_POST['acao'] === 'salvar_aula') {

    // Extrai dados do POST
    $id_turma = (int)$_POST['modal_id_turma'];
    $id_disciplina = (int)$_POST['modal_id_disciplina'];
    $id_prof = (int)$_POST['modal_id_prof'];
    $dia = trim($_POST['modal_dia']);
    $horario_str = trim($_POST['modal_horario']);
    $modo_visualizacao_redirect = $_POST['modo_visualizacao'] ?? 'turma';

    $id_periodo = $mapa_periodos_str_to_id[$horario_str] ?? 0;
    $id_sala = 1; // Mantido como 1 (padrão)

    if ($id_turma > 0 && $id_periodo > 0) {

        if ($id_disciplina > 0 && $id_prof > 0) {
            // --- SALVAR/SUBSTITUIR (REPLACE) ---
            $sql_replace = "REPLACE INTO {$tabela_alocacao} (id_disc, id_periodo, id_prof, id_turma, dia, id_sala) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt_replace = $conexao->prepare($sql_replace);

            if ($stmt_replace) {
                $stmt_replace->bind_param("iiiisi", $id_disciplina, $id_periodo, $id_prof, $id_turma, $dia, $id_sala);
                if ($stmt_replace->execute()) {
                    $mensagem_sucesso = "Aula alocada/substituída com sucesso para {$dia} ({$horario_str}).";
                } else {
                    $mensagem_erro = "Erro ao alocar aula: " . $stmt_replace->error;
                }
                $stmt_replace->close();
            } else {
                $mensagem_erro = "Erro de preparação do REPLACE: " . $conexao->error;
            }

        } elseif ($id_disciplina == 0 && $id_prof == 0) {
            // --- REMOVER (DELETE): Apenas se ambos forem resetados para '0' ---
            $sql_delete = "DELETE FROM {$tabela_alocacao} WHERE id_turma = ? AND id_periodo = ? AND dia = ?";
            $stmt_delete = $conexao->prepare($sql_delete);

            if ($stmt_delete) {
                $stmt_delete->bind_param("iis", $id_turma, $id_periodo, $dia);
                if ($stmt_delete->execute()) {
                    $mensagem_sucesso = "Aula removida com sucesso para {$dia} ({$horario_str}).";
                } else {
                    $mensagem_erro = "Erro ao remover aula: " . $stmt_delete->error;
                }
                $stmt_delete->close();
            } else {
                $mensagem_erro = "Erro de preparação do DELETE: " . $conexao->error;
            }

        } else {
            // Erro se a seleção for incompleta
            $mensagem_erro = "Erro: Seleção incompleta. Para salvar, selecione Disciplina e Professor. Para remover, deixe ambos como '0'.";
        }
    } else {
        $mensagem_erro = "Erro: Turma ou Período inválidos.";
    }

    // Redireciona APÓS POST
    $redirect_id = ($modo_visualizacao_redirect === 'turma') ? "&id_turma={$id_turma}" : "&id_prof={$id_prof}";
    header("Location: alocacao.php?modo={$modo_visualizacao_redirect}{$redirect_id}&msg_sucesso=" . urlencode($mensagem_sucesso) . "&msg_erro=" . urlencode($mensagem_erro));
    exit();
}

// Verifica mensagens passadas via GET para exibição
if (isset($_GET['msg_sucesso'])) { $mensagem_sucesso = htmlspecialchars($_GET['msg_sucesso']); }
if (isset($_GET['msg_erro'])) { $mensagem_erro = htmlspecialchars($_GET['msg_erro']); }

// A partir daqui, você incluiria o arquivo HTML
include 'alocacao_view.php'; 
?>