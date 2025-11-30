<?php
session_start();
include 'includes/config.php';

// --- CONFIGURAÇÕES ---
define('BASE_PATH', 'testes4.php'); 
$tabela_alocacao = 'horario'; 
$tabela_periodo = 'periodo_aula'; 
$tabela_restricoes = 'disponibilidade_prof'; 

define('LIMITE_AULAS_PROFESSOR_DIARIO', 8);     
define('LIMITE_AULAS_PROFESSOR_SEMANAL', 30);  

$mensagem_sucesso = '';
$mensagem_erro = '';

// --- NOVA FUNÇÃO DE REDIRECIONAMENTO (USANDO SESSÃO) ---
function redirect($sucesso, $erro) {
    // Armazena na sessão para sobreviver ao redirect
    if (!empty($sucesso)) $_SESSION['flash_sucesso'] = $sucesso;
    if (!empty($erro)) $_SESSION['flash_erro'] = $erro;
    
    // Redireciona para a URL limpa (sem ?msg=...)
    header("Location: " . BASE_PATH);
    exit();
}

// --- RECUPERA E LIMPA MENSAGENS DA SESSÃO ---
if (isset($_SESSION['flash_sucesso'])) {
    $mensagem_sucesso = $_SESSION['flash_sucesso'];
    unset($_SESSION['flash_sucesso']); // Apaga para não mostrar de novo no F5
}
if (isset($_SESSION['flash_erro'])) {
    $mensagem_erro = $_SESSION['flash_erro'];
    unset($_SESSION['flash_erro']); // Apaga para não mostrar de novo no F5
}

$dias_semana = ['Segunda', 'Terça', 'Quarta', 'Quinta', 'Sexta'];

// --- 1. CARREGAR PERÍODOS ---
$periodos_aula = [];
$sql_periodos = "SELECT id_periodo, horario AS horario_str FROM {$tabela_periodo} ORDER BY id_periodo ASC";
$result_periodos = mysqli_query($conexao, $sql_periodos);

if ($result_periodos && mysqli_num_rows($result_periodos) > 0) {
    $periodos_aula = mysqli_fetch_all($result_periodos, MYSQLI_ASSOC);
}
$mapa_id_to_periodo_str = array_column($periodos_aula, 'horario_str', 'id_periodo');

// --- 2. CARREGAR DADOS BÁSICOS ---
$professores = [];
$res_prof = mysqli_query($conexao, "SELECT id_prof, nome, areas FROM professor WHERE status_aprovacao = 'APROVADO' ORDER BY nome ASC");
if ($res_prof) $professores = mysqli_fetch_all($res_prof, MYSQLI_ASSOC);
$professores_map = array_column($professores, 'nome', 'id_prof');

$disciplinas = [];
$res_disc = mysqli_query($conexao, "SELECT id_disc, nome_disc AS nome, area FROM disciplina ORDER BY nome_disc ASC");
if ($res_disc) $disciplinas = mysqli_fetch_all($res_disc, MYSQLI_ASSOC);
$disciplinas_map = array_column($disciplinas, 'nome', 'id_disc');

$turmas = [];
$res_turma = mysqli_query($conexao, "SELECT id_turma, nome_turma FROM turma ORDER BY nome_turma ASC");
if ($res_turma) $turmas = mysqli_fetch_all($res_turma, MYSQLI_ASSOC);
$turmas_map = array_column($turmas, 'nome_turma', 'id_turma');

// --- 3. CARREGAR RESTRIÇÕES E CARGAS ---
$restricoes_prof_map = [];
$res_rest = mysqli_query($conexao, "SELECT id_prof, dia, horario FROM {$tabela_restricoes}");
if ($res_rest) {
    while ($row = mysqli_fetch_assoc($res_rest)) {
        $key = (string)$row['id_prof'] . '_' . trim($row['dia']) . '_' . trim($row['horario']);
        $restricoes_prof_map[$key] = true;
    }
}

$carga_horaria_necessaria = [];
$res_carga = mysqli_query($conexao, "SELECT id_turma, id_disc, aulas_semanais FROM relacionamento_disciplina");
if ($res_carga) {
    while ($row = mysqli_fetch_assoc($res_carga)) {
        $carga_horaria_necessaria[$row['id_turma']][(int)$row['id_disc']] = (int)$row['aulas_semanais'];
    }
}

