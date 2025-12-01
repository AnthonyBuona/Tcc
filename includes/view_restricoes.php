<?php
// Arquivo: includes/view_restricoes.php
session_start();
include 'config.php';

$dias_semana = ['Segunda', 'Terça', 'Quarta', 'Quinta', 'Sexta'];

// 1. CARREGAR PERÍODOS
$mapa_periodos = [];
$horarios_aula_visual = []; 

$sql_p = "SELECT id_periodo, horario FROM periodo_aula ORDER BY id_periodo ASC";
$res_p = mysqli_query($conexao, $sql_p);

if ($res_p) {
    while ($row = mysqli_fetch_assoc($res_p)) {
        $mapa_periodos[$row['id_periodo']] = $row['horario'];
        $horarios_aula_visual[] = $row['horario']; 
    }
} else {
    $horarios_aula_visual = [
        '07:30 - 08:20', '08:20 - 09:10', '09:10 - 10:00', '10:20 - 11:10',
        '13:30 - 14:20', '14:20 - 15:10', '15:10 - 16:00', '16:20 - 17:10', 
        '17:10 - 18:00'
    ];
}

// 2. Carregar Professores
$professores = [];
$res = mysqli_query($conexao, "SELECT id_prof, nome FROM professor WHERE status_aprovacao = 'APROVADO' ORDER BY nome ASC");
if ($res) $professores = mysqli_fetch_all($res, MYSQLI_ASSOC);

// 3. Dados do Professor Selecionado
$id_selecionado = isset($_GET['id_prof']) ? (int)$_GET['id_prof'] : 0;
$dados_prof = null;
$restricoes_atuais = [];
$aulas_ocupadas = []; 

if ($id_selecionado > 0) {
    // Perfil
    $stmt = $conexao->prepare("SELECT trabalha_outro_lugar, horario_saida_outro_lugar, nome FROM professor WHERE id_prof = ?");
    $stmt->bind_param("i", $id_selecionado);
    $stmt->execute();
    $res_p = $stmt->get_result();
    $dados_prof = $res_p->fetch_assoc();
    $stmt->close();

    // Restrições Manuais
    $stmt_r = $conexao->prepare("SELECT dia, horario FROM disponibilidade_prof WHERE id_prof = ?");
    $stmt_r->bind_param("i", $id_selecionado);
    $stmt_r->execute();
    $res_r = $stmt_r->get_result();
    while ($row = $res_r->fetch_assoc()) {
        $restricoes_atuais[] = $row['dia'] . '_' . trim($row['horario']);
    }
    $stmt_r->close();

    // Aulas Atribuídas
    $sql_aulas = "
        SELECT h.dia, h.id_periodo, t.nome_turma 
        FROM horario h
        JOIN turma t ON h.id_turma = t.id_turma
        WHERE h.id_prof = ?
    ";
    $stmt_a = $conexao->prepare($sql_aulas);
    $stmt_a->bind_param("i", $id_selecionado);
    $stmt_a->execute();
    $res_a = $stmt_a->get_result();
    
    while ($row = $res_a->fetch_assoc()) {
        $horario_texto = $mapa_periodos[$row['id_periodo']] ?? null;
        if ($horario_texto) {
            $chave = $row['dia'] . '_' . trim($horario_texto);
            $aulas_ocupadas[$chave] = $row['nome_turma']; 
        }
    }
    $stmt_a->close();
}
?>

<div class="main-header">
    <h1 style="color: #48ab87;">Restrições de Horário</h1>
    <p>Defina a disponibilidade. <span style="color:#007bff; font-weight:bold;">Azul</span> indica aulas atribuídas. <span style="color:#666; font-weight:bold;">Cinza</span> indica restrição por horário de saída.</p>
</div>

