<?php
session_start();
// Inclui a conexão com o banco de dados. Assume-se que $conexao está definida em config.php
include 'includes/config.php'; 

// --- LÓGICA PHP PARA LISTAGEM DE TURMAS (Primeiro para mapeamento) ---
$sql_turmas = "SELECT * FROM turma ORDER BY id_turma ASC";
$result_turmas = mysqli_query($conexao, $sql_turmas);
$turmas = mysqli_fetch_all($result_turmas, MYSQLI_ASSOC);
mysqli_free_result($result_turmas);

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
        'aluno' AS tipo_acesso, 
        id_turma AS detalhe 
    FROM aluno 
    WHERE status_aprovacao = 'PENDENTE'
    
    UNION ALL
    
    SELECT 
        id_prof AS id, 
        nome, 
        cpf, 
        'professor' AS tipo_acesso, 
        areas AS detalhe 
    FROM professor 
    WHERE status_aprovacao = 'PENDENTE'
    ORDER BY id ASC
";

$result_pendentes = mysqli_query($conexao, $sql_pendentes_unificado); 
$pendentes = mysqli_fetch_all($result_pendentes, MYSQLI_ASSOC); 
$total_pendentes = count($pendentes);

// --- LÓGICA PHP PARA LISTAGEM DE PROFESSORES ---
$sql_professores = "SELECT id_prof, nome, cpf, areas, status_aprovacao FROM professor ORDER BY id_prof DESC";
$result_professores = mysqli_query($conexao, $sql_professores);
$professores = mysqli_fetch_all($result_professores, MYSQLI_ASSOC);
mysqli_free_result($result_professores); 

// --- LÓGICA PHP PARA LISTAGEM DE ALUNOS ---
$sql_alunos = "SELECT id_aluno, nome, cpf, id_turma, status_aprovacao FROM aluno ORDER BY id_aluno DESC";
$result_alunos = mysqli_query($conexao, $sql_alunos);
$alunos = mysqli_fetch_all($result_alunos, MYSQLI_ASSOC);
mysqli_free_result($result_alunos);

// --- Contagem de alunos por turma (para exibir "Total Alunos") ---
$contagem_turmas = [];
foreach ($alunos as $a) {
    $tid = $a['id_turma'] ?? null;
    if ($tid !== null && $tid !== '') {
        if (!isset($contagem_turmas[$tid])) $contagem_turmas[$tid] = 0;
        $contagem_turmas[$tid]++;
    }
}


// Pré-processamento das áreas dos professores para os filtros
$areas_professores = [];
foreach($professores as $p) {
    $areas_professores = array_merge($areas_professores, explode(',', $p['areas']));
}
$areas_professores = array_unique(array_map('trim', $areas_professores));
?>