// --- 4. CARREGAR GRADE ATUAL (DO BANCO) ---
$grade_visualizada = [];
$aulas_por_prof = [];         
$aulas_por_dia_prof = [];     
$aulas_alocadas = [];         
$conflito_horario_map = []; 

$res_aulas = mysqli_query($conexao, "SELECT dia, id_periodo, id_prof, id_disc, id_turma FROM {$tabela_alocacao}");
if ($res_aulas) {
    while ($row = mysqli_fetch_assoc($res_aulas)) {
        $key = $row['dia'] . '_' . $row['id_periodo'] . '_' . $row['id_turma'];
        $grade_visualizada[$key] = $row;
        
        $p = (int)$row['id_prof'];
        $d = (int)$row['id_disc'];
        $t = (int)$row['id_turma'];
        $dia = $row['dia'];
        $per = (int)$row['id_periodo'];

        if ($p > 0) {
            $aulas_por_prof[$p] = ($aulas_por_prof[$p] ?? 0) + 1;
            $aulas_por_dia_prof[$p][$dia] = ($aulas_por_dia_prof[$p][$dia] ?? 0) + 1;
            $conflito_horario_map[$dia][$per][$t] = $p;
        }
        if ($d > 0) {
            $aulas_alocadas[$t][$d] = ($aulas_alocadas[$t][$d] ?? 0) + 1;
        }
    }
}

