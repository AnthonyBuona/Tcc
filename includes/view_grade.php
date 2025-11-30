<?php
// Arquivo: includes/view_grade.php
session_start();
include 'config.php';

// Configurações
$tabela_alocacao = 'horario'; 
$tabela_periodo = 'periodo_aula'; 
$tabela_restricoes = 'disponibilidade_prof'; 

define('LIMITE_AULAS_PROFESSOR_DIARIO', 8);     
define('LIMITE_AULAS_PROFESSOR_SEMANAL', 30);  

$dias_semana = ['Segunda', 'Terça', 'Quarta', 'Quinta', 'Sexta'];

// --- 1. CARREGAR PERÍODOS ---
$periodos_aula = [];
$sql_periodos = "SELECT id_periodo, horario AS horario_str FROM {$tabela_periodo} ORDER BY id_periodo ASC";
$result_periodos = mysqli_query($conexao, $sql_periodos);
if ($result_periodos) {
    $periodos_aula = mysqli_fetch_all($result_periodos, MYSQLI_ASSOC);
}

// --- 2. CARREGAR DADOS ---
$professores = [];
$res_prof = mysqli_query($conexao, "SELECT id_prof, nome FROM professor WHERE status_aprovacao = 'APROVADO' ORDER BY nome ASC");
if ($res_prof) $professores = mysqli_fetch_all($res_prof, MYSQLI_ASSOC);

$disciplinas = [];
$res_disc = mysqli_query($conexao, "SELECT id_disc, nome_disc AS nome FROM disciplina ORDER BY nome_disc ASC");
if ($res_disc) $disciplinas = mysqli_fetch_all($res_disc, MYSQLI_ASSOC);
$disciplinas_map = array_column($disciplinas, 'nome', 'id_disc');

$turmas = [];
$res_turma = mysqli_query($conexao, "SELECT id_turma, nome_turma FROM turma ORDER BY nome_turma ASC");
if ($res_turma) $turmas = mysqli_fetch_all($res_turma, MYSQLI_ASSOC);
$turmas_map = array_column($turmas, 'nome_turma', 'id_turma');

// --- 3. CARGAS E RESTRIÇÕES ---
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

// --- 4. GRADE ATUAL ---
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
?>

<div class="main-header">
    <h1>Atribuição de Aulas</h1>
</div>

<div class="card" style="margin-bottom: 20px; padding: 15px; border-left: 5px solid #48ab87; background-color: #fff; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
    <h3 style="margin-top: 0; color: #333; display: flex; justify-content: space-between; align-items: center;">
        <span>📊 Status da Carga Horária</span>
        <button type="button" onclick="document.getElementById('painel-carga-ajax').style.display = (document.getElementById('painel-carga-ajax').style.display === 'none' ? 'block' : 'none');" style="background: #48ab87; color: white; border: none; padding: 5px 10px; border-radius: 4px; cursor: pointer; font-size: 0.8rem;">
            Mostrar/Esconder
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

    <div id="painel-carga-ajax" style="display: none; margin-top: 15px; border-top: 1px solid #eee; padding-top: 15px;">
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 15px;">
        <?php foreach ($turmas as $t): 
            $t_id = $t['id_turma'];
            if (empty($carga_horaria_necessaria[$t_id])) continue;
        ?>
            <div style="border: 1px solid #e0e0e0; padding: 10px; border-radius: 6px; background: #fafafa;">
                <h4 style="margin: 0 0 10px 0; color: #48ab87; font-size: 1em; border-bottom: 1px solid #eee; padding-bottom: 5px;">
                    <?= htmlspecialchars($t['nome_turma']) ?>
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
                                <span style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 180px;" title="<?= htmlspecialchars($nome_disc) ?>">
                                    <?= htmlspecialchars($nome_disc) ?>
                                </span>
                                <span style="font-weight: bold; color: <?= $cor_txt ?>;">
                                    <?= $atual ?>/<?= $meta ?>
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

