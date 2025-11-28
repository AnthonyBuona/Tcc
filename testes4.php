<?php
session_start();
// Inclui a conexão com o banco de dados.
include 'includes/config.php';

// --- CONFIGURAÇÃO DE CAMINHO ---
define('BASE_PATH', '/proj_site/testes4.php'); 

$tabela_alocacao = 'horario'; 
$tabela_periodo = 'periodo_aula'; 

// --- Configuração de Limite (CORRIGIDO) ---
define('LIMITE_AULAS_PROFESSOR_DIARIO', 8);     // Limite MÁXIMO de aulas por dia
define('LIMITE_AULAS_PROFESSOR_SEMANAL', 30);  // Limite MÁXIMO de aulas na semana (AJUSTE ESTE VALOR)

$mensagem_sucesso = '';
$mensagem_erro = '';

// Função auxiliar para redirecionamento
function redirect($sucesso, $erro) {
    header("Location: " . BASE_PATH . "?msg_sucesso=" . urlencode($sucesso) . "&msg_erro=" . urlencode($erro));
    exit();
}

// Verifica mensagens passadas via GET
if (isset($_GET['msg_sucesso'])) { $mensagem_sucesso = htmlspecialchars($_GET['msg_sucesso']); }
if (isset($_GET['msg_erro'])) { $mensagem_erro = htmlspecialchars($_GET['msg_erro']); }


// --- DEFINIÇÕES DE DIAS ---
$dias_semana = ['Segunda', 'Terça', 'Quarta', 'Quinta', 'Sexta'];

// --- 1. CARREGAR PERIODOS DE AULA DO BANCO DE DADOS ---
$periodos_aula = [];
$sql_periodos = "SELECT id_periodo, horario AS horario_str FROM {$tabela_periodo} ORDER BY id_periodo ASC";
$result_periodos = mysqli_query($conexao, $sql_periodos);

if ($result_periodos && mysqli_num_rows($result_periodos) > 0) {
    $periodos_aula = mysqli_fetch_all($result_periodos, MYSQLI_ASSOC);
    mysqli_free_result($result_periodos);
} else {
    // FALLBACK CRÍTICO (Apenas se a tabela 'periodo_aula' for inacessível)
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
    $mensagem_erro .= (empty($mensagem_erro) ? '' : '<br>') . "Aviso: A tabela **`periodo_aula`** está vazia ou inacessível. Usando horários estáticos.";
}
$mapa_id_to_periodo_str = array_column($periodos_aula, 'horario_str', 'id_periodo');


// --------------------------------------------------------
// --- 2. CARREGAMENTO DE DADOS E REGRAS (Pré-Cálculos) ---
// --------------------------------------------------------

$professores = [];
$disciplinas = [];
$turmas = [];

// Usando ALIAS para carregar a coluna 'areas' do professor como 'id_area'
$result_prof = mysqli_query($conexao, "SELECT id_prof, nome, areas AS id_area FROM professor ORDER BY nome ASC");
if ($result_prof) { $professores = mysqli_fetch_all($result_prof, MYSQLI_ASSOC); mysqli_free_result($result_prof); } else { $mensagem_erro .= "<br>Erro ao carregar Professores: " . $conexao->error; }

// Usando ALIAS para carregar a coluna 'area' da disciplina como 'id_area'
$result_disc = mysqli_query($conexao, "SELECT id_disc, nome_disc AS nome, area AS id_area FROM disciplina ORDER BY nome_disc ASC");
if ($result_disc) { $disciplinas = mysqli_fetch_all($result_disc, MYSQLI_ASSOC); mysqli_free_result($result_disc); } else { $mensagem_erro .= "<br>Erro ao carregar Disciplinas: " . $conexao->error; }

$result_turma = mysqli_query($conexao, "SELECT id_turma, nome_turma FROM turma ORDER BY nome_turma ASC");
if ($result_turma) { $turmas = mysqli_fetch_all($result_turma, MYSQLI_ASSOC); mysqli_free_result($result_turma); } else { $mensagem_erro .= "<br>Erro ao carregar Turmas: " . $conexao->error; }

