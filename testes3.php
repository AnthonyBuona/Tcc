<?php
session_start();
// Inclui a conexão com o banco de dados.
include 'includes/config.php'; 

// Nome da tabela a ser usada
$tabela_restricao = 'disponibilidade_prof';

$mensagem_sucesso = '';
$mensagem_erro = '';

// --- LÓGICA DE MANIPULAÇÃO DE DADOS ---

// 2. DIAS E HORÁRIOS FIXOS (AJUSTADOS PARA TERMINAR EM 18:00)
$dias_semana = ['Segunda', 'Terça', 'Quarta', 'Quinta', 'Sexta'];
$horarios_aula = [
    '07:30 - 08:20', '08:20 - 09:10', '09:10 - 10:00', '10:20 - 11:10',
    '13:30 - 14:20', '14:20 - 15:10', '15:10 - 16:00', '16:20 - 17:10', 
    '17:10 - 18:00' // <-- ÚLTIMO HORÁRIO
];


// 1. CARREGAR PROFESSORES (INCLUINDO NOVOS CAMPOS DO PERFIL)
$professores = [];
$sql_prof = "SELECT id_prof, nome AS nome_prof, trabalha_outro_lugar, horario_saida_outro_lugar FROM professor ORDER BY nome ASC"; 

$result_prof = mysqli_query($conexao, $sql_prof);
if ($result_prof) {
    $professores = mysqli_fetch_all($result_prof, MYSQLI_ASSOC);
    mysqli_free_result($result_prof);
} else {
    $mensagem_erro = "Erro ao carregar lista de professores: " . mysqli_error($conexao);
}

// 3. LÓGICA PARA SALVAR RESTRIÇÕES (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao']) && $_POST['acao'] === 'salvar_restricoes') {
    $id_prof_selecionado = (int)$_POST['id_prof_selecionado'];
    $restricoes = isset($_POST['restricoes']) ? $_POST['restricoes'] : []; 

    if ($id_prof_selecionado > 0) {
        
        // Limpar restrições existentes (usando disponibilidade_prof)
        $stmt_del = $conexao->prepare("DELETE FROM {$tabela_restricao} WHERE id_prof = ?");
        
        if (!$stmt_del) {
            $mensagem_erro = "Erro de preparação do DELETE: **" . $conexao->error . "**";
        } else {
            $stmt_del->bind_param("i", $id_prof_selecionado);
            $stmt_del->execute();
            $stmt_del->close();
        }
        
        if (!empty($restricoes) && empty($mensagem_erro)) {
            // Inserir novas restrições
            $sql_insert = "INSERT INTO {$tabela_restricao} (id_prof, dia, horario) VALUES (?, ?, ?)";
            $stmt_insert = $conexao->prepare($sql_insert);
            
            if (!$stmt_insert) {
                $mensagem_erro = "Erro de preparação do INSERT: **" . $conexao->error . "**";
            } else {
                $insercoes_bem_sucedidas = 0;

                foreach ($restricoes as $restricao) {
                    list($dia, $horario) = explode('_', $restricao, 2);
                    
                    $stmt_insert->bind_param("iss", $id_prof_selecionado, $dia, $horario); 
                    if ($stmt_insert->execute()) {
                        $insercoes_bem_sucedidas++;
                    } else {
                         $mensagem_erro .= (empty($mensagem_erro) ? '' : '<br>') . "Erro de execução do INSERT: " . $stmt_insert->error;
                    }
                }
                $stmt_insert->close();
            }
        }

        if (empty($mensagem_erro) && (empty($restricoes) || $insercoes_bem_sucedidas > 0)) {
            $mensagem_sucesso = "Restrições de horário salvas com sucesso para o professor (ID: {$id_prof_selecionado}).";
        } else if (empty($mensagem_sucesso)) {
            $mensagem_erro = "Erro ao salvar restrições. Detalhe: " . $mensagem_erro;
        }
    } else {
        $mensagem_erro = "Selecione um professor válido.";
    }
}