<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>PlanIt - Admin | Dashboard</title>
    <link rel="stylesheet" href="css/dashboard.css" /> 
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" crossorigin="anonymous" /> 
</head>
<body>

    <header>
        <div class="logo" onclick="mostrarSecao('dashboard-home')" style="cursor: pointer;">PlanIt 🦉</div>
        <div class="profile">
            <span>Administrador</span>
            <img src="img/avatar.jpg" alt="Avatar" />
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

                <li class="menu-title">Gerenciamento de Condições</li>
                <ul class="submenu">
                    <li><a href="">Restrição Porfessor</a></li>
                    <li><a href="">Carga Horária</a></li>
                </ul>

                <li class="menu-title">Gerenciamento de Disciplinas</li>
                <ul class="submenu">
                    <li><a href="">Cadastrar Disciplina</a></li>
                    <li><a href="">Visualizar Disciplina</a></li>
                </ul>

                <li class="menu-title">Alocação de Horários</li>
                <ul class="submenu">
                    <li><a href="#" onclick="mostrarSecao('atribuicao-section'); carregarAtribuicaoAulas(); return false;">Atribuir Aulas</a></li>
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
    
    <div id="dashboard-home">
        <div class="main-header">
            <h1>Bem-vindo, Administrador!</h1>
            <p>Escolha uma opção no menu para gerenciar usuários, disciplinas e horários.</p>
        </div>
    </div>
    
    <div id="aprovacoes-section" style="display:none;">
        <div class="main-header">
            <h1>Gerenciamento de Usuários</h1>
            <p>Aprovação de novos cadastros de alunos e professores.</p>
        </div>
        
        <br />
        
        <div class="listagem">
            <h3>Cadastros Pendentes de Aprovação</h3>
            
            <?php if ($total_pendentes > 0): ?>
                <div class="alerta-aprovacao">
                    <?= $total_pendentes; ?> usuário(s) aguardando sua aprovação para acessar o sistema.
                </div>
                
                <h4>Usuários Pendentes (<?= $total_pendentes; ?>)</h4>
                <table class="tabela-pendentes">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Tipo</th>
                            <th>Nome</th>
                            <th>CPF</th>
                            <th>E-mail</th>
                            <th>Detalhe (Turma/Áreas)</th>
                            <th>Ação</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($pendentes as $usuario): ?>
                            <?php 
                                $detalhe_display = htmlspecialchars($usuario['detalhe']);
                                if ($usuario['tipo_acesso'] === 'aluno' && isset($mapa_turmas[$usuario['detalhe']])) {
                                    $detalhe_display = htmlspecialchars($mapa_turmas[$usuario['detalhe']]);
                                }
                            ?>
                            <tr id="linha-<?= $usuario['tipo_acesso']; ?>-<?= $usuario['id']; ?>">
                                <td><?= htmlspecialchars($usuario['id']); ?></td>
                                <td><?= htmlspecialchars($usuario['tipo_acesso']); ?></td>
                                <td><?= htmlspecialchars($usuario['nome']); ?></td>
                                <td><?= htmlspecialchars($usuario['cpf']); ?></td>
                                <td><?= htmlspecialchars($usuario['email'] ?? 'N/A'); ?></td>
                                <td><?= $detalhe_display; ?></td>
                                <td>
                                    <button class="btn-aprovar" 
                                        onclick="processarUsuario('<?= $usuario['id']; ?>', '<?= $usuario['tipo_acesso']; ?>', 'aprovar')">
                                        Aprovar
                                    </button>
                                    <button class="btn-reprovar" 
                                        onclick="processarUsuario('<?= $usuario['id']; ?>', '<?= $usuario['tipo_acesso']; ?>', 'reprovar')">
                                        Reprovar
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>Nenhum cadastro pendente no momento. Tudo certo!</p>
            <?php endif; ?>

        </div>
    </div>
    <div id="professores-section" style="display:none;">
        <div class="main-header">
            <h1>Listagem de Professores</h1>

            <div style="display: flex; gap: 15px; align-items: center; flex-wrap: wrap; margin-bottom: 20px;">
                <div class="search-bar" style="flex-grow: 1; min-width: 250px; max-width: 400px; margin-bottom: 0;">
                    <input type="text" id="searchInputProf" placeholder="Buscar por nome..." autocomplete="off" onkeyup="filtrarProfessores()" />
                    <i class="fas fa-search"></i>
                </div>
                
                <div class="search-bar" style="flex-grow: 1; max-width: 250px; margin-bottom: 0; justify-content: flex-start;">
                    <select id="filtroArea" onchange="filtrarProfessores()" 
                                style="padding: 8px 10px; border: 1px solid #ccc; border-radius: 4px; flex-grow: 1; width: auto; min-width: 150px;">
                        <option value="" selected disabled>Filtrar por área</option>
                        <?php foreach($areas_professores as $a): ?>
                            <option value="<?= htmlspecialchars($a) ?>"><?= htmlspecialchars($a) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <button class="btn-limpar" onclick="limparFiltroProfessores()" style="background: none; border: none; cursor: pointer; margin-left: -5px;">×</button>
                </div>
            </div>
        </div>

        <div class="listagem">
            <?php if (empty($professores)): ?>
                <p>Nenhum professor encontrado.</p>
            <?php else: ?>
                <table id="professoresTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nome</th>
                            <th>CPF</th>
                            <th>Áreas</th>
                            <th>Status</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($professores as $row): ?>
                            <tr data-id="<?= $row['id_prof']; ?>" data-area="<?= htmlspecialchars($row['areas']); ?>">
                                <td><?= htmlspecialchars($row['id_prof']); ?></td>
                                <td class="nome"><?= htmlspecialchars($row['nome']); ?></td>
                                <td><?= htmlspecialchars($row['cpf']); ?></td>
                                <td><?= htmlspecialchars($row['areas']); ?></td>
                                <td>
                                    <?php if ($row['status_aprovacao'] === 'APROVADO'): ?>
                                        <span class="status-aprovado">Aprovado</span>
                                    <?php else: ?>
                                        <span class="status-pendente">Pendente</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="acoes">
                                        <button 
                                            type="button" 
                                            class="btn-acao btn-editar tooltip btn-editar-modal" 
                                            data-tooltip="Editar"
                                            data-id="<?= $row['id_prof']; ?>"
                                        >
                                            <i class="fa-regular fa-pen-to-square"></i>
                                        </button>

                                        <button
                                            class="btn-acao btn-excluir tooltip btn-excluir-ajax"
                                            data-tooltip="Excluir"
                                            data-id="<?= $row['id_prof']; ?>"
                                            data-tipo="professor" >
                                            <i class="fa-regular fa-trash-can"></i>
                                        </button>

                                        <?php if ($row['status_aprovacao'] !== 'APROVADO'): ?>
                                            <button
                                                class="btn-acao btn-aprovar tooltip btn-aprovar-ajax"
                                                data-tooltip="Aprovar solicitação"
                                                data-id="<?= $row['id_prof']; ?>"
                                                data-tipo="professor" >
                                                <i class="fa-solid fa-check"></i>
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>

    <div id="alunos-section" style="display:none;">
        <div class="main-header">
            <h1>Listagem de Alunos</h1>
            
            <div style="display: flex; gap: 15px; align-items: center; flex-wrap: wrap; margin-bottom: 20px;">
                <div class="search-bar" style="flex-grow: 1; min-width: 250px; max-width: 400px; margin-bottom: 0;">
                    <input type="text" id="searchInputAluno" placeholder="Buscar por nome..." autocomplete="off" onkeyup="filtrarAlunos()" />
                    <i class="fas fa-search"></i>
                </div>
                
                <div class="search-bar" style="flex-grow: 2; margin-bottom: 0; justify-content: flex-start; gap: 10px;">
                    <select id="filtroAno" onchange="filtrarAlunos()" style="padding: 8px 10px; border: 1px solid #ccc; border-radius: 4px; flex-grow: 1; min-width: 100px;">
                        <option value="" selected disabled>Ano</option>
                        <option value="1">1º</option><option value="2">2º</option><option value="3">3º</option>
                    </select>
                    <select id="filtroCurso" onchange="filtrarAlunos()" style="padding: 8px 10px; border: 1px solid #ccc; border-radius: 4px; flex-grow: 2; min-width: 150px;">
                        <option value="" selected disabled>Curso</option>
                        <option value="Mecatrônica">Mecatrônica</option>
                        <option value="DS">DS</option>
                        <option value="Linguagens">Linguagens</option>
                    </select>
                    <button class="btn-limpar" onclick="limparFiltroAlunos()" style="background: none; border: none; cursor: pointer;">×</button>
                </div>
            </div>
        </div>

        <div class="listagem">
            <?php if (empty($alunos)): ?>
                <p>Nenhum aluno encontrado.</p>
            <?php else: ?>
                <table id="alunosTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nome</th>
                            <th>CPF</th>
                            <th>Turma (ID)</th>
                            <th>Status</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        foreach ($alunos as $row): 
                            $turma_nome = '';
                            foreach($turmas as $t) {
                                if ($t['id_turma'] == $row['id_turma']) {
                                    $turma_nome = $t['nome_turma'];
                                    break;
                                }
                            }
                            preg_match('/(\d+)°/', $turma_nome, $m); 
                            $ano = $m[1] ?? ''; 
                            $curso = ''; 
                            if(stripos($turma_nome, 'mecatr') !== false) $curso = 'Mecatrônica';
                            elseif(stripos($turma_nome, 'desenvolvimento') !== false || stripos($turma_nome, 'ds') !== false) $curso = 'DS';
                            elseif(stripos($turma_nome, 'linguagens') !== false) $curso = 'Linguagens';
                        ?>
                            <tr data-id="<?= $row['id_aluno']; ?>" data-ano="<?= $ano ?>" data-curso="<?= $curso ?>">
                                <td><?= htmlspecialchars($row['id_aluno']); ?></td>
                                <td class="nome"><?= htmlspecialchars($row['nome']); ?></td>
                                <td><?= htmlspecialchars($row['cpf']); ?></td>
                                <td><?= htmlspecialchars($row['id_turma']); ?> (<?= htmlspecialchars($turma_nome); ?>)</td>
                                <td>
                                    <?php if ($row['status_aprovacao'] === 'APROVADO'): ?>
                                        <span class="status-aprovado">Aprovado</span>
                                    <?php else: ?>
                                        <span class="status-pendente">Pendente</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="acoes">
                                        <button 
                                            type="button" 
                                            class="btn-acao btn-editar tooltip btn-editar-modal-aluno" 
                                            data-tooltip="Editar"
                                            data-id="<?= $row['id_aluno']; ?>"
                                        >
                                            <i class="fa-regular fa-pen-to-square"></i>
                                        </button>

                                        <button
                                            class="btn-acao btn-excluir tooltip btn-excluir-ajax"
                                            data-tooltip="Excluir"
                                            data-id="<?= $row['id_aluno']; ?>"
                                            data-tipo="aluno"
                                        >
                                            <i class="fa-regular fa-trash-can"></i>
                                        </button>

                                        <?php if ($row['status_aprovacao'] !== 'APROVADO'): ?>
                                            <button
                                                class="btn-acao btn-aprovar tooltip btn-aprovar-ajax"
                                                data-tooltip="Aprovar solicitação"
                                                data-id="<?= $row['id_aluno']; ?>"
                                                data-tipo="aluno"
                                            >
                                                <i class="fa-solid fa-check"></i>
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
    
    <div id="turmas-section" style="display:none;">
        <div class="main-header">
            <h1>Listagem de Turmas</h1>
        </div>
        
        <div class="search-bar" style="display: flex; gap: 10px; align-items: center; max-width: 500px; margin-bottom: 20px;">
             <select id="filtroAnoTurma" onchange="filtrarTurmas()" style="padding: 8px 10px; border: 1px solid #ccc; border-radius: 4px; flex-grow: 1;">
                 <option value="" selected disabled>Ano</option>
                 <option value="1">1º</option><option value="2">2º</option><option value="3">3º</option>
             </select>
             <select id="filtroCursoTurma" onchange="filtrarTurmas()" style="padding: 8px 10px; border: 1px solid #ccc; border-radius: 4px; flex-grow: 2;">
                 <option value="" selected disabled>Curso</option>
                 <option value="Mecatrônica">Mecatrônica</option>
                 <option value="DS">DS</option>
                 <option value="Linguagens">Linguagens</option>
             </select>
             <button class="btn-limpar" onclick="limparFiltroTurmas()" style="background: none; border: none; cursor: pointer;">×</button>
        </div>
        
        <div class="listagem">
            <?php if (empty($turmas)): ?>
                <p>Nenhuma turma encontrada.</p>
            <?php else: ?>
                <table id="turmasTable">
                    <thead>
                        <tr>
                            <th>ID</th><th>Nome</th><th>Turno</th><th>Capacidade</th><th>Série</th><th>Curso</th><th>Total Alunos</th>
                            </tr>
                    </thead>
                    <tbody>
                    <?php foreach($turmas as $t):
                        preg_match('/(\d+)°/',$t['nome_turma'],$m); $ano=$m[1]??''; 
                        $curso=''; 
                        
                        // As duas primeiras linhas permanecem iguais
                        if(stripos($t['nome_turma'],'mecatr')!==false) $curso='Mecatrônica';
                        elseif(stripos($t['nome_turma'],'desenvolvimento')!==false || stripos($t['nome_turma'],'ds')!==false) $curso='DS';
                        
                        // LINHA CORRIGIDA: Agora checa por 'linguagens' OU 'ling'
                        elseif(stripos($t['nome_turma'],'linguagens')!==false || stripos($t['nome_turma'],'ling')!==false) $curso='Linguagens';
                    ?>
                        <tr data-ano="<?= $ano ?>" data-curso="<?= $curso ?>">
                            <td><?= htmlspecialchars($t['id_turma']) ?></td>
                            <td><?= htmlspecialchars($t['nome_turma']) ?></td>
                            <td><?= htmlspecialchars($t['turno']) ?></td>
                            <td><?= htmlspecialchars($t['capacidade']) ?></td>
                            <td><?= htmlspecialchars($ano) ?></td>
                            <td><?= htmlspecialchars($curso) ?></td>
                            <td><?= htmlspecialchars($contagem_turmas[$t['id_turma']] ?? 0) ?></td>
                            </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
                        
    <div id="atribuicao-section" style="display:none;" class="content-section">
        <p style="text-align:center; padding: 20px;">Carregando grade...</p>
    </div>
    
    </main>