// Mapear IDs para nomes e áreas
$turmas_map = array_column($turmas, 'nome_turma', 'id_turma');
$professores_map = array_column($professores, 'nome', 'id_prof');
$disciplinas_map = array_column($disciplinas, 'nome', 'id_disc'); 

// Mapeamento de Áreas (strings)
$professores_area_map = array_column($professores, 'id_area', 'id_prof');
$disciplinas_area_map = array_column($disciplinas, 'id_area', 'id_disc');

// NOVO: Carregar Restrições de Professor (Horários que NÃO PODE dar aula)
$restricoes_prof_map = []; // [IDPROF_DIA_IDPERIODO] => true
$sql_restricoes = "SELECT id_prof, dia, id_periodo FROM restricao_prof"; 
$result_restricoes = mysqli_query($conexao, $sql_restricoes);
if ($result_restricoes) {
    while ($row = mysqli_fetch_assoc($result_restricoes)) {
        // CORREÇÃO APLICADA AQUI: Garante que a chave seja formada por strings para consistência
        $key = (string)$row['id_prof'] . '_' . $row['dia'] . '_' . (string)$row['id_periodo'];
        $restricoes_prof_map[$key] = true;
    }
}

// NOVO: Carregar Carga Horária Necessária por Turma/Disciplina
$carga_horaria_necessaria = []; // [id_turma][id_disc] => carga_total
$sql_carga = "SELECT id_turma, id_disc, carga_total FROM carga_horaria"; 
$result_carga = mysqli_query($conexao, $sql_carga);
if ($result_carga) {
    while ($row = mysqli_fetch_assoc($result_carga)) {
        $carga_horaria_necessaria[$row['id_turma']][(int)$row['id_disc']] = (int)$row['carga_total'];
    }
}


// Carrega Grade para visualização e pré-cálculos (DEVE SER FEITO ANTES DO POST)
$grade_visualizada = []; // [DIA_IDPERIODO_IDTURMA] => array de alocação
$aulas_por_professor = []; // [id_prof] => total_aulas (SEMANAL)
$aulas_alocadas = []; // [id_turma][id_disc] => total_alocadas
$aulas_por_dia_prof = []; // [id_prof][dia] => total_aulas_dia

$sql_aulas = "
    SELECT h.dia, h.id_periodo, h.id_prof, h.id_disc AS id_disciplina, h.id_turma
    FROM {$tabela_alocacao} h
";
$stmt_aulas = $conexao->prepare($sql_aulas);
if ($stmt_aulas) {
    $stmt_aulas->execute();
    $result_aulas = $stmt_aulas->get_result();
    while ($row = $result_aulas->fetch_assoc()) {
        $row['id_prof'] = (int)$row['id_prof'];
        $row['id_disciplina'] = (int)$row['id_disciplina'];
        $row['id_turma'] = (int)$row['id_turma'];
        $row['id_periodo'] = (int)$row['id_periodo'];

        $key = $row['dia'] . '_' . $row['id_periodo'] . '_' . $row['id_turma'];
        $grade_visualizada[$key] = $row;
        
        // Pré-cálculos
        $aulas_por_professor[$row['id_prof']] = ($aulas_por_professor[$row['id_prof']] ?? 0) + 1; // SEMANAL
        $aulas_alocadas[$row['id_turma']][$row['id_disciplina']] = ($aulas_alocadas[$row['id_turma']][$row['id_disciplina']] ?? 0) + 1;
        $aulas_por_dia_prof[$row['id_prof']][$row['dia']] = ($aulas_por_dia_prof[$row['id_prof']][$row['dia']] ?? 0) + 1; // DIÁRIO
    }
    $stmt_aulas->close();
}

// Cria uma cópia para rastrear mudanças no POST
$aulas_por_professor_post = $aulas_por_professor;
$aulas_alocadas_post = $aulas_alocadas;
$aulas_por_dia_prof_post = $aulas_por_dia_prof;

// Rastreia o estado da grade durante o POST (AGORA INCLUINDO CHAVES DE TURMAS NO NÍVEL MAIS ALTO PARA CHECAGEM)
// [DIA][IDPERIODO][IDTURMA] => [IDPROF]
$dynamic_grade_prof_state = []; 
foreach($grade_visualizada as $key => $alocacao) {
    list($dia, $id_periodo, $id_turma) = explode('_', $key);
    $dynamic_grade_prof_state[$dia][(int)$id_periodo][(int)$id_turma] = (int)$alocacao['id_prof'];
}