// --- BLOCO: LÓGICA PARA SALVAR INFORMAÇÕES DE PERFIL (POST) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao']) && $_POST['acao'] === 'salvar_perfil') {
    $id_prof_selecionado_perfil = (int)$_POST['id_prof_selecionado_perfil'];
    $trabalha_outro_lugar_input = isset($_POST['trabalha_outro_lugar']) ? (int)$_POST['trabalha_outro_lugar'] : 0;
    $horario_saida_input = trim($_POST['horario_saida_outro_lugar']);

    // Prepara o valor para o DB: NULL se não trabalhar em outro lugar, ou o horário
    $horario_saida_db = $trabalha_outro_lugar_input == 1 && !empty($horario_saida_input) ? $horario_saida_input : NULL;

    if ($id_prof_selecionado_perfil > 0) {
        $sql_update = "UPDATE professor SET trabalha_outro_lugar = ?, horario_saida_outro_lugar = ? WHERE id_prof = ?";
        $stmt_update = $conexao->prepare($sql_update);

        if (!$stmt_update) {
            $mensagem_erro = "Erro de preparação do UPDATE de Perfil: " . $conexao->error;
        } else {
            $stmt_update->bind_param("isi", $trabalha_outro_lugar_input, $horario_saida_db, $id_prof_selecionado_perfil);
            if ($stmt_update->execute()) {
                $mensagem_sucesso = "Informações de perfil atualizadas com sucesso.";
            } else {
                $mensagem_erro = "Erro ao atualizar informações de perfil: " . $stmt_update->error;
            }
            $stmt_update->close();
        }
        // Redefine o ID para recarregar o estado correto da página
        $_POST['id_prof_selecionado'] = $id_prof_selecionado_perfil;
    }
}


// 4. LÓGICA PARA CARREGAR RESTRIÇÕES E PERFIL
$restricoes_selecionadas = [];
$professor_selecionado = null;
$id_prof_selecionado = 0;

// O ID a ser carregado vem do GET (seleção inicial) ou do POST (após salvar)
$id_prof_para_carregar = isset($_GET['id_prof']) ? (int)$_GET['id_prof'] : (isset($_POST['id_prof_selecionado']) ? (int)$_POST['id_prof_selecionado'] : 0);

