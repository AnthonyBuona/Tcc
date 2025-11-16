/**
 * Arquivo: dashboard.js
 * Descrição: Contém a lógica de front-end para navegação entre seções,
 * gerenciamento do modal de edição de professor e filtros de tabela.
 */

// =======================================================
// SEÇÃO 1: NAVEGAÇÃO E CONTROLE DE SEÇÕES
// =======================================================

/**
 * Função global para alternar a exibição das seções principais do dashboard.
 * É chamada pelos links da navbar lateral (sidebar).
 * @param {string} secaoId - O ID da seção a ser exibida (ex: 'aprovacoes-section').
 */
function mostrarSecao(secaoId) {
    // Esconde todas as seções principais
    const secoes = [
        'dashboard-home',
        'aprovacoes-section',
        'professores-section'
    ];
    secoes.forEach(id => {
        const secao = document.getElementById(id);
        if (secao) {
            secao.style.display = 'none';
        }
    });

    // Exibe a seção solicitada
    const secao = document.getElementById(secaoId);
    if (secao) {
        secao.style.display = 'block';
    }
}

// Inicializa a seção correta ao carregar a página, verificando o hash (#) na URL.
document.addEventListener('DOMContentLoaded', () => {
    // Garante que a função 'mostrarSecao' esteja disponível globalmente
    window.mostrarSecao = mostrarSecao; 
    
    const currentHash = window.location.hash.substring(1);
    
    if (currentHash === 'aprovacoes') {
        mostrarSecao('aprovacoes-section');
    } else if (currentHash === 'listar-professores') {
        mostrarSecao('professores-section');
    } else {
        mostrarSecao('dashboard-home'); // Padrão
    }

    // Adiciona o listener para o filtro de busca ao carregar a página
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('input', filtroTabelaProfessores);
    }
});

// =======================================================
// SEÇÃO 2: LÓGICA DO MODAL DE EDIÇÃO E MULTI-SELECT DE ÁREAS
// =======================================================

// Elementos principais do Modal
const modal = document.getElementById('editModal');
const editForm = document.getElementById('editForm');
const btnCancelar = modal ? modal.querySelector('.btn-cancelar') : null;

// Elementos do Multi-Select
const multiSelectDisplay = document.getElementById('multi-select-display');
const multiSelectOptions = document.getElementById('multi-select-options');
const hiddenAreasInput = document.getElementById('edit-areas');
const options = multiSelectOptions ? multiSelectOptions.querySelectorAll('.option') : [];

// Funções utilitárias globais para modal e multi-select
window.updateMultiSelectDisplay = function(options, multiSelectDisplay, hiddenAreasInput) {
    if (!options || typeof options.forEach !== 'function') return;
    const selectedValues = [];
    options.forEach(option => {
        if (option.classList.contains('selected')) {
            selectedValues.push(option.textContent.trim());
        }
    });
    if (selectedValues.length === 0) {
        if (multiSelectDisplay) multiSelectDisplay.textContent = 'Área(s) que você ensina...';
        if (hiddenAreasInput) hiddenAreasInput.value = '';
    } else {
        if (multiSelectDisplay) multiSelectDisplay.textContent = selectedValues.join(', ');
        if (hiddenAreasInput) hiddenAreasInput.value = selectedValues.join(', ');
    }
};

window.fecharModal = function(modal, editForm, multiSelectOptions, options, multiSelectDisplay, hiddenAreasInput) {
    if (modal) modal.style.display = 'none';
    if (editForm) editForm.reset();
    if (multiSelectOptions) multiSelectOptions.style.display = 'none';
    if (options && typeof options.forEach === 'function') {
        options.forEach(option => option.classList.remove('selected'));
        window.updateMultiSelectDisplay(options, multiSelectDisplay, hiddenAreasInput);
    }
}

/**
 * Fecha o modal de edição e reseta seu estado.
 */
function fecharModal() {
    if (modal) modal.style.display = 'none';
    if (editForm) editForm.reset();
    if (multiSelectOptions) multiSelectOptions.style.display = 'none';

    options.forEach(option => option.classList.remove('selected'));
    updateMultiSelectDisplay();
}