<div class="content-panel" style="margin-bottom: 20px;">
    <fieldset>
        <legend>Selecionar Professor</legend>
        <form id="form-busca-prof-restricao" style="display:flex; gap:10px; align-items:flex-end;">
            <div class="form-group" style="flex-grow:1; max-width:400px; margin-bottom:0;">
                <label for="select_prof_restricao">Professor:</label>
                <select id="select_prof_restricao" name="id_prof" class="custom-select-like" style="width:100%; padding:10px; border:1px solid #ccc; border-radius:4px;">
                    <option value="">Selecione...</option>
                    <?php foreach ($professores as $p): ?>
                        <option value="<?= $p['id_prof'] ?>" <?= ($id_selecionado == $p['id_prof']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($p['nome']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="btn-salvar" style="margin:0;">Carregar</button>
        </form>
    </fieldset>
</div>

<?php if ($dados_prof): ?>
    <div id="area-edicao-restricoes">
        <div class="content-panel" style="margin-bottom: 20px;">
            <fieldset>
                <legend>Perfil: <?= htmlspecialchars($dados_prof['nome']) ?></legend>
                <form id="form-perfil-prof" class="form-ajax-restricao">
                    <input type="hidden" name="acao" value="salvar_perfil">
                    <input type="hidden" name="id_prof_selecionado_perfil" value="<?= $id_selecionado ?>">
                    
                    <div style="display:flex; gap:20px; flex-wrap:wrap;">
                        <div class="form-group">
                            <label>Trabalha em outro local?</label>
                            <select name="trabalha_outro_lugar" id="trabalha_outro" onchange="toggleHorarioSaida()" style="padding:8px; border-radius:4px; border:1px solid #ccc;">
                                <option value="0" <?= ($dados_prof['trabalha_outro_lugar'] == 0) ? 'selected' : '' ?>>Não</option>
                                <option value="1" <?= ($dados_prof['trabalha_outro_lugar'] == 1) ? 'selected' : '' ?>>Sim</option>
                            </select>
                        </div>
                        <div class="form-group" id="div-horario-saida" style="display: <?= ($dados_prof['trabalha_outro_lugar'] == 1) ? 'block' : 'none' ?>;">
                            <label>Horário de Saída (Outro Local):</label>
                            <input type="time" name="horario_saida_outro_lugar" value="<?= htmlspecialchars($dados_prof['horario_saida_outro_lugar'] ?? '') ?>" style="padding:8px; border-radius:4px; border:1px solid #ccc;">
                        </div>
                    </div>
                    <button type="submit" class="btn-salvar">Salvar Perfil</button>
                </form>
            </fieldset>
        </div>

        <div class="content-panel">
            <fieldset>
                <legend>Grade de Indisponibilidade</legend>
                <div style="display:flex; gap:15px; margin-bottom:10px; font-size:0.9em; flex-wrap: wrap;">
                    <div style="display:flex; align-items:center; gap:5px;">
                        <div style="width:15px; height:15px; background:#ffcccc; border:1px solid #ddd;"></div>
                        <span>Indisponível (Manual)</span>
                    </div>
                    <div style="display:flex; align-items:center; gap:5px;">
                        <div style="width:15px; height:15px; background:#dbeafe; border:1px solid #ddd;"></div>
                        <span>Aula Atribuída</span>
                    </div>
                    <div style="display:flex; align-items:center; gap:5px;">
                        <div style="width:15px; height:15px; background:#e0e0e0; border:1px solid #999;"></div>
                        <span>Bloqueio por Perfil (Saída)</span>
                    </div>
                </div>
                
                <form id="form-grade-restricao" class="form-ajax-restricao">
                    <input type="hidden" name="acao" value="salvar_restricoes">
                    <input type="hidden" name="id_prof_selecionado" value="<?= $id_selecionado ?>">
                    
                    <div style="overflow-x:auto;">
                        <table class="restricao-table" style="width:100%; border-collapse:collapse; margin-top:10px;">
                            <thead>
                                <tr>
                                    <th style="background:#48ab87; color:white; padding:8px;">Horário</th>
                                    <?php foreach ($dias_semana as $d): ?>
                                        <th style="background:#48ab87; color:white; padding:8px;"><?= $d ?></th>
                                    <?php endforeach; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                // Variáveis auxiliares para o bloqueio de saída
                                $h_saida_prof = null;
                                if ($dados_prof['trabalha_outro_lugar'] == 1 && !empty($dados_prof['horario_saida_outro_lugar'])) {
                                    $h_saida_prof = strtotime(date('Y-m-d') . ' ' . $dados_prof['horario_saida_outro_lugar']);
                                }

                                foreach ($horarios_aula_visual as $horario): 
                                    // Extrai o horário de início (Ex: "16:20 - 17:10" -> "16:20")
                                    $parts = explode(' - ', $horario);
                                    $inicio_periodo_str = isset($parts[0]) ? trim($parts[0]) : null;
                                    
                                    // Verifica se o período é posterior ao horário de saída
                                    $bloqueado_por_perfil = false;
                                    if ($h_saida_prof && $inicio_periodo_str) {
                                        $h_inicio_aula = strtotime(date('Y-m-d') . ' ' . $inicio_periodo_str);
                                        if ($h_inicio_aula >= $h_saida_prof) {
                                            $bloqueado_por_perfil = true;
                                        }
                                    }
                                ?>
                                    <tr>
                                        <td style="border:1px solid #ddd; padding:8px; font-weight:bold; white-space:nowrap;"><?= $horario ?></td>
                                        
                                        <?php foreach ($dias_semana as $dia): 
                                            $val = $dia . '_' . trim($horario);
                                            
                                            // Status
                                            $is_restrito = in_array($val, $restricoes_atuais);
                                            $turma_ocupada = $aulas_ocupadas[$val] ?? false;
                                            
                                            // Definição de Estilos
                                            if ($turma_ocupada) {
                                                // Aula Atribuída
                                                $style = "background-color: #dbeafe; color: #1e40af; cursor: not-allowed;";
                                                $onclick = "";
                                                $conteudo = "<small style='font-weight:600; display:block; line-height:1.2;'>" . htmlspecialchars($turma_ocupada) . "</small>";
                                                $checkbox = ""; 
                                            } elseif ($bloqueado_por_perfil) {
                                                // Bloqueio Automático por Perfil
                                                $style = "background-color: #e0e0e0; color: #555; cursor: not-allowed; border: 1px solid #bbb;";
                                                $onclick = "";
                                                $conteudo = "<small style='font-size:0.7em;'>Indisponível<br>(Perfil)</small>";
                                                // Checkbox hidden disabled para não enviar, mas marcando o bloqueio visualmente
                                                $checkbox = ""; 
                                            } else {
                                                // Disponível / Restrição Manual
                                                $cor_bg = $is_restrito ? '#ffcccc' : 'white';
                                                $style = "background-color: $cor_bg; cursor: pointer; transition: background 0.2s;";
                                                $onclick = "onclick=\"toggleRestricao(this)\"";
                                                $checked = $is_restrito ? 'checked' : '';
                                                $conteudo = "";
                                                $checkbox = "<input type='checkbox' name='restricoes[]' value='$val' $checked style='display:none;'>";
                                            }
                                        ?>
                                            <td class="celula-restricao <?= $is_restrito ? 'restrito' : '' ?>" 
                                                style="border:1px solid #ddd; text-align:center; height:40px; vertical-align:middle; <?= $style ?>"
                                                <?= $onclick ?>>
                                                
                                                <?= $conteudo ?>
                                                <?= $checkbox ?>
                                                
                                            </td>
                                        <?php endforeach; ?>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <button type="submit" class="btn-salvar" style="margin-top:15px;">Salvar Grade</button>
                </form>
            </fieldset>
        </div>
    </div>
<?php endif; ?>