// ==========================================================
// --- 5. PROCESSAMENTO DO POST ---
// ==========================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao']) && $_POST['acao'] === 'salvar_grade') {
    $aulas_post = $_POST['aulas'] ?? [];
    $dia_focado = $_POST['dia_salvo'] ?? '';
    
    // --- CÁLCULO DE VALIDAÇÃO ---
    $contagem_disciplina_validacao = []; 
    $contagem_prof_semana_validacao = []; 
    $novas_aulas_prof_neste_dia = []; 
    
    // Soma aulas do banco (EXCETO O DIA ATUAL)
    foreach ($grade_visualizada as $key => $dados) {
        if ($dados['dia'] !== $dia_focado) {
            $t = (int)$dados['id_turma'];
            $d = (int)$dados['id_disc'];
            $p = (int)$dados['id_prof'];
            if ($d > 0) $contagem_disciplina_validacao[$t][$d] = ($contagem_disciplina_validacao[$t][$d] ?? 0) + 1;
            if ($p > 0) $contagem_prof_semana_validacao[$p] = ($contagem_prof_semana_validacao[$p] ?? 0) + 1;
        }
    }

    // Soma aulas do POST (O NOVO DIA ATUAL)
    foreach ($aulas_post as $key => $dados) {
        list($dia_p, $per_str, $turma_str) = explode('_', $key);
        if ($dia_p !== $dia_focado) continue; 

        $id_turma = (int)$turma_str;
        $id_disc = (int)($dados['id_disc'] ?? 0);
        $id_prof = (int)($dados['id_prof'] ?? 0);

        if ($id_disc > 0) {
            $contagem_disciplina_validacao[$id_turma][$id_disc] = ($contagem_disciplina_validacao[$id_turma][$id_disc] ?? 0) + 1;
        }
        if ($id_prof > 0) {
            $contagem_prof_semana_validacao[$id_prof] = ($contagem_prof_semana_validacao[$id_prof] ?? 0) + 1;
            $novas_aulas_prof_neste_dia[$id_prof] = ($novas_aulas_prof_neste_dia[$id_prof] ?? 0) + 1;
        }
    }

    // --- VALIDAÇÕES ---
    $pode_salvar = true;

    // 1. Limite de Carga Horária da Disciplina
    foreach ($contagem_disciplina_validacao as $t_id => $discs) {
        foreach ($discs as $d_id => $total_calc) {
            $limite = $carga_horaria_necessaria[$t_id][$d_id] ?? 999;
            if ($total_calc > $limite) {
                $nome_t = $turmas_map[$t_id] ?? $t_id;
                $nome_d = $disciplinas_map[$d_id] ?? $d_id;
                $mensagem_erro .= "Erro: <strong>$nome_d</strong> na turma <strong>$nome_t</strong> ultrapassou o limite ($total_calc/$limite). ";
                $pode_salvar = false;
            }
        }
    }

    // 2. Limites do Professor
    foreach ($novas_aulas_prof_neste_dia as $p_id => $qtd_dia) {
        if ($qtd_dia > LIMITE_AULAS_PROFESSOR_DIARIO) {
            $mensagem_erro .= "Prof. " . ($professores_map[$p_id]??$p_id) . " excedeu limite diário ($qtd_dia). ";
            $pode_salvar = false;
        }
    }
    foreach ($contagem_prof_semana_validacao as $p_id => $qtd_sem) {
        if ($qtd_sem > LIMITE_AULAS_PROFESSOR_SEMANAL) {
            $mensagem_erro .= "Prof. " . ($professores_map[$p_id]??$p_id) . " excedeu limite semanal ($qtd_sem). ";
            $pode_salvar = false;
        }
    }

    // --- SALVAR ---
    if ($pode_salvar && empty($mensagem_erro)) {
        // Limpa o dia inteiro para evitar duplicatas
        $stmt_clean = $conexao->prepare("DELETE FROM {$tabela_alocacao} WHERE dia = ?");
        $stmt_clean->bind_param("s", $dia_focado);
        
        if ($stmt_clean->execute()) {
            $stmt_ins = $conexao->prepare("INSERT INTO {$tabela_alocacao} (id_disc, id_periodo, id_prof, id_turma, dia, id_sala) VALUES (?, ?, ?, ?, ?, ?)");
            $contador_sucesso = 0;

            foreach ($aulas_post as $key => $dados) {
                list($dia_p, $per_str, $turma_str) = explode('_', $key);
                if ($dia_p !== $dia_focado) continue;

                $id_disc = (int)($dados['id_disc'] ?? 0);
                $id_prof = (int)($dados['id_prof'] ?? 0);
                
                if ($id_disc > 0) {
                    $id_per = (int)$per_str;
                    $id_turma = (int)$turma_str;
                    $id_sala = $id_turma; // Sala = ID Turma

                    $stmt_ins->bind_param("iiiisi", $id_disc, $id_per, $id_prof, $id_turma, $dia_p, $id_sala);
                    $stmt_ins->execute();
                    $contador_sucesso++;
                }
            }
            redirect("Salvo com sucesso! $contador_sucesso aulas atualizadas.", "");
        } else {
            $mensagem_erro = "Erro crítico ao limpar agenda do dia: " . $conexao->error;
        }
    } else {
        redirect("", $mensagem_erro);
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Atribuição de Aulas</title>
    <link rel="stylesheet" href="css/dashboard.css" />
    <style>
        .grade-container { overflow-x: auto; margin-top: 20px; }
        .tabela-grade { width: 100%; border-collapse: collapse; min-width: 800px; font-size: 13px; }
        .tabela-grade th, .tabela-grade td { border: 1px solid #ccc; padding: 5px; text-align: center; }
        .tabela-grade th { background: #48ab87; color: white; }
        .cell-content { display: flex; flex-direction: column; gap: 4px; }
        select { width: 100%; font-size: 11px; padding: 2px; }
        .msg-ok { background: #d4edda; color: #155724; padding: 10px; border-radius: 5px; margin-bottom: 10px; }
        .msg-bad { background: #f8d7da; color: #721c24; padding: 10px; border-radius: 5px; margin-bottom: 10px; }
        option:disabled { background-color: #ffdddd; color: #a00; font-weight: bold; }
    </style>
</head>
<body>
<main style="margin: 20px;">
    
    <h1>Atribuição de Aulas</h1>
    
    <?php if ($mensagem_sucesso): ?><div class="msg-ok"><?= $mensagem_sucesso ?></div><?php endif; ?>
    <?php if ($mensagem_erro): ?><div class="msg-bad"><?= $mensagem_erro ?></div><?php endif; ?>

    <div class="card" style="margin-bottom: 20px; padding: 15px; border-left: 5px solid #48ab87; background-color: #fff; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
        <h3 style="margin-top: 0; color: #333; display: flex; justify-content: space-between; align-items: center;">
            <span>📊 Status da Carga Horária</span>
            <button type="button" onclick="toggleCarga()" style="background: #48ab87; color: white; border: none; padding: 5px 10px; border-radius: 4px; cursor: pointer; font-size: 0.8rem;">
                Mostrar/Esconder Detalhes
            </button>
        </h3>
        
        <div style="margin-bottom: 10px; font-size: 0.95em;">
            <?php 
            $total_pend = 0;
            foreach ($carga_horaria_necessaria as $t_id => $dd) {
                foreach ($dd as $d_id => $m) {
                    if (($aulas_alocadas[$t_id][$d_id] ?? 0) < $m) $total_pend++;
                }
            }
            if ($total_pend > 0) {
                echo "<span style='color: #e65100; font-weight: bold;'>⚠️ Atenção: Existem $total_pend disciplinas com carga horária incompleta.</span>";
            } else {
                echo "<span style='color: green; font-weight: bold;'>✅ Tudo certo! Toda a carga horária foi alocada corretamente.</span>";
            }
            ?>
        </div>

        <div id="painel-carga" style="display: none; margin-top: 15px; border-top: 1px solid #eee; padding-top: 15px;">
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 15px;">
            <?php foreach ($turmas as $t): 
                $t_id = $t['id_turma'];
                $nome_turma = $t['nome_turma'];
                if (empty($carga_horaria_necessaria[$t_id])) continue; 
            ?>
                <div style="border: 1px solid #e0e0e0; padding: 10px; border-radius: 6px; background: #fafafa;">
                    <h4 style="margin: 0 0 10px 0; color: #48ab87; font-size: 1em; border-bottom: 1px solid #eee; padding-bottom: 5px;">
                        <?= htmlspecialchars($nome_turma) ?>
                    </h4>
                    
                    <div style="display: flex; flex-direction: column; gap: 8px;">
                        <?php foreach ($carga_horaria_necessaria[$t_id] as $d_id => $meta): 
                            $atual = $aulas_alocadas[$t_id][$d_id] ?? 0;
                            $nome_disc = $disciplinas_map[$d_id] ?? "ID $d_id";
                            $percent = ($meta > 0) ? min(100, round(($atual / $meta) * 100)) : 0;
                            
                            if ($atual >= $meta) {
                                $cor_barra = '#4CAF50'; 
                                $status_txt = 'OK';
                                $cor_txt = 'green';
                            } else {
                                $cor_barra = '#ff9800'; 
                                $status_txt = '-' . ($meta - $atual);
                                $cor_txt = '#e65100';
                            }
                        ?>
                            <div style="font-size: 0.85em;">
                                <div style="display: flex; justify-content: space-between; margin-bottom: 2px;">
                                    <span style="color: #555; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 200px;" title="<?= htmlspecialchars($nome_disc) ?>">
                                        <?= htmlspecialchars($nome_disc) ?>
                                    </span>
                                    <span style="font-weight: bold; color: <?= $cor_txt ?>;">
                                        <?= $atual ?>/<?= $meta ?> (<?= $status_txt ?>)
                                    </span>
                                </div>
                                <div style="background: #ddd; height: 6px; border-radius: 3px; overflow: hidden;">
                                    <div style="background: <?= $cor_barra ?>; width: <?= $percent ?>%; height: 100%;"></div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
            </div>
        </div>
    </div>

    <script>
    function toggleCarga() {
        var x = document.getElementById("painel-carga");
        x.style.display = (x.style.display === "none") ? "block" : "none";
    }
    </script>

    <?php foreach ($dias_semana as $dia): ?>
        <div class="card" style="background:white; padding:15px; border-radius:8px; margin-bottom:30px; box-shadow:0 2px 5px rgba(0,0,0,0.1);">
            <h2 style="color:#48ab87; border-bottom:1px solid #eee; padding-bottom:5px;"><?= $dia ?></h2>
            
            <form method="POST" action="<?= BASE_PATH ?>">
                <input type="hidden" name="acao" value="salvar_grade">
                <input type="hidden" name="dia_salvo" value="<?= $dia ?>">
                
                <div class="grade-container">
                    <table class="tabela-grade">
                        <thead>
                            <tr>
                                <th style="width: 100px;">Horário</th>
                                <?php foreach ($turmas as $t): ?>
                                    <th><?= htmlspecialchars($t['nome_turma']) ?></th>
                                <?php endforeach; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($periodos_aula as $p): 
                                $h_str = $p['horario_str'];
                                $p_id = $p['id_periodo'];
                            ?>
                            <tr>
                                <td><b><?= $h_str ?></b></td>
                                <?php foreach ($turmas as $t): 
                                    $t_id = $t['id_turma'];
                                    $key = $dia . '_' . $p_id . '_' . $t_id;
                                    
                                    // Valores atuais
                                    $val_disc = $grade_visualizada[$key]['id_disc'] ?? 0;
                                    $val_prof = $grade_visualizada[$key]['id_prof'] ?? 0;
                                ?>
                                <td>
                                    <div class="cell-content">
                                        <select name="aulas[<?= $key ?>][id_disc]">
                                            <option value="0">-- Disciplina --</option>
                                            <?php foreach ($disciplinas as $d): 
                                                $d_id = $d['id_disc'];
                                                $sel = ($val_disc == $d_id) ? 'selected' : '';
                                                
                                                if (!isset($carga_horaria_necessaria[$t_id][$d_id])) continue;

                                                $meta = $carga_horaria_necessaria[$t_id][$d_id];
                                                $ja_tem = $aulas_alocadas[$t_id][$d_id] ?? 0;
                                                if ($sel) $ja_tem--; // Ajuste visual
                                                
                                                $label = htmlspecialchars($d['nome']);
                                                if ($meta > 0) $label .= " ($ja_tem/$meta)";
                                            ?>
                                                <option value="<?= $d_id ?>" <?= $sel ?>><?= $label ?></option>
                                            <?php endforeach; ?>
                                        </select>

                                        <select name="aulas[<?= $key ?>][id_prof]">
                                            <option value="0">-- Professor --</option>
                                            <?php foreach ($professores as $prof): 
                                                $prof_id = $prof['id_prof'];
                                                $sel = ($val_prof == $prof_id) ? 'selected' : '';
                                                
                                                $bloqueado = false;
                                                $motivo = '';

                                                if (isset($conflito_horario_map[$dia][$p_id])) {
                                                    foreach ($conflito_horario_map[$dia][$p_id] as $t_ocupada => $p_ocupado) {
                                                        if ($p_ocupado == $prof_id && $t_ocupada != $t_id) {
                                                            $bloqueado = true;
                                                            $motivo = ' [Ocupado]';
                                                            break;
                                                        }
                                                    }
                                                }

                                                $res_key = $prof_id . '_' . trim($dia) . '_' . trim($h_str);
                                                if (isset($restricoes_prof_map[$res_key])) {
                                                    $bloqueado = true;
                                                    $motivo = ' [Restrição]';
                                                }

                                                $count_dia = ($aulas_por_dia_prof[$prof_id][$dia] ?? 0) - ($sel ? 1 : 0);
                                                $count_sem = ($aulas_por_prof[$prof_id] ?? 0) - ($sel ? 1 : 0);

                                                if ($count_dia >= LIMITE_AULAS_PROFESSOR_DIARIO) {
                                                    $bloqueado = true;
                                                    $motivo = ' [Max Dia]';
                                                }
                                                if ($count_sem >= LIMITE_AULAS_PROFESSOR_SEMANAL) {
                                                    $bloqueado = true;
                                                    $motivo = ' [Max Sem]';
                                                }

                                                if ($sel) $bloqueado = false; 
                                            ?>
                                                <option value="<?= $prof_id ?>" <?= $sel ?> <?= $bloqueado ? 'disabled' : '' ?>>
                                                    <?= htmlspecialchars($prof['nome']) . $motivo ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </td>
                                <?php endforeach; ?>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div style="text-align:right; margin-top:10px;">
                    <button type="submit" class="btn-salvar">Salvar Alterações de <?= $dia ?></button>
                </div>
            </form>
        </div>
    <?php endforeach; ?>

</main>
</body>
</html>