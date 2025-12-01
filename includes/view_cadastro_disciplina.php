<?php
// Arquivo: includes/view_cadastro_disciplina.php
session_start();
include 'config.php';

// --- 1. BUSCAR DADOS DO BANCO ---

// A. Disciplinas
$disciplinas = [];
$res_d = mysqli_query($conexao, "SELECT id_disc, nome_disc, area FROM disciplina ORDER BY nome_disc ASC");
if ($res_d) $disciplinas = mysqli_fetch_all($res_d, MYSQLI_ASSOC);

// B. Professores (Aprovados)
$professores = [];
$res_p = mysqli_query($conexao, "SELECT id_prof, nome, areas FROM professor WHERE status_aprovacao = 'APROVADO' ORDER BY nome ASC");
if ($res_p) $professores = mysqli_fetch_all($res_p, MYSQLI_ASSOC);

// C. Turmas e Agrupamento
$turmas = [];
$res_t = mysqli_query($conexao, "SELECT * FROM turma ORDER BY id_turma ASC");
if ($res_t) $turmas = mysqli_fetch_all($res_t, MYSQLI_ASSOC);

// Agrupamento de Turmas por Curso (Lógica extraída do testes.php)
$turmas_agrupadas = [];
foreach ($turmas as $t) {
    $nome = $t['nome_turma'];
    // Remove "1º ", "2º " etc para pegar o nome do curso
    $curso = preg_replace('/^\d+º\s?/', '', $nome); 
    if (empty($curso) || $curso === $nome) {
        $curso = preg_replace('/^\d\s?/', '', $nome); 
    }
    $turmas_agrupadas[$curso][] = $t;
}

// Extrair áreas únicas dos professores para o filtro
$areas_filtro = [];
foreach($professores as $p) {
    $areas_filtro = array_merge($areas_filtro, explode(',', $p['areas']));
}
$areas_filtro = array_unique(array_map('trim', $areas_filtro));
sort($areas_filtro);
?>

<div class="main-header">
    <h1 style="color: #48ab87;">Relacionamento de Disciplinas</h1>
    <p>Vincule uma disciplina a um professor e às turmas correspondentes.</p>
</div>

<div id="feedback-cadastro-disciplina"></div>

