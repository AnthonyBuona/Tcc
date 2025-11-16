/**
 * Arquivo: dashboard_filtros.js
 * Descrição: Contém funções JavaScript para navegação de abas e filtros das tabelas.
 */

// 1. Função de Navegação de Seções
function mostrarSecao(id){
    // Esconde todas as divs diretamente dentro de <main>
    document.querySelectorAll('main > div').forEach(d => d.style.display = 'none');
    // Mostra a div com o ID correspondente
    const secao = document.getElementById(id);
    if (secao) {
        secao.style.display = 'block';
    }
}

// 2. Função de Processamento de Usuário (Simulação de Aprovação/Reprovação)
function processarUsuario(id, tipo, acao){
    if(!confirm(`Confirma ${acao} do ${tipo} ${id}?`)) return;
    
    // Simulação: remover a linha da tabela após a confirmação
    const linha = document.getElementById(`linha-${tipo}-${id}`);
    if (linha) {
        linha.remove();
        alert(`Ação '${acao}' para ${tipo} ${id} processada. (Você precisa implementar a chamada AJAX real no backend)`);
        // Lembrete: Você deve implementar aqui a chamada AJAX real para um arquivo PHP (ex: processar_aprovacao.php)
    } else {
        alert(`Ação '${acao}' para ${tipo} ${id} processada. (Linha não encontrada)`);
    }
}

// 3. --- Filtros Professores (Atualizado para funcionar com a pesquisa e filtro) ---
function filtrarProfessores(){
    const areaFiltro = document.getElementById('filtroArea').value.toLowerCase();
    const nomeFiltro = document.getElementById('searchInputProf').value.toLowerCase();
    
    document.querySelectorAll('#professoresTable tbody tr').forEach(tr => {
        const nomeProf = tr.querySelector('.nome').textContent.toLowerCase();
        const areaData = tr.getAttribute('data-area').toLowerCase();
        
        const areaCorresponde = !areaFiltro || areaData.includes(areaFiltro);
        const nomeCorresponde = !nomeFiltro || nomeProf.includes(nomeFiltro);

        tr.style.display = (areaCorresponde && nomeCorresponde) ? '' : 'none';
    });
}

function limparFiltroProfessores(){ 
    document.getElementById('filtroArea').value = ''; 
    document.getElementById('searchInputProf').value = ''; // Limpa a busca por nome
    filtrarProfessores(); 
}

// 4. --- Filtros Alunos (Atualizado para funcionar com a pesquisa e filtros) ---
function filtrarAlunos(){
    const ano = document.getElementById('filtroAno').value;
    const curso = document.getElementById('filtroCurso').value;
    const nomeFiltro = document.getElementById('searchInputAluno').value.toLowerCase();
    
    document.querySelectorAll('#alunosTable tbody tr').forEach(tr => {
        const nomeAluno = tr.querySelector('.nome').textContent.toLowerCase();

        const tAno = tr.getAttribute('data-ano');
        const tCurso = tr.getAttribute('data-curso');
        
        const anoCorresponde = !ano || tAno == ano;
        const cursoCorresponde = !curso || tCurso == curso;
        const nomeCorresponde = !nomeFiltro || nomeAluno.includes(nomeFiltro);

        tr.style.display = (anoCorresponde && cursoCorresponde && nomeCorresponde) ? '' : 'none';
    });
}

function limparFiltroAlunos(){ 
    document.getElementById('filtroAno').value = ''; 
    document.getElementById('filtroCurso').value = ''; 
    document.getElementById('searchInputAluno').value = ''; // Limpa a busca por nome
    filtrarAlunos(); 
}

// 5. --- Filtros Turmas ---
function filtrarTurmas(){
    const ano = document.getElementById('filtroAnoTurma').value;
    const curso = document.getElementById('filtroCursoTurma').value;
    
    document.querySelectorAll('#turmasTable tbody tr').forEach(tr => {
        const tAno = tr.getAttribute('data-ano');
        const tCurso = tr.getAttribute('data-curso');
        
        tr.style.display = (!ano || tAno == ano) && (!curso || tCurso == curso) ? '' : 'none';
    });
}

function limparFiltroTurmas(){ 
    document.getElementById('filtroAnoTurma').value = ''; 
    document.getElementById('filtroCursoTurma').value = ''; 
    filtrarTurmas(); 
}

// NOTE: A função filterTable (que você mencionou no onkeyup) foi substituída pelas funções
// filtrarProfessores e filtrarAlunos acima para integrar os filtros de área/curso e busca por nome.

// Inicializa a exibição da seção principal ao carregar
window.onload = function() {
    mostrarSecao('dashboard-home');
};