<div id="editModal" class="modal-overlay" style="display:none;">
    <div class="modal-content">
        <h2>Editar Professor</h2>
        
        <form id="editForm">
            <input type="hidden" id="edit-id_prof" name="id_prof">
            
            <label for="edit-nome">Nome:</label>
            <input type="text" id="edit-nome" name="nome" required>

            <label>Áreas:</label>
            <div class="multi-select-wrapper">
                <div class="multi-select" id="multi-select-display">Área(s) que você ensina...</div>
                <div class="options" id="multi-select-options">
                    <div class="option" data-value="Humanas">Ciências Humanas e Sociais</div>
                    <div class="option" data-value="Exatas">Ciências Exatas</div>
                    <div class="option" data-value="Biologicas">Ciências Biológicas</div>
                    <div class="option" data-value="Linguagens">Linguagens e Comunicação</div>
                    <div class="option" data-value="Informatica">Informática / TI</div>
                    <div class="option" data-value="Administracao">Administração e Negócios</div>
                    <div class="option" data-value="Saude">Saúde</div>
                    <div class="option" data-value="Engenharia">Engenharia e Tecnologia</div>
                    <div class="option" data-value="Design">Design e Comunicação Visual</div>
                    <div class="option" data-value="MeioAmbiente">Meio Ambiente e Agroindústria</div>
                    <div class="option" data-value="Moda">Moda e Estética</div>
                </div>
                <input type="hidden" name="areas" id="edit-areas" /> 
            </div>
            
            <div class="modal-actions">
                <button type="button" class="btn-cancelar">Cancelar</button>
                <button type="submit" class="btn-salvar">Salvar Alterações</button>
            </div>
        </form>
    </div>