<div class="content-panel">
    <form id="form-relacionamento-disciplina">                        
        <div class="form-two-columns">
            
            <fieldset>
                <legend>1. Selecione a Disciplina</legend>
                <div class="form-group">
                    <div class="custom-select-wrapper" id="wrapper-disciplina">
                        <div class="custom-select" tabindex="0">Selecione a Disciplina</div>
                        <div class="options" style="max-height: 300px; overflow-y: auto;">
                            <?php if (!empty($disciplinas)): ?>
                                <?php foreach ($disciplinas as $d): ?>
                                    <div class="option disciplina-option" 
                                         data-value="<?= $d['id_disc'] ?>"
                                         data-area="<?= htmlspecialchars($d['area']) ?>">
                                        <?= htmlspecialchars($d['nome_disc']) ?> 
                                        <small style="display:block; color:#888; font-size:0.8em;"><?= htmlspecialchars($d['area']) ?></small>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="option disabled">Nenhuma disciplina cadastrada.</div>
                            <?php endif; ?>
                        </div>
                        <input type="hidden" name="id_disc" id="id_disc_input" required />
                    </div>
                </div>
            </fieldset>

            <fieldset>
                <legend>2. Professor Responsável</legend>
                <div class="form-group">
                    <div class="custom-select-wrapper" id="wrapper-professor">
                        <div class="custom-select" tabindex="0">Selecione o Professor</div>
                        <div class="options" style="min-width: 100%;">
                            
                            <div class="options-search" style="padding:10px; border-bottom:1px solid #eee; background:#f9f9f9;">
                                <select id="filtro-area-prof" style="width: 100%; padding:5px; margin-bottom:5px;" onclick="event.stopPropagation()">
                                    <option value="">Todas as Áreas</option>
                                    <?php foreach ($areas_filtro as $area): ?>
                                        <?php if (!empty($area)): ?>
                                            <option value="<?= htmlspecialchars($area) ?>"><?= htmlspecialchars($area) ?></option>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </select>
                                <input type="text" id="busca-prof" placeholder="Buscar nome..." style="width:100%; padding:5px;" onclick="event.stopPropagation()">
                            </div>

                            <div class="options-list" style="max-height: 200px; overflow-y: auto;">
                                <?php if (!empty($professores)): ?>
                                    <?php foreach ($professores as $prof): ?>
                                        <div class="option professor-option" 
                                             data-value="<?= $prof['id_prof'] ?>" 
                                             data-name="<?= htmlspecialchars($prof['nome']) ?>"
                                             data-areas="<?= htmlspecialchars($prof['areas']) ?>">
                                            <?= htmlspecialchars($prof['nome']) ?>
                                            <span style="display:none;"><?= htmlspecialchars($prof['areas']) ?></span> </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <div class="option disabled">Nenhum professor aprovado.</div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <input type="hidden" name="id_prof_padrao" id="id_prof_input" required />
                    </div>
                </div>
            </fieldset>
        </div> 
        
        <fieldset>
            <legend>3. Selecione as Turmas</legend>
            <p style="margin-bottom: 20px; font-size: 0.9em; color:#666;">
                Clique no cartão para ativar a turma e defina a carga horária semanal e total.
            </p>

            <div class="turmas-agrupadas-container">
            <?php if (!empty($turmas_agrupadas)): ?>
                <?php foreach ($turmas_agrupadas as $curso => $lista_turmas): ?>
                    <div class="turma-grupo">
                        <h4 style="color:#3b8e72; border-bottom:1px solid #eee; margin-bottom:10px; padding-bottom:5px;">
                            <i class="fas fa-layer-group"></i> <?= htmlspecialchars($curso) ?>
                        </h4>
                        
                        <div class="turmas-relacionamento-grid" style="display:grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap:15px;">
                        <?php foreach ($lista_turmas as $turma): ?>
                            <div class="turma-card-relacionamento" id="card_<?= $turma['id_turma'] ?>" 
                                 onclick="toggleCardTurma('<?= $turma['id_turma'] ?>')"
                                 style="border:1px solid #ddd; border-radius:8px; padding:15px; cursor:pointer; transition:all 0.2s;">
                                            
                                <input type="checkbox" id="chk_<?= $turma['id_turma'] ?>" 
                                       name="turmas[<?= $turma['id_turma'] ?>][incluir]" 
                                       value="1" style="display: none;">
                                            
                                <div style="display: flex; justify-content: space-between; align-items: center; font-weight:600; color:#555;">
                                    <span><?= htmlspecialchars($turma['nome_turma']) ?></span>
                                    <i class="fas fa-toggle-off toggle-icon" id="icon_<?= $turma['id_turma'] ?>" style="color: #ccc; font-size:1.2em;"></i>
                                </div>
                                            
                                <input type="hidden" name="turmas[<?= $turma['id_turma'] ?>][id_turma]" value="<?= $turma['id_turma'] ?>">

                                <div id="inputs_<?= $turma['id_turma'] ?>" style="display: none; margin-top: 15px; padding-top: 10px; border-top: 1px dashed #eee;">
                                    <div class="form-group" style="margin-bottom:10px;">
                                        <label style="font-size:0.85em;">Aulas Semanais:</label>
                                        <input type="number" name="turmas[<?= $turma['id_turma'] ?>][aulas_semanais]" 
                                               min="1" value="0" class="input-sm" style="width:100%; padding:5px; border:1px solid #ccc; border-radius:4px;" disabled 
                                               onclick="event.stopPropagation()">
                                    </div>
                                    <div class="form-group" style="margin-bottom:0;">
                                        <label style="font-size:0.85em;">Carga Total (H):</label>
                                        <input type="number" name="turmas[<?= $turma['id_turma'] ?>][carga_horaria]" 
                                               min="1" value="0" class="input-sm" style="width:100%; padding:5px; border:1px solid #ccc; border-radius:4px;" disabled
                                               onclick="event.stopPropagation()">
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Nenhuma turma cadastrada.</p>
            <?php endif; ?>
            </div>
        </fieldset>

        <button type="submit" class="btn-salvar" style="margin-top: 25px;">
            <i class="fas fa-save"></i> Salvar Relacionamento
        </button>
    </form>
</div>