<?php foreach ($dias_semana as $dia): ?>
    <div class="card" style="background:white; padding:15px; border-radius:8px; margin-bottom:30px; box-shadow:0 2px 5px rgba(0,0,0,0.1);">
        <h2 style="color:#48ab87; border-bottom:1px solid #eee; padding-bottom:5px;"><?= $dia ?></h2>
        
        <form class="form-alocacao-ajax" data-dia="<?= $dia ?>">
            <input type="hidden" name="acao" value="salvar_grade">
            <input type="hidden" name="dia_salvo" value="<?= $dia ?>">
            
            <div class="grade-container" style="overflow-x: auto;">
                <table class="tabela-grade" style="width: 100%; border-collapse: collapse; min-width: 800px; font-size: 13px;">
                    <thead>
                        <tr>
                            <th style="background: #48ab87; color: white; padding: 5px; border: 1px solid #ccc; width: 100px;">Horário</th>
                            <?php foreach ($turmas as $t): ?>
                                <th style="background: #48ab87; color: white; padding: 5px; border: 1px solid #ccc;"><?= htmlspecialchars($t['nome_turma']) ?></th>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($periodos_aula as $p): 
                            $h_str = $p['horario_str'];
                            $p_id = $p['id_periodo'];
                        ?>
                        <tr>
                            <td style="border: 1px solid #ccc; padding: 5px; text-align: center;"><b><?= $h_str ?></b></td>
                            <?php foreach ($turmas as $t): 
                                $t_id = $t['id_turma'];
                                $key = $dia . '_' . $p_id . '_' . $t_id;
                                $val_disc = $grade_visualizada[$key]['id_disc'] ?? 0;
                                $val_prof = $grade_visualizada[$key]['id_prof'] ?? 0;
                            ?>
                            <td style="border: 1px solid #ccc; padding: 5px;">
                                <div style="display: flex; flex-direction: column; gap: 4px;">
                                    
                                    <select name="aulas[<?= $key ?>][id_disc]" style="width: 100%; font-size: 11px; padding: 2px;">
                                        <option value="0">--</option>
                                        <?php foreach ($disciplinas as $d): 
                                            $d_id = $d['id_disc'];
                                            if (!isset($carga_horaria_necessaria[$t_id][$d_id])) continue;
                                            $sel = ($val_disc == $d_id) ? 'selected' : '';
                                            $meta = $carga_horaria_necessaria[$t_id][$d_id];
                                            $ja_tem = $aulas_alocadas[$t_id][$d_id] ?? 0;
                                            if ($sel) $ja_tem--;
                                            $label = htmlspecialchars($d['nome']);
                                            if ($meta > 0) $label .= " ($ja_tem/$meta)";
                                        ?>
                                            <option value="<?= $d_id ?>" <?= $sel ?>><?= $label ?></option>
                                        <?php endforeach; ?>
                                    </select>

                                    <select name="aulas[<?= $key ?>][id_prof]" style="width: 100%; font-size: 11px; padding: 2px;">
                                        <option value="0">--</option>
                                        <?php foreach ($professores as $prof): 
                                            $prof_id = $prof['id_prof'];
                                            $sel = ($val_prof == $prof_id) ? 'selected' : '';
                                            
                                            $bloqueado = false;
                                            $motivo = '';

                                            // Validações Visuais
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
                                            if ($count_dia >= LIMITE_AULAS_PROFESSOR_DIARIO) {
                                                $bloqueado = true;
                                                $motivo = ' [Max Dia]';
                                            }
                                            if ($sel) $bloqueado = false;
                                        ?>
                                            <option value="<?= $prof_id ?>" <?= $sel ?> <?= $bloqueado ? 'disabled style="background:#ffdddd;color:#a00;"' : '' ?>>
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
                <button type="submit" class="btn-salvar">Salvar <?= $dia ?></button>
            </div>
        </form>
    </div>
<?php endforeach; ?>