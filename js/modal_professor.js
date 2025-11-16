/**
 * Arquivo: js/modal_professor.js
 * Lógica para o Modal de Edição de Professor (abrir, fechar, multi-select, salvar AJAX)
 */

document.addEventListener('DOMContentLoaded', () => {
    // === Elementos da UI ===
    const modal = document.getElementById('editModal');
    const editForm = document.getElementById('editForm');
    const btnCancelar = modal ? modal.querySelector('.btn-cancelar') : null;
    const multiSelectDisplay = document.getElementById('multi-select-display');
    const multiSelectOptions = document.getElementById('multi-select-options');
    const hiddenAreasInput = document.getElementById('edit-areas');
    const options = multiSelectOptions ? multiSelectOptions.querySelectorAll('.option') : [];

    if (!modal || !editForm || !btnCancelar || !multiSelectDisplay || !multiSelectOptions) {
        // console.warn('Modal de professor ou seus elementos essenciais não encontrados. Ignorando JS do Modal.');
        return;
    }

    // ===========================================
    // === FUNÇÕES DE UTILIDADE E CONTROLE DO MODAL ===
    // ===========================================

    // Funções utilitárias globais já estão em funcoes_dashboard.js
    // Substitui funções locais por chamadas globais

    // Atualizar display do Multi-Select
    function atualizarDisplay() {
        window.updateMultiSelectDisplay(options, multiSelectDisplay, hiddenAreasInput);
    }

    /**
     * Função para fechar o modal e resetar o estado
     */
    function fecharModal() {
        modal.style.display = 'none';
        editForm.reset(); 
        multiSelectOptions.style.display = 'none'; // Garante que a lista de opções feche

        // Limpa o estado do Multi-Select
        options.forEach(option => option.classList.remove('selected'));
        atualizarDisplay();
    }
    
    // ===========================================
    // === EVENT LISTENERS (UI) ===
    // ===========================================

    // Alternar a visibilidade da lista de opções
    multiSelectDisplay.addEventListener('click', (e) => {
        e.stopPropagation();
        multiSelectOptions.style.display = multiSelectOptions.style.display === 'block' ? 'none' : 'block';
    });

    // Seleção de opções
    options.forEach(option => {
        option.addEventListener('click', function(e) {
            e.stopPropagation();
            this.classList.toggle('selected');
            atualizarDisplay();
        });
    });

    // Fechar opções ao clicar fora
    document.addEventListener('click', (e) => {
        if (!e.target.closest('.multi-select-wrapper')) {
            multiSelectOptions.style.display = 'none';
        }
    });

    // Fechar modal
    function fecharModalLocal() {
        window.fecharModal(modal, editForm, multiSelectOptions, options, multiSelectDisplay, hiddenAreasInput);
    }

    btnCancelar.addEventListener('click', fecharModalLocal);
    modal.addEventListener('click', (e) => {
        if (e.target === modal) {
            fecharModalLocal();
        }
    });

    // ===========================================
    // === EVENT LISTENERS (AÇÕES AJAX) ===
    // ===========================================

    // ABRIR Modal e Carregar Dados (AJAX GET para includes/get_professor.php)
    document.querySelectorAll('.btn-editar-modal').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            
            // Reseta antes de abrir
            fecharModal(); // Garante o reset do Multi-Select
            modal.style.display = 'flex'; // Abre o modal

            fetch('includes/get_professor.php?id=' + encodeURIComponent(id))
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Preenche campos simples
                        document.getElementById('edit-id_prof').value = data.professor.id_prof;
                        document.getElementById('edit-nome').value = data.professor.nome;

                        // Trata o Multi-Select
                        const currentAreas = data.professor.areas ? data.professor.areas.split(', ').map(area => area.trim()) : [];
                        
                        currentAreas.forEach(area => {
                            // Encontra e seleciona a opção correspondente
                            const optionElement = Array.from(options).find(opt => (opt.getAttribute('data-value') || opt.textContent.trim()) === area);
                            if (optionElement) {
                                optionElement.classList.add('selected');
                            }
                        });
                        
                        atualizarDisplay(); // Atualiza o texto do display
                        
                    } else {
                        alert('Erro ao carregar dados do professor: ' + (data.error || 'Desconhecido'));
                        fecharModal();
                    }
                })
                .catch(() => {
                    alert('Erro na comunicação com o servidor ao buscar dados.');
                    fecharModal();
                });
        });
    });

    // SALVAR Edição (AJAX POST para includes/update_professor.php)
    editForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Garante que o input hidden tem os valores mais recentes
        atualizarDisplay(); 

        const formData = new FormData(editForm);
        const id = formData.get('id_prof');
        
        // Cria os parâmetros de URL-encoded
        const params = new URLSearchParams(formData);

        fetch('includes/update_professor.php', {
            method: 'POST',
            body: params.toString(),
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                fecharModal();
                
                // ATUALIZAÇÃO DIRETA NA LINHA DA TABELA
                const linha = document.querySelector(`#professoresTable tr[data-id="${id}"]`);
                if (linha) {
                    // Atualiza o nome (coluna 2 - index 1)
                    linha.children[1].textContent = formData.get('nome'); 
                    // Atualiza as áreas (coluna 4 - index 3)
                    linha.children[3].textContent = formData.get('areas'); 
                }
                
            } else {
                alert('Erro ao atualizar: ' + (data.error || 'Erro desconhecido'));
            }
        })
        .catch(() => alert('Erro na requisição de atualização.'));
    });
});