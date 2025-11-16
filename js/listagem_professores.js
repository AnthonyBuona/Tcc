// Arquivo: js/listagem_professores.js

document.addEventListener('DOMContentLoaded', function() {

    // ===========================================
    // === BUSCA EM TEMPO REAL ===
    // ===========================================
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const filtro = this.value.toLowerCase();
            const linhas = document.querySelectorAll('#professoresTable tbody tr');

            linhas.forEach(linha => {
                // Certifique-se de que a coluna nome tem a classe 'nome'
                const nomeCell = linha.querySelector('.nome'); 
                if (nomeCell) {
                    const nome = nomeCell.textContent.toLowerCase();
                    linha.style.display = nome.includes(filtro) ? '' : 'none';
                }
            });
        });
    }

    // ===========================================
    // === LÓGICA DO MODAL E MULTI-SELECT ===
    // ===========================================
    
    const modal = document.getElementById('editModal');
    const editForm = document.getElementById('editForm');
    const btnCancelar = modal ? modal.querySelector('.btn-cancelar') : null;
    const multiSelectDisplay = document.getElementById('multi-select-display');
    const multiSelectOptions = document.getElementById('multi-select-options');
    const hiddenAreasInput = document.getElementById('edit-areas');
    const options = multiSelectOptions ? multiSelectOptions.querySelectorAll('.option') : [];


    // Funções utilitárias globais já estão em funcoes_dashboard.js
    // Substitui funções locais por chamadas globais

    function atualizarDisplay() {
        window.updateMultiSelectDisplay(options, multiSelectDisplay, hiddenAreasInput);
    }

    // 1. Alternar a visibilidade da lista de opções
    if (multiSelectDisplay) {
        multiSelectDisplay.addEventListener('click', () => {
            multiSelectOptions.style.display = multiSelectOptions.style.display === 'block' ? 'none' : 'block';
        });
    }

    // 2. Lógica de Seleção de Opções
    options.forEach(option => {
        option.addEventListener('click', function() {
            this.classList.toggle('selected');
            atualizarDisplay();
        });
    });

    // 3. Fechar opções ao clicar fora
    document.addEventListener('click', (e) => {
        if (!e.target.closest('.multi-select-wrapper')) {
            if (multiSelectOptions) {
                multiSelectOptions.style.display = 'none';
            }
        }
    });

    // Função para fechar o modal
    function fecharModalLocal() {
        window.fecharModal(modal, editForm, multiSelectOptions, options, multiSelectDisplay, hiddenAreasInput);
    }
    
    // Fechar Modal (botão Cancelar e clique fora)
    if (btnCancelar) {
        btnCancelar.addEventListener('click', fecharModalLocal);
    }
    if (modal) {
        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                fecharModalLocal();
            }
        });
    }
    
    // ABRIR Modal e Carregar Dados (AJAX GET para get_professor.php)
    document.querySelectorAll('.btn-editar-modal').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            if (!id) {
                showToast('ID do professor não encontrado. Verifique o botão de edição.', true);
                return;
            }
            console.log('Abrindo edição de professor, id:', id); // LOG
            options.forEach(option => option.classList.remove('selected'));
            atualizarDisplay();
            if (modal) {
                modal.style.display = 'flex'; 
            }
            fetch('includes/get_professor.php?id=' + encodeURIComponent(id))
                .then(response => {
                    console.log('Status HTTP:', response.status); // LOG
                    if (!response.ok) {
                        response.text().then(txt => {
                            showToast('Erro HTTP ' + response.status + ': ' + txt, true);
                        });
                        fecharModal();
                        throw new Error('HTTP ' + response.status);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Resposta JSON:', data); // LOG
                    if (data.success) {
                        document.getElementById('edit-id_prof').value = data.professor.id_prof;
                        document.getElementById('edit-nome').value = data.professor.nome;
                        const currentAreas = data.professor.areas ? data.professor.areas.split(', ').map(area => area.trim()) : [];
                        currentAreas.forEach(area => {
                            const optionElement = Array.from(options).find(opt => opt.textContent.trim() === area);
                            if (optionElement) {
                                optionElement.classList.add('selected');
                            }
                        });
                        atualizarDisplay(); 
                    } else {
                        showToast('Erro ao carregar dados do professor: ' + (data.error || 'Erro desconhecido'), true);
                        fecharModal();
                    }
                })
                .catch(err => {
                    showToast('Erro na comunicação com o servidor: ' + err, true);
                    fecharModal();
                });
        });
    });

    // SALVAR Edição (AJAX POST para update_professor.php)
    if (editForm) {
        editForm.addEventListener('submit', function(e) {
            e.preventDefault();
            // Garante que o valor do input hidden 'areas' está atualizado
            atualizarDisplay(); 
            // Validação extra: impede envio se não houver áreas selecionadas
            if (!hiddenAreasInput.value || hiddenAreasInput.value.trim() === '') {
                showToast('Selecione ao menos uma área para o professor.', true);
                return;
            }
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
                    showToast('Professor atualizado com sucesso!', false);
                    fecharModal();
                    const linha = document.querySelector(`#professoresTable tr[data-id="${id}"]`);
                    if (linha) {
                        linha.children[1].textContent = formData.get('nome'); // Nome
                        linha.children[3].textContent = formData.get('areas'); // Áreas
                    }
                } else {
                    showToast('Erro ao atualizar: ' + (data.error || 'Erro desconhecido'), true);
                }
            })
            .catch(() => showToast('Erro na requisição de atualização.', true));
        });
    }

    // OBS: A LÓGICA DE EXCLUSÃO E APROVAÇÃO VIA AJAX (btn-excluir-ajax e btn-aprovar-ajax)
    // DEVE SER INCLUÍDA AQUI OU NO SEU ARQUIVO 'dashboard_pendente.js', dependendo
    // de onde você prefere gerenciar essas ações.

});