</div>
    
<div id="editModalAluno" class="modal-overlay" style="display:none;">
    <div class="modal-content">
        <h2>Editar Aluno</h2>
        
        <form id="editFormAluno">
            <input type="hidden" id="edit-id_aluno" name="id_aluno">
            <input type="hidden" id="edit-cpf-aluno" name="cpf"> 
            
            <label for="edit-nome-aluno">Nome:</label>
            <input type="text" id="edit-nome-aluno" name="nome" required>
            
            <label>Série:</label>
            <div class="input-box">
                <div class="custom-select-wrapper" id="edit-wrapper-serie">
                    <div class="custom-select" tabindex="0">Carregando Séries...</div>
                    <div class="options">
                        </div>
                    <input type="hidden" name="id_serie" required /> 
                </div>
            </div>

            <label>Turma:</label>
            <div class="input-box">
                <div class="custom-select-wrapper" id="edit-wrapper-turma">
                    <div class="custom-select" tabindex="0">Selecione uma Série primeiro</div>
                    <div class="options">
                        </div>
                    <input type="hidden" name="id_turma" required /> 
                </div>
            </div>
            
            <div class="modal-actions">
                <button type="button" class="btn-cancelar-aluno">Cancelar</button>
                <button type="submit" class="btn-salvar">Salvar Alterações</button>
            </div>
        </form>
    </div>
</div>

    <script src="js/filterTable.js"></script>
    <script src="js/funcoes_dashboard.js"></script>
    <script src="js/dashboard_pendente.js"></script> 
    <script src="js/listagem_alunos.js"></script> 
    <script src="js/listagem_professores.js"></script> 
    <script src="js/dashboard.js"></script> 
    <script src="js/dashboard_filtros.js"></script> 
    <script src="js/dashboard_atribuicao.js"></script>
    
</body>
</html>