// ------------------------------------------
// --- LÓGICA DE SALVAR TUDO (POST) ---
// ------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao']) && $_POST['acao'] === 'salvar_grade' && isset($_POST['aulas'])) {
    
    $dados_enviados = $_POST['aulas'];
    $dia_focado = $_POST['dia_salvo'] ?? null; // NOVO: Pega o dia que foi submetido
    
    // Se o dia não foi fornecido, algo está errado, ou está tentando salvar tudo.
    if (!in_array($dia_focado, $dias_semana) && count($dias_semana) > 0) {
         // Se você tivesse um botão Salvar Geral, ele viria para cá.
         // Mas como a requisição veio de um botão de dia, vamos forçar a falha se o dia for inválido.
         $mensagem_erro = "Erro: Dia da semana inválido fornecido para salvamento.";
         redirect('', $mensagem_erro);
    }
    
    $sucesso_count = 0;
    $erro_count = 0;
    
    // ID_SALA dummy
    $id_sala_dummy = 1;

    // Prepara as statements fora do loop para otimização
    $sql_replace = "REPLACE INTO {$tabela_alocacao} (id_disc, id_periodo, id_prof, id_turma, dia, id_sala) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt_replace = $conexao->prepare($sql_replace);

    $sql_delete = "DELETE FROM {$tabela_alocacao} WHERE id_turma = ? AND id_periodo = ? AND dia = ?";
    $stmt_delete = $conexao->prepare($sql_delete);

    if (!$stmt_replace || !$stmt_delete) {
        $mensagem_erro = "Erro de preparação de SQL: " . $conexao->error;
        redirect('', $mensagem_erro);
    }
    
    // Processa apenas as aulas que pertencem ao dia submetido
    foreach ($dados_enviados as $key => $valores) {
        list($dia, $id_periodo_str, $id_turma_str) = explode('_', $key);
        
        // NOVO: Processa SOMENTE os dados do dia que o usuário clicou em salvar
        if ($dia !== $dia_focado) {
            continue;
        }
        
        $id_periodo = (int)$id_periodo_str;
        $id_turma = (int)$id_turma_str;
        
        $id_disciplina = (int)($valores['id_disc'] ?? 0); 
        $id_prof = (int)($valores['id_prof'] ?? 0);
        $info_antiga = $grade_visualizada[$key] ?? null; // Usa a grade original (carregada antes do POST) para calcular descontos
        $id_prof_antigo = $info_antiga['id_prof'] ?? 0;
        $id_disc_antiga = $info_antiga['id_disciplina'] ?? 0;
        
        // --- 1. AJUSTE DA CONTAGEM (Prepara a contagem ANTES da gravação) ---
        // Se havia uma aula (disc antiga > 0)
        if ($id_disc_antiga > 0) {
            // Desconta a aula antiga da contagem da disciplina
            $aulas_alocadas_post[$id_turma][$id_disc_antiga] = max(0, ($aulas_alocadas_post[$id_turma][$id_disc_antiga] ?? 1) - 1);

            if ($id_prof_antigo > 0) {
                // Desconta do total SEMANAL do professor
                $aulas_por_professor_post[$id_prof_antigo] = max(0, ($aulas_por_professor_post[$id_prof_antigo] ?? 1) - 1);
                // Desconta do limite DIÁRIO do professor
                $aulas_por_dia_prof_post[$id_prof_antigo][$dia] = max(0, ($aulas_por_dia_prof_post[$id_prof_antigo][$dia] ?? 1) - 1);
            }
        }
        
        
        // --- 2. REMOÇÃO (Disciplina = 0) ---
        if ($id_disciplina === 0) {
            if ($info_antiga) { 
                 $stmt_delete->bind_param("iis", $id_turma, $id_periodo, $dia); 
                 if ($stmt_delete->execute()) {
                     $sucesso_count++;
                     // ATUALIZA O ESTADO DINÂMICO APÓS DELETE
                     unset($dynamic_grade_prof_state[$dia][$id_periodo][$id_turma]);
                 } else {
                     $erro_count++;
                     $mensagem_erro .= "<br>Erro ao remover {$turmas_map[$id_turma]} em {$dia}: " . $stmt_delete->error;
                 }
            }
        } 
        // --- 3. ALOCAÇÃO/ATUALIZAÇÃO (Disciplina > 0) ---
        else {
            if ($id_prof === 0) {
                 $erro_count++;
                 $mensagem_erro .= "<br>Erro: Professor não selecionado para {$turmas_map[$id_turma]} em {$dia} com a disciplina {$disciplinas_map[$id_disciplina]}.";
                 continue; 
            }
            
            // --- VALIDAÇÕES RÍGIDAS (Bloqueio) ---
            
            // a) Conflito de Sala/Horário (O professor já está em outra turma neste slot?)
            $conflito_simultaneo = false;
            $t_nome_check = '';
            
            if (isset($dynamic_grade_prof_state[$dia][$id_periodo])) {
                foreach ($dynamic_grade_prof_state[$dia][$id_periodo] as $t_id_check => $prof_id_check) {
                    // Se o professor checado for o que está sendo alocado E a turma for diferente
                    // (Esta célula é a única que está sendo alterada, então verifica se o professor
                    // está em outra turma, no mesmo horário, que JÁ FOI ALOCADA ANTES DESTE POST.)
                    if ((int)$prof_id_check === $id_prof && (int)$t_id_check !== $id_turma) {
                        $conflito_simultaneo = true;
                        $t_nome_check = $turmas_map[$t_id_check] ?? 'Turma Desconhecida';
                        break;
                    }
                }
            }


            // b) Restrição de Horário (O professor tem restrição neste slot?)
            $restricao_key = (string)$id_prof . '_' . $dia . '_' . (string)$id_periodo;
            $tem_restricao = isset($restricoes_prof_map[$restricao_key]);

            // c) Limite de Aulas no Dia (8 aulas)
            // Usa a contagem dinâmica (aulas_por_dia_prof_post) que já teve o valor antigo descontado
            $aulas_proj_dia = ($aulas_por_dia_prof_post[$id_prof][$dia] ?? 0) + 1; 
            $limite_diario_excedido = $aulas_proj_dia > LIMITE_AULAS_PROFESSOR_DIARIO; 

            // d) Limite Total Semanal
            // Usa a contagem dinâmica (aulas_por_professor_post) que já teve o valor antigo descontado
            $aulas_proj_semanal = ($aulas_por_professor_post[$id_prof] ?? 0) + 1; 
            $limite_semanal_excedido = $aulas_proj_semanal > LIMITE_AULAS_PROFESSOR_SEMANAL; 

            // --- BLOQUEIOS ---
            if ($conflito_simultaneo) {
                 $erro_count++;
                 $mensagem_erro .= "<br>BLOQUEIO: Professor **{$professores_map[$id_prof]}** já está alocado em outra turma neste mesmo horário ({$dia} / {$mapa_id_to_periodo_str[$id_periodo]}) na turma **{$t_nome_check}**.";
                 continue;
            }
            if ($tem_restricao) {
                 $erro_count++;
                 $mensagem_erro .= "<br>BLOQUEIO: Professor **{$professores_map[$id_prof]}** tem uma restrição de horário cadastrada para {$dia} / {$mapa_id_to_periodo_str[$id_periodo]}.";
                 continue;
            }
            if ($limite_diario_excedido) {
                 $erro_count++;
                 $mensagem_erro .= "<br>BLOQUEIO: Professor **{$professores_map[$id_prof]}** excederá o limite de aulas por dia (Serão {$aulas_proj_dia} aulas em {$dia}. Limite: " . LIMITE_AULAS_PROFESSOR_DIARIO . ").";
                 continue;
            }
            if ($limite_semanal_excedido) { 
                 $erro_count++;
                 $mensagem_erro .= "<br>BLOQUEIO: Professor **{$professores_map[$id_prof]}** excederá o limite TOTAL de aulas na semana (Serão {$aulas_proj_semanal} aulas. Limite: " . LIMITE_AULAS_PROFESSOR_SEMANAL . ").";
                 continue;
            }


            // --- EXECUÇÃO ---
            $stmt_replace->bind_param("iiiisi", $id_disciplina, $id_periodo, $id_prof, $id_turma, $dia, $id_sala_dummy); 
            
            if ($stmt_replace->execute()) {
                $sucesso_count++;
                
                // --- 4. ATUALIZA A CONTAGEM PÓS-GRAVAÇÃO ---
                // Nota: Aulas Semanal e Diária são atualizadas para a contagem 'post' APENAS se o REPLACE for bem-sucedido.
                $aulas_por_professor_post[$id_prof] = $aulas_proj_semanal; // ATUALIZA SEMANAL
                $aulas_alocadas_post[$id_turma][$id_disciplina] = ($aulas_alocadas_post[$id_turma][$id_disciplina] ?? 0) + 1;
                $aulas_por_dia_prof_post[$id_prof][$dia] = $aulas_proj_dia; // ATUALIZA DIÁRIO

                // ATUALIZA O ESTADO DINÂMICO DA GRADE (NOVO FORMATO) APÓS REPLACE
                $dynamic_grade_prof_state[$dia][$id_periodo][$id_turma] = $id_prof;

            } else {
                $erro_count++;
                $mensagem_erro .= "<br>Erro ao alocar {$turmas_map[$id_turma]} em {$dia}: " . $stmt_replace->error;
            }
        }
    }
    
    // Fechar statements e gerar mensagem final
    $stmt_replace->close();
    $stmt_delete->close();
    
    $mensagem_sucesso_final = '';
    $mensagem_erro_final = $mensagem_erro;
    
    if ($sucesso_count > 0) {
        $mensagem_sucesso_final = "Grade do dia **{$dia_focado}** salva! **{$sucesso_count}** alocações/atualizações concluídas com sucesso. ";
        if ($erro_count > 0) {
             $mensagem_sucesso_final .= " (**{$erro_count}** erros/avisos foram encontrados, veja detalhes abaixo).";
        }
    } elseif ($erro_count > 0) {
         $mensagem_erro_final = "Nenhuma alteração salva com sucesso. **{$erro_count}** erros/avisos encontrados para {$dia_focado}. Detalhes: " . $mensagem_erro;
    } else {
         $mensagem_sucesso_final = "Nenhuma alteração foi realizada na grade do dia **{$dia_focado}**.";
    }

    // --- PONTO DE REDIRECIONAMENTO ---
    redirect($mensagem_sucesso_final, $mensagem_erro_final);
}
// --- FIM LÓGICA DE SALVAR TUDO ---
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>PlanIt - Admin | Atribuição de Aulas (Geral)</title>
    <link rel="stylesheet" href="css/dashboard.css" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" crossorigin="anonymous" />
    <style>
        /* Estilos CSS */
        .grade-atribuicao { width: 100%; border-collapse: collapse; margin-top: 20px; background-color: rgba(255, 255, 255, 0.9); border-radius: 12px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,.1); }
        .grade-atribuicao th, .grade-atribuicao td { padding: 8px 10px; text-align: center; border: 1px solid #e5e7eb; vertical-align: middle; }
        .grade-atribuicao th { background-color: #48ab87; color: #fff; }
        .in-cell-slot { padding: 5px; height: 90px; min-width: 120px; cursor: default; }
        .in-cell-slot.alocada { background-color: #d4edda; }
        .in-cell-alocacao-form { display: flex; flex-direction: column; gap: 3px; width: 100%; }
        .in-cell-select { width: 100%; padding: 2px; font-size: 0.8em; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; background-color: #fff; min-height: 20px; }
        .save-button-area { margin-top: 20px; padding: 15px; background-color: #f7f7f7; border-radius: 8px; text-align: right; }
        .save-button { padding: 10px 20px; font-size: 1em; background-color: #48ab87; color: white; border: none; border-radius: 6px; cursor: pointer; transition: background-color 0.3s; }
        .save-button:hover { background-color: #3a8a6b; }
        .mensagem-sucesso { background-color: #d4edda; color: #155724; padding: 10px; border: 1px solid #c3e6cb; border-radius: 4px; margin-bottom: 15px; }
        .mensagem-erro { background-color: #f8d7da; color: #721c24; padding: 10px; border: 1px solid #f5c6cb; border-radius: 4px; margin-bottom: 15px; }
        .professor-tag { display: none; } 
        /* Estilo para alertas visuais */
        .select-alerta-dia { background-color: #ffc; color: #aa0; font-weight: bold; }
        .select-bloqueado-conflito { background-color: #fdd; color: #900; font-weight: bold; }
        .card-alerta { border-left: 5px solid #d9534f; }
        .list-alerta { list-style: none; margin: 0; padding: 0; }
        .list-alerta li { margin-bottom: 5px; background-color: #f2dede; padding: 8px; border-radius: 4px; border-left: 3px solid #ebccd1; }
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
                    <li style="background-color: rgba(72,171,135,0.9);"><a href="<?php echo BASE_PATH; ?>" class="menu-active" style="padding: 10px 20px; display: block; color: #fff; text-decoration: none; border-radius: 8px; font-weight: 500;">Atribuir Aulas</a></li>
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
                <h1>Atribuição de Aulas (Geral)</h1>
            </div>

            <?php if (!empty($mensagem_sucesso)): ?>
                <div class="mensagem-sucesso"><?php echo $mensagem_sucesso; ?></div>
            <?php endif; ?>

            <?php if (!empty($mensagem_erro)): ?>
                <div class="mensagem-erro"><?php echo $mensagem_erro; ?></div>
            <?php endif; ?>
            
            <div class="card card-alerta" style="margin-top: 20px;">
                <h3>⚠️ Alertas de Carga Horária</h3>
                <ul class="list-alerta">
                    <?php 
                    $total_alertas = 0;
                    foreach ($carga_horaria_necessaria as $id_turma => $cargas_disc) {
                        $nome_turma = $turmas_map[$id_turma] ?? 'Turma Desconhecida';
                        foreach ($cargas_disc as $id_disc => $carga_necessaria) {
                            $nome_disc = $disciplinas_map[$id_disc] ?? 'Disciplina Desconhecida';
                            $aulas_alocadas_final = $aulas_alocadas[$id_turma][$id_disc] ?? 0;
                            
                            $pendencia = $carga_necessaria - $aulas_alocadas_final;
                            
                            if ($pendencia > 0) {
                                echo "<li>**{$nome_turma}** / **{$nome_disc}**: Faltam **{$pendencia}** aula(s) de {$carga_necessaria} necessárias.</li>";
                                $total_alertas++;
                            }
                        }
                    }
                    if ($total_alertas === 0) {
                        echo "<li>Nenhuma pendência de carga horária encontrada.</li>";
                    }
                    ?>
                </ul>
            </div>
            <div class="card">
            </div>
            
            <?php if (!empty($turmas)): ?>
            
                <?php 
                foreach ($dias_semana as $dia_str): 
                ?>
                    <div class="card" style="margin-top: 20px; overflow-x: auto;">
                        <h3><?php echo "{$dia_str}"; ?></h3>
                        
                        <form method="POST" action="<?php echo BASE_PATH; ?>" style="margin-bottom: 20px;">
                            <input type="hidden" name="acao" value="salvar_grade">
                            <input type="hidden" name="dia_salvo" value="<?php echo $dia_str; ?>"> <table class="grade-atribuicao">
                                <thead>
                                    <tr>
                                        <th style="min-width: 120px;">Horário</th>
                                        <?php foreach ($turmas as $turma): ?>
                                            <th style="min-width: 180px;"><?php echo htmlspecialchars($turma['nome_turma']); ?></th>
                                        <?php endforeach; ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($periodos_aula as $periodo): ?>
                                        <tr>
                                            <td><?php echo $periodo['horario_str']; ?></td>
                                            <?php 
                                            $id_periodo = $periodo['id_periodo'];
                                            
                                            foreach ($turmas as $turma): 
                                                $id_turma_coluna = $turma['id_turma'];
                                                // Chave única para este slot
                                                $key = $dia_str . '_' . $id_periodo . '_' . $id_turma_coluna; 
                                                $info_alocada = $grade_visualizada[$key] ?? null;
                                                
                                                $current_disc_id = (int)($info_alocada['id_disciplina'] ?? 0);
                                                $current_prof_id = (int)($info_alocada['id_prof'] ?? 0);
                                                $css_class = 'in-cell-slot' . ($info_alocada ? ' alocada' : '');
                                                
                                                $name_disc = "aulas[{$key}][id_disc]";
                                                $name_prof = "aulas[{$key}][id_prof]";
                                                
                                                // 1. Lógica de Disciplinas (Filtro por Carga Horária)
                                                $texto_disciplina_options = '<option value="0" data-area="">Disciplina</option>';
                                                foreach ($disciplinas as $disciplina) {
                                                    $id_disc = (int)$disciplina['id_disc'];
                                                    $nome_disc = htmlspecialchars($disciplina['nome']);
                                                    $selected = ($current_disc_id === $id_disc) ? 'selected' : '';
                                                    
                                                    $carga_necessaria = $carga_horaria_necessaria[$id_turma_coluna][$id_disc] ?? 0;
                                                    $aulas_atuais = $aulas_alocadas[$id_turma_coluna][$id_disc] ?? 0;
                                                    
                                                    // Se a carga foi atingida E a disciplina NÃO é a que está atualmente alocada
                                                    if ($carga_necessaria > 0 && $aulas_atuais >= $carga_necessaria && $id_disc !== $current_disc_id) {
                                                        continue; 
                                                    }
                                                    
                                                    $meta = $carga_necessaria > 0 ? " ({$aulas_atuais}/{$carga_necessaria})" : '';
                                                    
                                                    $disc_area_data = htmlspecialchars($disciplina['id_area'] ?? ''); // Adiciona data-area
                                                    $texto_disciplina_options .= '<option value="' . $id_disc . '" ' . $selected . ' data-area="' . $disc_area_data . '">' . $nome_disc . $meta . '</option>';
                                                }

                                                // 2. Lógica de Professores (Restrições, Conflito/Dia/Semana)
                                                $texto_professor_options = '<option value="0" data-area="" ' . (($current_prof_id === 0 && $current_disc_id > 0) ? 'style="color:red; font-weight:bold;"' : '') . '>Professor</option>';

                                                // Obter estado atual da grade visualizada (DB) ou o estado dinâmico (POST) que está sendo visualizado
                                                $professores_alocados_no_slot = $dynamic_grade_prof_state[$dia_str][$id_periodo] ?? [];

                                                foreach ($professores as $prof) {
                                                    $id_prof_option = (int)$prof['id_prof'];
                                                    $nome_prof = htmlspecialchars($prof['nome']);
                                                    $selected = ($current_prof_id === $id_prof_option) ? 'selected' : '';
                                                    $style_prof = '';
                                                    $data_attributes = ''; 

                                                    // --- VALIDAÇÃO DE CONFLITO SIMULTÂNEO (Visual) ---
                                                    $prof_em_conflito_agora = false;
                                                    $turma_conflito_nome = '';

                                                    foreach ($professores_alocados_no_slot as $t_id_check => $prof_id_check) {
                                                        // Se o professor checado for o que está sendo alocado E a turma for diferente
                                                        if ((int)$prof_id_check === $id_prof_option && (int)$t_id_check !== $id_turma_coluna) {
                                                            $prof_em_conflito_agora = true;
                                                            $turma_conflito_nome = $turmas_map[$t_id_check] ?? 'Outra Turma';
                                                            break;
                                                        }
                                                    }
                                                    
                                                    if ($prof_em_conflito_agora && $current_prof_id !== $id_prof_option) {
                                                        $nome_prof .= " (CONFLITO C/ {$turma_conflito_nome}!)";
                                                        $style_prof = 'class="select-bloqueado-conflito"';
                                                        $data_attributes .= ' data-conflict="simultaneous"';
                                                    }

                                                    // --- VALIDAÇÃO DE RESTRIÇÃO DE HORÁRIO ---
                                                    $restricao_key = (string)$id_prof_option . '_' . $dia_str . '_' . (string)$id_periodo;
                                                    if (isset($restricoes_prof_map[$restricao_key]) && $current_prof_id !== $id_prof_option) {
                                                        $nome_prof .= " (RESTRIÇÃO DE HORÁRIO!)";
                                                        $style_prof = 'class="select-bloqueado-conflito"';
                                                        $data_attributes .= ' data-restriction="true"';
                                                    }


                                                    // --- VALIDAÇÃO DE LIMITE DE AULAS NO DIA (8 aulas) ---
                                                    $aulas_no_dia_existentes = $aulas_por_dia_prof[$id_prof_option][$dia_str] ?? 0;
                                                    if ($current_prof_id === $id_prof_option) {
                                                        $aulas_no_dia_existentes = max(0, $aulas_no_dia_existentes - 1);
                                                    }
                                                    $aulas_proj_dia = $aulas_no_dia_existentes + 1; 

                                                    if ($aulas_proj_dia > LIMITE_AULAS_PROFESSOR_DIARIO && $current_prof_id !== $id_prof_option) { 
                                                        $nome_prof .= " (LIMITE DIÁRIO EXCEDIDO: {$aulas_no_dia_existentes}+1/" . LIMITE_AULAS_PROFESSOR_DIARIO . "!)";
                                                        $style_prof = 'class="select-alerta-dia"';
                                                        $data_attributes .= ' data-limit-day="exceeded"';
                                                    }
                                                    
                                                    // --- VALIDAÇÃO DE LIMITE DE AULAS NA SEMANA (NOVO) ---
                                                    $aulas_na_semana_existentes = $aulas_por_professor[$id_prof_option] ?? 0;
                                                    if ($current_prof_id === $id_prof_option) {
                                                        $aulas_na_semana_existentes = max(0, $aulas_na_semana_existentes - 1);
                                                    }
                                                    $aulas_proj_semanal = $aulas_na_semana_existentes + 1;
                                                    
                                                    if ($aulas_proj_semanal > LIMITE_AULAS_PROFESSOR_SEMANAL && $current_prof_id !== $id_prof_option) {
                                                        $nome_prof .= " (LIMITE SEMANAL EXCEDIDO: {$aulas_na_semana_existentes}+1/" . LIMITE_AULAS_PROFESSOR_SEMANAL . "!)";
                                                        $style_prof = 'class="select-bloqueado-conflito"'; 
                                                        $data_attributes .= ' data-limit-week="exceeded"'; 
                                                    }


                                                    $texto_professor_options .= '<option value="' . $id_prof_option . '" ' . $selected . ' data-area="' . htmlspecialchars($prof['id_area'] ?? '') . '" ' . $style_prof . $data_attributes . '>' . $nome_prof . '</option>';
                                                }
                                            ?>
                                            <td class="<?php echo $css_class; ?>">
                                                <div class="in-cell-alocacao-form">
                                                    <select 
                                                        name="<?php echo $name_disc; ?>" 
                                                        id="disc_<?php echo $key; ?>" 
                                                        class="in-cell-select disc-select" 
                                                        data-key="<?php echo $key; ?>" 
                                                        data-turma="<?php echo $id_turma_coluna; ?>"
                                                    >
                                                        <?php echo $texto_disciplina_options; ?>
                                                    </select>
                                                    
                                                    <select 
                                                        name="<?php echo $name_prof; ?>" 
                                                        id="prof_<?php echo $key; ?>" 
                                                        class="in-cell-select prof-select"
                                                        data-key="<?php echo $key; ?>"
                                                        data-dia="<?php echo $dia_str; ?>"
                                                        data-periodo="<?php echo $id_periodo; ?>"
                                                    >
                                                        <?php echo $texto_professor_options; ?>
                                                    </select>
                                                </div>
                                            </td>
                                            <?php endforeach; ?>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                            
                            <div class="save-button-area" style="text-align: center; background-color: #f0fff0; border: 1px solid #d4edda;">
                                <button type="submit" class="save-button">💾 Salvar Grade de <?php echo $dia_str; ?></button>
                            </div>
                        </form>
                    </div>
                <?php endforeach; ?>
                
            <?php endif; ?>
            
            <div style="height: 50px;"></div> </div>
    </main>

    </body>
</html>