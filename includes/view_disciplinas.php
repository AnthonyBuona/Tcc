<?php
// Arquivo: includes/view_disciplinas.php
session_start();
include 'config.php';

// Busca todas as disciplinas
$sql = "SELECT * FROM disciplina ORDER BY nome_disc ASC";
$result = mysqli_query($conexao, $sql);
$disciplinas = mysqli_fetch_all($result, MYSQLI_ASSOC);

// Lista de áreas para o filtro/select (pode ser fixa ou dinâmica)
$areas_sugestao = [
    'Ciências Exatas', 'Ciências Humanas e Sociais', 'Ciências Biológicas', 
    'Linguagens e Comunicação', 'Informática / TI', 'Engenharia e Tecnologia', 
    'Saúde', 'Administração e Negócios'
];
?>

<div class="main-header">
    <h1 style="color: #48ab87;">Gerenciar Disciplinas</h1>
    <p>Visualize, edite ou exclua as disciplinas cadastradas.</p>
</div>

<div class="content-panel">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; flex-wrap:wrap; gap:10px;">
        <div class="search-bar" style="flex-grow:1; max-width:400px; margin-bottom:0;">
            <input type="text" id="busca-disciplina" placeholder="Buscar disciplina..." onkeyup="filtrarDisciplinas()">
            <i class="fas fa-search"></i>
        </div>
        <div class="badge-contador" style="background:#e0f2f1; color:#3b8e72; padding:8px 15px; border-radius:20px; font-weight:600;">
            Total: <?= count($disciplinas) ?>
        </div>
    </div>

    <div style="overflow-x:auto;">
        <table id="tabela-disciplinas" style="width:100%; border-collapse:collapse;">
            <thead>
                <tr>
                    <th style="width:50px;">ID</th>
                    <th>Nome da Disciplina</th>
                    <th>Área / Eixo</th>
                    <th style="width:100px; text-align:center;">Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($disciplinas)): ?>
                    <tr><td colspan="4" style="text-align:center; padding:20px;">Nenhuma disciplina encontrada.</td></tr>
                <?php else: ?>
                    <?php foreach ($disciplinas as $d): ?>
                        <tr data-id="<?= $d['id_disc'] ?>">
                            <td><?= htmlspecialchars($d['id_disc']) ?></td>
                            <td class="nome-disc" style="font-weight:500;"><?= htmlspecialchars($d['nome_disc']) ?></td>
                            <td class="area-disc" style="color:#666;"><?= htmlspecialchars($d['area']) ?></td>
                            <td style="text-align:center;">
                                <div class="acoes" style="justify-content:center;">
                                    <button class="btn-acao btn-editar tooltip" data-tooltip="Editar"
                                            onclick="abrirModalEdicaoDisciplina(<?= $d['id_disc'] ?>, '<?= addslashes($d['nome_disc']) ?>', '<?= addslashes($d['area']) ?>')">
                                        <i class="fa-regular fa-pen-to-square"></i>
                                    </button>
                                    <button class="btn-acao btn-excluir tooltip" data-tooltip="Excluir"
                                            onclick="excluirDisciplina(<?= $d['id_disc'] ?>, '<?= addslashes($d['nome_disc']) ?>')">
                                        <i class="fa-regular fa-trash-can"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div id="modal-edicao-disciplina" class="modal-overlay" style="display:none;">
    <div class="modal-content">
        <h2 style="border-bottom:1px solid #eee; padding-bottom:10px; margin-bottom:20px;">Editar Disciplina</h2>
        
        <form id="form-editar-disciplina">
            <input type="hidden" name="acao" value="editar">
            <input type="hidden" name="id_disc" id="edit-id-disc">
            
            <div class="form-group">
                <label>Nome da Disciplina:</label>
                <input type="text" name="nome_disc" id="edit-nome-disc" required class="input-padrao" style="width:100%; padding:10px; border:1px solid #ccc; border-radius:4px;">
            </div>
            
            <div class="form-group" style="margin-top:15px;">
                <label>Área:</label>
                <input type="text" name="area" id="edit-area-disc" list="sugestoes-areas" required class="input-padrao" style="width:100%; padding:10px; border:1px solid #ccc; border-radius:4px;">
                <datalist id="sugestoes-areas">
                    <?php foreach ($areas_sugestao as $area): ?>
                        <option value="<?= $area ?>">
                    <?php endforeach; ?>
                </datalist>
            </div>

            <div class="modal-actions" style="margin-top:25px;">
                <button type="button" class="btn-cancelar" onclick="document.getElementById('modal-edicao-disciplina').style.display='none'">Cancelar</button>
                <button type="submit" class="btn-salvar">Salvar Alterações</button>
            </div>
        </form>
    </div>
</div>