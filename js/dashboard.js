/**
 * Arquivo: js/listar_professores.js
 * Lógica do CRUD (Busca, Modal de Edição, Excluir, Aprovar) para a Listagem de Professores.
 * Atualizado: adicionada função mostrarSecao para controlar visibilidade das seções.
 */

document.addEventListener('DOMContentLoaded', function() {

    // =========================
    // === NOVA FUNÇÃO ADICIONADA ===
    // Controla qual seção do dashboard é exibida
    // =========================
    window.mostrarSecao = function(secaoId) {
        const secoes = ['dashboard-home', 'aprovacoes-section', 'professores-section', 'alunos-section', 'turmas-section','atribuicao-section', 'carga-horaria-section'];
        secoes.forEach(id => {
            const elem = document.getElementById(id);
            if (elem) elem.style.display = (id === secaoId) ? 'block' : 'none';
        });
        window.scrollTo(0, 0); // opcional: rola para o topo
    };

    // =========================
    // === BUSCA EM TEMPO REAL ===
    // =========================
    const searchInput = document.getElementById('searchInputProf'); 
    const professoresTable = document.getElementById('professoresTable');

    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const filtro = this.value.toLowerCase();
            const linhas = professoresTable ? professoresTable.querySelectorAll('tbody tr') : [];
            linhas.forEach(linha => {
                const nomeElement = linha.querySelector('.nome');
                linha.style.display = (nomeElement && nomeElement.textContent.toLowerCase().includes(filtro)) ? '' : 'none';
            });
        });
    }

    // =========================
    // === LÓGICA DO MODAL E MULTI-SELECT ===
    // =========================
    const modal = document.getElementById('editModal');
    if (!modal) return;

    const editForm = document.getElementById('editForm');
    const btnCancelar = modal.querySelector('.btn-cancelar');
    const multiSelectDisplay = document.getElementById('multi-select-display');
    const multiSelectOptions = document.getElementById('multi-select-options');
    const hiddenAreasInput = document.getElementById('edit-areas');
    const options = multiSelectOptions ? multiSelectOptions.querySelectorAll('.option') : [];

    function atualizarDisplay() {
        window.updateMultiSelectDisplay(options, multiSelectDisplay, hiddenAreasInput);
    }

    if (multiSelectDisplay) {
        multiSelectDisplay.addEventListener('click', () => {
            multiSelectOptions.style.display = multiSelectOptions.style.display === 'block' ? 'none' : 'block';
        });
    }

    options.forEach(option => {
        option.addEventListener('click', function() {
            this.classList.toggle('selected');
            atualizarDisplay();
        });
    });

    document.addEventListener('click', (e) => {
        if (!e.target.closest('.multi-select-wrapper')) {
            if (multiSelectOptions) multiSelectOptions.style.display = 'none';
        }
    });

    function fecharModalLocal() {
        window.fecharModal(modal, editForm, multiSelectOptions, options, multiSelectDisplay, hiddenAreasInput);
    }

    document.querySelectorAll('.btn-editar-modal').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            modal.style.display = 'flex'; 

            fetch('includes/get_professor.php?id=' + encodeURIComponent(id))
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('edit-id_prof').value = data.professor.id_prof;
                        document.getElementById('edit-nome').value = data.professor.nome;

                        const currentAreas = data.professor.areas.split(', ').map(area => area.trim());
                        options.forEach(option => option.classList.remove('selected'));
                        currentAreas.forEach(area => {
                            const optionElement = Array.from(options).find(opt => opt.textContent.trim() === area);
                            if (optionElement) optionElement.classList.add('selected');
                        });

                        atualizarDisplay();
                    } else {
                        alert('Erro ao carregar dados do professor.');
                        fecharModalLocal();
                    }
                })
                .catch(() => {
                    alert('Erro na comunicação com o servidor ao buscar dados.');
                    fecharModalLocal();
                });
        });
    });

    btnCancelar.addEventListener('click', fecharModalLocal);
    modal.addEventListener('click', (e) => {
        if (e.target === modal) fecharModalLocal();
    });

    editForm.addEventListener('submit', function(e) {
        e.preventDefault();
        atualizarDisplay();

        const formData = new FormData(editForm);
        const id = formData.get('id_prof');

        fetch('includes/update_professor.php', {
            method: 'POST',
            body: new URLSearchParams(formData).toString(),
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Professor atualizado com sucesso!');
                fecharModalLocal();

                const linha = document.querySelector(`tr[data-id="${id}"]`);
                if (linha) {
                    linha.children[1].textContent = formData.get('nome');
                    linha.children[3].textContent = formData.get('areas');
                }
            } else {
                alert('Erro ao atualizar: ' + (data.error || 'Erro desconhecido'));
            }
        })
        .catch(() => alert('Erro na requisição de atualização.'));
    });

    // =========================
    // === EXCLUSÃO E APROVAÇÃO RÁPIDA ===
    // =========================
    document.querySelectorAll('.btn-excluir-ajax').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const tipo = this.getAttribute('data-tipo') || 'professor';
            const nomeEl = this.closest('tr').querySelector('.nome');
            const nome = nomeEl ? nomeEl.textContent : (tipo + ' ' + id);

            if (confirm(`Tem certeza que deseja EXCLUIR o ${tipo} ${nome} (ID: ${id})?`)) {
                fetch('includes/delete_usuario.php', {
                    method: 'POST',
                    body: new URLSearchParams({ id: id, tipo: tipo }).toString(),
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(data.message || (`${tipo} ${nome} excluído com sucesso.`));
                        const linha = this.closest('tr');
                        if (linha) linha.remove();
                    } else {
                        alert('Erro ao excluir: ' + (data.error || data.message || 'Erro desconhecido.'));
                    }
                })
                .catch((err) => { console.error(err); alert('Erro na comunicação com o servidor.'); });
            }
        });
    });

    document.querySelectorAll('.btn-aprovar-ajax').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const tipo = this.getAttribute('data-tipo');
            const nome = this.closest('tr').querySelector('.nome').textContent;

            if (confirm(`Confirmar a APROVAÇÃO do professor ${nome} (ID: ${id})?`)) {
                fetch('includes/processar_usuario.php', {
                    method: 'POST',
                    body: new URLSearchParams({ id: id, tipo: tipo, acao: 'aprovar' }).toString(),
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(`${tipo} ${nome} aprovado com sucesso!`);
                        const linha = document.querySelector(`tr[data-id="${id}"]`);
                        if (linha) {
                            linha.children[4].innerHTML = '<span class="status-aprovado">APROVADO</span>';
                            this.remove();
                        }
                    } else {
                        alert('Erro ao aprovar: ' + (data.error || 'Erro desconhecido.'));
                    }
                })
                .catch(() => alert('Erro na comunicação com o servidor.'));
            }
        });
    });
    

});
