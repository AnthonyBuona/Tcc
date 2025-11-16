<?php

// ====================================================================

// --- CONFIGURAÇÃO DE DEPURACÃO (RECOMENDADO MANTER ATIVA) ---

ini_set('display_errors', 1);

ini_set('display_startup_errors', 1);

error_reporting(E_ALL);

// ====================================================================



session_start();

// Inclui a conexão com o banco de dados. Assume-se que $conexao está definida em config.php

// ATENÇÃO: Se config.php não estiver em 'includes/', este caminho deve ser ajustado

include 'includes/config.php';



// --- VERIFICAÇÃO CRÍTICA DA CONEXÃO ---

if (!isset($conexao) || $conexao === false || mysqli_connect_errno()) {

    die("<h1>Erro Crítico: Falha na Conexão com o Banco de Dados.</h1>

          <p>Detalhe do Erro: " . mysqli_connect_error() . "</p>");

}



// ====================================================================

// *** ATENÇÃO: VERIFICAÇÃO DE SESSÃO ADMINISTRATIVA FOI REMOVIDA PARA TESTES ***

// ====================================================================





// Lógica para exibir mensagens de sucesso/erro após o redirecionamento

$mensagem = '';

$status = '';

// A URL usa #hash para mostrar a seção correta

if (isset($_GET['msg']) && isset($_GET['status'])) {

    $mensagem = htmlspecialchars($_GET['msg']);

    $status = htmlspecialchars($_GET['status']);

}



// --- LÓGICA PHP PARA LISTAGEM DE TURMAS (Primeiro para mapeamento) ---

$sql_turmas = "SELECT * FROM turma ORDER BY id_turma ASC";

$result_turmas = mysqli_query($conexao, $sql_turmas);

if ($result_turmas) {

    $turmas = mysqli_fetch_all($result_turmas, MYSQLI_ASSOC);

    mysqli_free_result($result_turmas);

} else {

    $turmas = [];

}





// Mapeamento de Turmas: ID -> Nome

$mapa_turmas = [];

foreach ($turmas as $t) {

    $mapa_turmas[$t['id_turma']] = $t['nome_turma'];

}



// --- LÓGICA PHP PARA BUSCAR CADASTROS PENDENTES (APROVAÇÕES) ---

$sql_pendentes_unificado = "

    SELECT

        id_aluno AS id,

        nome,

        cpf,

        email,

        'aluno' AS tipo_acesso,

        id_turma AS detalhe

    FROM aluno

    WHERE status_aprovacao = 'PENDENTE'

   

    UNION ALL

   

    SELECT

        id_prof AS id,

        nome,

        cpf,

        email,

        'professor' AS tipo_acesso,

        areas AS detalhe

    FROM professor

    WHERE status_aprovacao = 'PENDENTE'

    ORDER BY id ASC

";



$result_pendentes = mysqli_query($conexao, $sql_pendentes_unificado);

$pendentes = ($result_pendentes) ? mysqli_fetch_all($result_pendentes, MYSQLI_ASSOC) : [];

$total_pendentes = count($pendentes);



// --- LÓGICA PHP PARA LISTAGEM DE PROFESSORES ---

$sql_professores = "SELECT id_prof, nome, cpf, areas, status_aprovacao FROM professor ORDER BY id_prof DESC";

$result_professores = mysqli_query($conexao, $sql_professores);

$professores = ($result_professores) ? mysqli_fetch_all($result_professores, MYSQLI_ASSOC) : [];

if ($result_professores) mysqli_free_result($result_professores);



// --- LÓGICA PHP PARA LISTAGEM DE ALUNOS ---

$sql_alunos = "SELECT id_aluno, nome, cpf, id_turma, status_aprovacao FROM aluno ORDER BY id_aluno DESC";

$result_alunos = mysqli_query($conexao, $sql_alunos);

$alunos = ($result_alunos) ? mysqli_fetch_all($result_alunos, MYSQLI_ASSOC) : [];

if ($result_alunos) mysqli_free_result($result_alunos);



// --- NOVO: LÓGICA PHP PARA LISTAGEM DE DISCIPLINAS EXISTENTES ---

$sql_disciplinas = "SELECT id_disc, nome_disc, area FROM disciplina ORDER BY nome_disc ASC";

$result_disciplinas = mysqli_query($conexao, $sql_disciplinas);

$disciplinas_existentes = ($result_disciplinas) ? mysqli_fetch_all($result_disciplinas, MYSQLI_ASSOC) : [];

if ($result_disciplinas) mysqli_free_result($result_disciplinas);



// Pré-processamento das áreas dos professores para os filtros

$areas_professores = [];

foreach($professores as $p) {

    // Quebra as áreas por vírgula e junta em um único array

    $areas_professores = array_merge($areas_professores, explode(',', $p['areas']));

}

$areas_professores = array_unique(array_map('trim', $areas_professores));

sort($areas_professores); // Ordena as áreas para o filtro manual



// --- NOVO: Agrupamento de Turmas por Curso/Eixo ---

$turmas_agrupadas = [];

foreach ($turmas as $t) {

    // Tenta extrair o nome do curso (ex: "1º Linguagens" -> "Linguagens")

    $nome_completo = $t['nome_turma'];

    // Expressão regular para remover a série/ano do início (ex: '1º ', '2º ')

    $curso = preg_replace('/^\d+º\s?/', '', $nome_completo);

   

    // Se não for encontrado um prefixo, usa o nome completo.

    if (empty($curso) || $curso === $nome_completo) {

        // Tenta remover apenas a série '1', '2' ou '3' seguida de espaço

        $curso = preg_replace('/^\d\s?/', '', $nome_completo);

    }

   

    // Agrupa as turmas

    $turmas_agrupadas[$curso][] = $t;

}

?>



<!DOCTYPE html>

<html lang="pt-BR">

<head>

    <meta charset="UTF-8" />

    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <title>PlanIt - Admin | Dashboard (Modo de Teste)</title>

    <link rel="stylesheet" href="css/dashboard.css" />

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet" />

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" crossorigin="anonymous" />

   

    </head>

<body>



    <header>

        <div class="logo" onclick="mostrarSecao('dashboard-home')" style="cursor: pointer;">PlanIt 🦉</div>

        <div class="profile">

            <span>Administrador (Modo Teste)</span>

            <img src="avatar.png" alt="Avatar" />

        </div>

    </header>



    <aside>

        <nav>

            <ul>

                <li class="menu-title">Gerenciamento de Usuários</li>

                <ul class="submenu">

                    <li><a href="#aprovacoes" onclick="mostrarSecao('aprovacoes-section'); return false;">Aprovações (<?= $total_pendentes; ?>)</a></li>

                    <li><a href="#listar-professores" onclick="mostrarSecao('professores-section'); return false;">Listar Professores</a></li>

                    <li><a href="#listar-alunos" onclick="mostrarSecao('alunos-section'); return false;">Listar Alunos</a></li>

                    <li><a href="#listar-turmas" onclick="mostrarSecao('turmas-section'); return false;">Listar Turmas</a></li>

                </ul>



                <li class="menu-title">Gerenciamento de Disciplinas</li>

                <ul class="submenu">

                    <li><a href="#cadastrar-disciplina" onclick="mostrarSecao('cadastrar-disciplina-section'); return false;">Relacionar Disciplina</a></li>

                    <li><a href="">Visualizar Disciplina</a></li>

                </ul>



                <li class="menu-title">Alocação de Horários</li>

                <ul class="submenu">

                    <li><a href="alocacao.php">Atribuir Aulas</a></li>

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

   

        <div id="cadastrar-disciplina-section" class="content-section">

            <div class="main-header">

                <h1 style="color: #48ab87;">Relacionamento de Disciplinas e Carga Horária</h1>

            </div>

           

            <?php if (!empty($mensagem)): ?>

                <div class="message-<?= $status ?>">

                    <?= $mensagem ?>

                </div>

            <?php endif; ?>



            <div class="content-panel">

                <form id="form-relacionamento-disciplina" method="POST">                        

                    <div class="form-two-columns">

                       

                        <fieldset>

                            <legend>1. Selecione a Disciplina Existente</legend>

                            <div class="form-group">

                                <div class="custom-select-wrapper">

                                    <div class="custom-select" tabindex="0">Selecione a Disciplina</div>

                                    <div class="options" id="disciplina-options-list">

                                        <?php if (!empty($disciplinas_existentes)): ?>

                                            <?php foreach ($disciplinas_existentes as $disc): ?>

                                                <div

                                                    class="option disciplina-option"

                                                    data-value="<?= $disc['id_disc'] ?>"

                                                    data-area="<?= htmlspecialchars($disc['area']) ?>">

                                                    <?= htmlspecialchars($disc['nome_disc']) ?> (Área: <?= htmlspecialchars($disc['area']) ?>)

                                                </div>

                                            <?php endforeach; ?>

                                        <?php else: ?>

                                            <div class="option" style="cursor: default;">Nenhuma disciplina encontrada.</div>

                                        <?php endif; ?>

                                    </div>

                                    <input type="hidden" name="id_disc" id="id_disc_input" required />

                                </div>

                            </div>

                        </fieldset>





                        <fieldset>

                            <legend>2. Professor Responsável (Padrão)</legend>

                            <div class="form-group">

                                <div class="custom-select-wrapper" id="professor-select-wrapper">

                                    <div class="custom-select" tabindex="0">Selecione o Professor</div>

                                    <div class="options" style="min-width: 300px;">

                                        <div class="options-search">

                                            <select id="filtro-area" style="width: 100%;" onchange="filterProfessorOptions()">

                                                <option value="">Filtrar por Outra Área...</option>

                                                <?php foreach ($areas_professores as $area): ?>

                                                    <?php if (!empty(trim($area))): ?>

                                                        <option value="<?= htmlspecialchars(trim($area)) ?>"><?= htmlspecialchars(trim($area)) ?></option>

                                                    <?php endif; ?>

                                                <?php endforeach; ?>

                                            </select>

                                        </div>

                                       

                                        <div class="options-search" style="border-top: 1px solid #ddd;">

                                            <input type="text" id="professor-search" placeholder="Buscar por nome..." onkeyup="filterProfessorOptions()">

                                        </div>



                                        <div class="options-list">

                                            <?php if (!empty($professores)): ?>

                                                <?php foreach ($professores as $prof): ?>

                                                    <?php if ($prof['status_aprovacao'] == 'APROVADO'): ?>

                                                        <div class="option professor-option"

                                                                data-value="<?= $prof['id_prof'] ?>"

                                                                data-name="<?= htmlspecialchars($prof['nome']) ?>"

                                                                data-areas="<?= htmlspecialchars($prof['areas']) ?>">

                                                                <?= htmlspecialchars($prof['nome']) ?> (<?= htmlspecialchars($prof['areas']) ?>)

                                                        </div>

                                                    <?php endif; ?>

                                                <?php endforeach; ?>

                                            <?php else: ?>

                                                <div class="option" data-value="" style="cursor: default;">Nenhum professor aprovado.</div>

                                            <?php endif; ?>

                                        </div>

                                    </div>

                                    <input type="hidden" name="id_prof_padrao" id="professor_input" required />

                                </div>

                            </div>

                        </fieldset>



                    </div>

                   

                    <fieldset>
                        <legend>3. Relacionamento: Turmas</legend>

                        <p style="margin-bottom: 20px; font-size: 0.95rem;">Clique para ativar a disciplina na turma e defina a carga horária semanal.</p>



                        <div class="turmas-agrupadas-container">

                        <?php if (!empty($turmas_agrupadas)): ?>

                            <?php foreach ($turmas_agrupadas as $curso => $turmas_grupo): ?>

                                <div class="turma-grupo">

                                    <h4><i class="fas fa-layer-group" style="margin-right: 8px;"></i> <?= htmlspecialchars($curso) ?></h4>

                                   

                                    <div class="turmas-relacionamento-grid">

                                    <?php foreach ($turmas_grupo as $turma): ?>

                                        <div class="turma-card-relacionamento" id="card_<?= $turma['id_turma'] ?>"

                                            onclick="toggleCardClick(event, '<?= $turma['id_turma'] ?>');">

                                                       

                                            <input type="checkbox" id="incluir_<?= $turma['id_turma'] ?>"

                                                    name="turmas[<?= $turma['id_turma'] ?>][incluir]"

                                                    value="1" style="display: none;"

                                                    onchange="toggleCargaFields(this); toggleCardStyle(this);">

                                                       

                                            <h4 style="margin-top: 0; color: #48ab87; display: flex; justify-content: space-between; align-items: center;">

                                                <span><?= htmlspecialchars($turma['nome_turma']) ?></span>

                                                <i class="fas fa-toggle-off toggle-icon" style="color: #ccc;"></i>

                                            </h4>

                                                       

                                            <input type="hidden" name="turmas[<?= $turma['id_turma'] ?>][id_turma]" value="<?= $turma['id_turma'] ?>">



                                            <div class="carga-fields" id="carga_fields_<?= $turma['id_turma'] ?>" style="display: none; margin-top: 10px; padding-top: 1px dashed #eee;">

                                                               

                                                <div class="form-group">

                                                    <label for="aulas_semanais_<?= $turma['id_turma'] ?>">Aulas Semanais:</label>

                                                    <input type="number" id="aulas_semanais_<?= $turma['id_turma'] ?>"

                                                            name="turmas[<?= $turma['id_turma'] ?>][aulas_semanais]"

                                                            min="1" value="0" placeholder="Aulas por semana" style="width: 100%;">

                                                </div>

                                                               

                                                <div class="form-group" style="margin-bottom: 0;">

                                                    <label for="carga_horaria_<?= $turma['id_turma'] ?>">Carga Horária Total (Anual/Semestral):</label>

                                                    <input type="number" id="carga_horaria_<?= $turma['id_turma'] ?>"

                                                            name="turmas[<?= $turma['id_turma'] ?>][carga_horaria]"

                                                            min="1" value="0" placeholder="Total de horas" style="width: 100%;">

                                                </div>

                                            </div>

                                        </div>

                                    <?php endforeach; ?>

                                    </div>

                                </div>

                            <?php endforeach; ?>

                        <?php else: ?>

                            <p>Nenhuma turma cadastrada. Por favor, cadastre turmas para alocar disciplinas.</p>

                        <?php endif; ?>

                        </div>

                    </fieldset>



                    <button type="submit" class="btn-salvar" style="margin-top: 25px;">

                        <i class="fas fa-save"></i> Salvar Relacionamento & Cargas

                    </button>

                </form>

            </div>

        </div>

       

        <div id="dashboard-home" style="display: none;" class="content-section">

            <div class="main-header">

                <h1>Bem-vindo, Administrador!</h1>

                <p>Escolha uma opção no menu para gerenciar usuários, disciplinas e horários.</p>

            </div>

        </div>



        <div id="aprovacoes-section" style="display: none;" class="content-section">

            <h2>Aprovações Pendentes</h2>

            <p>Conteúdo da seção de aprovações virá aqui.</p>

        </div>

        <div id="professores-section" style="display: none;" class="content-section">

            <h2>Listar Professores</h2>

            <p>Conteúdo da seção de professores virá aqui.</p>

        </div>

        <div id="alunos-section" style="display: none;" class="content-section">

            <h2>Listar Alunos</h2>

            <p>Conteúdo da seção de alunos virá aqui.</p>

        </div>

        <div id="turmas-section" style="display: none;" class="content-section">

            <h2>Listar Turmas</h2>

            <p>Conteúdo da seção de turmas virá aqui.</p>

        </div>



    </main>

   

    <script src="js/custom-scripts.js"></script>

   

</body>