if ($id_prof_para_carregar > 0) {
    $id_prof_selecionado = $id_prof_para_carregar;
    
    // Encontra o professor e seus dados de perfil na lista recém-carregada (ou recarregada)
    foreach ($professores as $prof) {
        if ($prof['id_prof'] == $id_prof_selecionado) {
            $professor_selecionado = $prof;
            break;
        }
    }
    
    // Variáveis de Perfil para uso no HTML
    $trabalha_outro_lugar = $professor_selecionado['trabalha_outro_lugar'] ?? 0;
    $horario_saida_outro_lugar = $professor_selecionado['horario_saida_outro_lugar'] ?? '';

    if ($professor_selecionado) {
        // Carrega as restrições salvas
        $sql_restricoes = "SELECT dia, horario FROM {$tabela_restricao} WHERE id_prof = ?";
        $stmt_rest = $conexao->prepare($sql_restricoes);
        
        if (!$stmt_rest) {
            $mensagem_erro .= (empty($mensagem_erro) ? '' : '<br>') . "Erro de preparação do SELECT: **" . $conexao->error . "**";
        } else {
            $stmt_rest->bind_param("i", $id_prof_selecionado);
            $stmt_rest->execute();
            $result_rest = $stmt_rest->get_result();
            
            while ($row = $result_rest->fetch_assoc()) {
                $restricoes_selecionadas[] = $row['dia'] . '_' . $row['horario'];
            }
            $stmt_rest->close();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>PlanIt - Admin | Restrições de Horário</title>
    <link rel="stylesheet" href="css/dashboard.css" />
    <link rel="stylesheet" href="css/disciplina.css" /> 
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" crossorigin="anonymous" />
    <style>
        /* Estilos da tabela e select */
        .restricao-table-container { max-width: 100%; overflow-x: auto; margin-top: 20px; }
        .restricao-table { width: 100%; border-collapse: collapse; text-align: center; }
        .restricao-table th, .restricao-table td { border: 1px solid #ddd; padding: 8px; min-width: 80px; vertical-align: middle; }
        
        /* CORREÇÃO AQUI: Fundo verde e texto branco para o cabeçalho */
        .restricao-table th { 
            background-color: #48ab87; /* Verde do tema */
            color: #fff; /* Texto branco */
            font-weight: 600; 
        }

        .restricao-table td { cursor: pointer; transition: background-color 0.2s; }
        .restricao-table td.restrito { background-color: #ffcccc; }
        .restricao-table td:not(.restrito):hover { background-color: #e0f2f1; }
        .custom-select-like { padding: 10px; border: 1px solid #ccc; border-radius: 4px; width: 100%; box-sizing: border-box; background-color: white; }
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
                    <li><a href="alocacao.php">Atribuir Aulas</a></li>
                    <li style="background-color: rgba(72,171,135,0.9); color: #fff; font-weight: 500;"><a href="restricoes_professor.php">Restrições de Professor</a></li>
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
        <div id="restricoes-professor-section" class="content-section">
            <div class="main-header">
                <h1 style="color: #48ab87;">Restrições de Horário de Professor</h1>
            </div>
            
            <?php if (!empty($mensagem_sucesso)): ?>
                <div class="message-success"><?= $mensagem_sucesso; ?></div>
            <?php endif; ?>
            <?php if (!empty($mensagem_erro)): ?>
                <div class="message-error"><?= $mensagem_erro; ?></div>
            <?php endif; ?>

            <div class="content-panel">
                <fieldset>
                    <legend>Selecionar Professor</legend>
                    
                    <form method="GET">
                        <div class="form-group" style="max-width: 400px;">
                            <label for="id_prof_selecionar">Professor:</label>
                            <select id="id_prof_selecionar" name="id_prof" required class="custom-select-like">
                                <option value="">--- Selecione um Professor ---</option>
                                <?php foreach ($professores as $prof): ?>
                                    <option 
                                        value="<?= htmlspecialchars($prof['id_prof']); ?>"
                                        <?= ($id_prof_selecionado == $prof['id_prof']) ? 'selected' : ''; ?>
                                    >
                                        <?= htmlspecialchars($prof['nome_prof']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <button type="submit" class="btn-salvar" style="margin-top: 15px;">
                            <i class="fas fa-search"></i> Carregar Dados
                        </button>
                    </form>
                </fieldset>
            </div>

            <?php if ($professor_selecionado): ?>
                
                <div class="content-panel" style="margin-top: 20px;">
                    <fieldset>
                        <legend>Perfil do Professor: <?= htmlspecialchars($professor_selecionado['nome_prof']); ?></legend>
                        
                        <form method="POST" action="testes3.php">
                            <input type="hidden" name="acao" value="salvar_perfil">
                            <input type="hidden" name="id_prof_selecionado_perfil" value="<?= $id_prof_selecionado; ?>">
                            
                            <div class="form-group" style="max-width: 400px;">
                                <label for="trabalha_outro_lugar">Trabalha em outro local?</label>
                                <select id="trabalha_outro_lugar" name="trabalha_outro_lugar" class="custom-select-like">
                                    <option value="0" <?= $trabalha_outro_lugar == 0 ? 'selected' : ''; ?>>Não</option>
                                    <option value="1" <?= $trabalha_outro_lugar == 1 ? 'selected' : ''; ?>>Sim</option>
                                </select>
                            </div>
                            
                            <div class="form-group" id="horario-saida-group" style="max-width: 400px; display: <?= $trabalha_outro_lugar == 1 ? 'block' : 'none'; ?>;">
                                <label for="horario_saida_outro_lugar">Horário de Saída (do outro trabalho):</label>
                                <input 
                                    type="time" 
                                    id="horario_saida_outro_lugar" 
                                    name="horario_saida_outro_lugar" 
                                    class="custom-select-like"
                                    value="<?= htmlspecialchars($horario_saida_outro_lugar); ?>"
                                />
                            </div>

                            <button type="submit" class="btn-salvar" style="margin-top: 15px;">
                                <i class="fas fa-user-edit"></i> Salvar Perfil
                            </button>
                        </form>
                    </fieldset>
                </div>

                <div class="content-panel" style="margin-top: 20px;">
                    <fieldset>
                        <legend>Restrições de Horário</legend>
                        
                        <form method="POST" action="testes3.php">
                            <input type="hidden" name="acao" value="salvar_restricoes">
                            <input type="hidden" name="id_prof_selecionado" value="<?= $id_prof_selecionado; ?>">
                            
                            <div class="restricao-table-container">
                                <table class="restricao-table">
                                    <thead>
                                        <tr>
                                            <th>Horário</th>
                                            <?php foreach ($dias_semana as $dia): ?>
                                                <th><?= $dia; ?></th>
                                            <?php endforeach; ?>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($horarios_aula as $horario): ?>
                                            <tr>
                                                <td style="font-weight: 600; white-space: nowrap;"><?= $horario; ?></td>
                                                <?php foreach ($dias_semana as $dia): ?>
                                                    <?php 
                                                        $identificador = $dia . '_' . $horario;
                                                        $is_restrito = in_array($identificador, $restricoes_selecionadas);
                                                    ?>
                                                    <td 
                                                        class="restricao-celula <?= $is_restrito ? 'restrito' : ''; ?>"
                                                        data-identificador="<?= htmlspecialchars($identificador); ?>"
                                                    >
                                                        <input 
                                                            type="checkbox" 
                                                            name="restricoes[]" 
                                                            value="<?= htmlspecialchars($identificador); ?>"
                                                            <?= $is_restrito ? 'checked' : ''; ?>
                                                            style="display: none;"
                                                        />
                                                    </td>
                                                <?php endforeach; ?>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            
                            <button type="submit" class="btn-salvar" style="margin-top: 25px;">
                                <i class="fas fa-save"></i> Salvar Restrições
                            </button>
                        </form>
                    </fieldset>
                </div>
            <?php endif; ?>

        </div>
    </main>
    <script>
        // --- JS para interatividade da tabela de restrições e perfil ---
        document.addEventListener('DOMContentLoaded', function() {
            const cells = document.querySelectorAll('.restricao-celula');
            const selectTrabalhaOutroLugar = document.getElementById('trabalha_outro_lugar');
            const horarioSaidaGroup = document.getElementById('horario-saida-group');

            // 1. Lógica da Tabela de Restrições
            cells.forEach(cell => {
                cell.addEventListener('click', function() {
                    const checkbox = this.querySelector('input[type="checkbox"]');
                    
                    checkbox.checked = !checkbox.checked;
                    this.classList.toggle('restrito', checkbox.checked);
                });
            });

            // 2. Lógica do Perfil (Exibir/Esconder Horário de Saída)
            function toggleHorarioSaida() {
                // Checa se o valor selecionado é '1' (Sim)
                if (selectTrabalhaOutroLugar.value === '1') {
                    horarioSaidaGroup.style.display = 'block';
                } else {
                    horarioSaidaGroup.style.display = 'none';
                    // Ao esconder, o campo é limpo para evitar salvar um horário antigo com a flag "Não"
                    document.getElementById('horario_saida_outro_lugar').value = ''; 
                }
            }

            selectTrabalhaOutroLugar.addEventListener('change', toggleHorarioSaida);

            // Garante que o estado inicial (vindo do PHP) seja respeitado no DOMContentLoaded
            toggleHorarioSaida(); 
        });
    </script>
</body>
</html>