// 1. Alternar a visibilidade da lista de opções do Multi-Select
if (multiSelectDisplay) {
    multiSelectDisplay.addEventListener('click', () => {
        multiSelectOptions.style.display = multiSelectOptions.style.display === 'block' ? 'none' : 'block';
    });
}

// 2. Lógica de Seleção de Opções do Multi-Select
options.forEach(option => {
    option.addEventListener('click', function() {
        this.classList.toggle('selected');
        updateMultiSelectDisplay();
    });
});

// 3. Fechar opções ao clicar fora do Multi-Select
document.addEventListener('click', (e) => {
    if (multiSelectOptions && !e.target.closest('.multi-select-wrapper')) {
        multiSelectOptions.style.display = 'none';
    }
});

// 4. ABRIR Modal e Carregar Dados (AJAX GET)
document.querySelectorAll('.btn-editar-modal').forEach(btn => {
    btn.addEventListener('click', function() {
        const id = this.getAttribute('data-id');

        options.forEach(option => option.classList.remove('selected'));
        updateMultiSelectDisplay(); // Reseta o display antes de carregar

        if (modal) modal.style.display = 'flex';

        fetch('includes/get_professor.php?id=' + encodeURIComponent(id))
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Preenche campos
                    document.getElementById('edit-id_prof').value = data.professor.id_prof;
                    document.getElementById('edit-nome').value = data.professor.nome;

                    // Trata e pré-seleciona as áreas no Multi-Select
                    const currentAreas = data.professor.areas ? data.professor.areas.split(', ').map(area => area.trim()) : [];

                    currentAreas.forEach(area => {
                        const optionElement = Array.from(options).find(opt => opt.textContent.trim() === area);
                        if (optionElement) {
                            optionElement.classList.add('selected');
                        }
                    });

                    updateMultiSelectDisplay(); // Atualiza o texto do display

                } else {
                    alert('Erro ao carregar dados do professor.');
                    fecharModal();
                }
            })
            .catch(() => {
                alert('Erro na comunicação com o servidor ao buscar dados.');
                fecharModal();
            });
    });
});

// 5. Fechar Modal (botão Cancelar e clique fora)
if (btnCancelar) btnCancelar.addEventListener('click', fecharModal);
if (modal) {
    modal.addEventListener('click', (e) => {
        if (e.target === modal) {
            fecharModal();
        }
    });
}

// 6. SALVAR Edição (AJAX POST)
if (editForm) {
    editForm.addEventListener('submit', function(e) {
        e.preventDefault();

        updateMultiSelectDisplay(); // Garante o valor correto das áreas

        const formData = new FormData(editForm);
        const id = formData.get('id_prof');

        fetch('includes/update_professor.php', {
            method: 'POST',
            body: new URLSearchParams(formData).toString(),
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Professor atualizado com sucesso!');
                fecharModal();

                // ATUALIZAÇÃO DIRETA DA LINHA DA TABELA
                const linha = document.querySelector(`#professoresTable tr[data-id="${id}"]`);
                if (linha) {
                    linha.children[1].textContent = formData.get('nome');
                    // A coluna 3 (índice 3) é a de Áreas
                    linha.children[3].textContent = formData.get('areas'); 
                }

            } else {
                alert('Erro ao atualizar: ' + (data.error || 'Erro desconhecido'));
            }
        })
        .catch(() => alert('Erro na requisição de atualização.'));
    });
}

// =======================================================
// SEÇÃO 3: FUNÇÕES DE UTILIDADE E FILTROS
// =======================================================

/**
 * Função para buscar em tempo real na tabela de professores.
 */
function filtroTabelaProfessores() {
    const searchInput = document.getElementById('searchInput');
    if (!searchInput) return; 
    
    const filtro = searchInput.value.toLowerCase();
    const linhas = document.querySelectorAll('#professoresTable tbody tr');

    linhas.forEach(linha => {
        // Assume que a coluna 2 (índice 1) é a do nome, com a classe 'nome'
        const nomeElement = linha.querySelector('.nome');
        if (nomeElement) {
             const nome = nomeElement.textContent.toLowerCase();
             linha.style.display = nome.includes(filtro) ? '' : 'none';
        }
    });
}