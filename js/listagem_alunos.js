/**
 * Arquivo: js/listagem_alunos.js
 * Descrição: Listagem e edição de alunos com custom select para série e turma.
 * Estratégia simplificada: sincronizar estado via objetos JS simples, sem dependências de cloning/listeners globais
 */

let todasTurmas = [];
let alunoEditState = { id_serie: null, id_turma: null };

document.addEventListener('DOMContentLoaded', () => {
    console.debug('listagem_alunos.js: DOMContentLoaded fired');

    // --- Função auxiliar: toast seguro ---
    function showToast(message, isError = false) {
        let toast = document.getElementById('feedback-toast');
        if (!toast) {
            toast = document.createElement('div');
            toast.id = 'feedback-toast';
            document.body.appendChild(toast);
        }
        toast.textContent = message;
        toast.style.display = 'block';
        toast.style.background = isError ? '#e74c3c' : '#27ae60';
        toast.style.color = '#fff';
        toast.style.padding = '12px 24px';
        toast.style.position = 'fixed';
        toast.style.bottom = '32px';
        toast.style.right = '32px';
        toast.style.borderRadius = '8px';
        toast.style.zIndex = '9999';
        setTimeout(() => { toast.style.display = 'none'; }, 3000);
    }

    // --- Função: sincronizar estado para hidden inputs e badge ---
    function syncState() {
        const serieInput = document.querySelector('#edit-wrapper-serie input[type="hidden"]');
        const turmaInput = document.querySelector('#edit-wrapper-turma input[type="hidden"]');
        const badge = document.getElementById('edit-aluno-debug');

        if (serieInput) serieInput.value = alunoEditState.id_serie || '';
        if (turmaInput) turmaInput.value = alunoEditState.id_turma || '';
        if (badge) badge.textContent = `debug: id_serie=${alunoEditState.id_serie || '(vazio)'} | id_turma=${alunoEditState.id_turma || '(vazio)'}`;

        console.debug('syncState updated:', alunoEditState);
    }

    // --- Função: preencher opções do select e wiring de cliques ---
    function populateSelectOptions(wrapperId, lista, idKey, nomeKey, valorSelecionado = null) {
        const wrapper = document.getElementById(wrapperId);
        if (!wrapper) return;

        const optionsDiv = wrapper.querySelector('.options');
        const customSelect = wrapper.querySelector('.custom-select');
        if (!optionsDiv || !customSelect) return;

        // Limpar opções anteriores
        optionsDiv.innerHTML = '';
        customSelect.textContent = 'Selecione uma opção';

        if (!Array.isArray(lista) || lista.length === 0) {
            customSelect.textContent = wrapperId === 'edit-wrapper-turma' ? 'Selecione uma Série primeiro' : 'Nenhuma opção disponível';
            return;
        }

        // Recriar opções
        lista.forEach(item => {
            const option = document.createElement('div');
            option.classList.add('option');
            option.setAttribute('data-value', item[idKey]);
            option.setAttribute('data-label', item[nomeKey]);
            option.textContent = item[nomeKey];
            optionsDiv.appendChild(option);
        });

        // Sincronizar opções com cliques diretos, sem delegação
        const options = optionsDiv.querySelectorAll('.option');
        options.forEach(opt => {
            opt.addEventListener('click', function(e) {
                e.stopPropagation();
                const val = this.getAttribute('data-value');
                const label = this.getAttribute('data-label');

                if (wrapperId === 'edit-wrapper-serie') {
                    alunoEditState.id_serie = val;
                    alunoEditState.id_turma = null; // reset turma when série changes
                    customSelect.textContent = label;
                    optionsDiv.style.display = 'none';
                    syncState();
                    updateTurmaOptions(null);
                } else if (wrapperId === 'edit-wrapper-turma') {
                    alunoEditState.id_turma = val;
                    customSelect.textContent = label;
                    optionsDiv.style.display = 'none';
                    syncState();
                }
            });
        });

        // Abrir/fechar dropdown ao clicar no label
        customSelect.removeEventListener('click', customSelect._clickListener || (() => {}));
        customSelect._clickListener = function(e) {
            e.stopPropagation();
            optionsDiv.style.display = optionsDiv.style.display === 'block' ? 'none' : 'block';
        };
        customSelect.addEventListener('click', customSelect._clickListener);

        // Fechar dropdown ao clicar fora
        document.removeEventListener('click', window._alunoModalOutsideClickListener || (() => {}));
        window._alunoModalOutsideClickListener = function(e) {
            if (!wrapper.contains(e.target)) optionsDiv.style.display = 'none';
        };
        document.addEventListener('click', window._alunoModalOutsideClickListener);

        // Se houver valorSelecionado, mostrar no label e syncState
        if (valorSelecionado !== null) {
            const found = lista.find(item => String(item[idKey]) === String(valorSelecionado));
            if (found) {
                customSelect.textContent = found[nomeKey];
                if (wrapperId === 'edit-wrapper-serie') {
                    alunoEditState.id_serie = String(valorSelecionado);
                } else if (wrapperId === 'edit-wrapper-turma') {
                    alunoEditState.id_turma = String(valorSelecionado);
                }
            }
        }
    }

    function updateTurmaOptions(idTurmaToSelect = null) {
        if (!alunoEditState.id_serie) return;

        const turmasFiltradas = todasTurmas.filter(
            t => t && t.id_serie != null && String(t.id_serie) === String(alunoEditState.id_serie)
        );
        populateSelectOptions('edit-wrapper-turma', turmasFiltradas, 'id_turma', 'nome_turma', idTurmaToSelect);
    }

    // --- Modal de Edição de Aluno ---
    const modalAluno = document.getElementById('editModalAluno');
    const editFormAluno = document.getElementById('editFormAluno');
    const btnCancelarAluno = modalAluno ? modalAluno.querySelector('.btn-cancelar-aluno') : null;

    function fecharModalAluno() {
        if (modalAluno) {
            modalAluno.style.display = 'none';
            if (editFormAluno) editFormAluno.reset();
            alunoEditState = { id_serie: null, id_turma: null };
        }
    }

    async function carregarDadosEdicaoAluno(id) {
        try {
            const [alunoResp, seriesResp, turmasResp] = await Promise.all([
                fetch('includes/get_aluno.php?id=' + encodeURIComponent(id)),
                fetch('includes/get_series.php'),
                fetch('includes/get_turmas.php')
            ]);

            const [alunoData, seriesData, turmasData] = await Promise.all([
                alunoResp.json(), seriesResp.json(), turmasResp.json()
            ]);

            if (!alunoData.success) throw new Error(alunoData.error || 'Erro ao buscar aluno');
            if (!seriesData.success) throw new Error(seriesData.error || 'Erro ao buscar séries');
            if (!turmasData.success) throw new Error(turmasData.error || 'Erro ao buscar turmas');

            const aluno = alunoData.aluno;
            todasTurmas = turmasData.lista;

            // Reset state
            alunoEditState = { id_serie: aluno.id_serie, id_turma: aluno.id_turma };

            // Preencher form
            document.getElementById('edit-id_aluno').value = aluno.id_aluno;
            document.getElementById('edit-nome-aluno').value = aluno.nome;
            document.getElementById('edit-cpf-aluno').value = aluno.cpf;

            // Preencher séries e turmas
            populateSelectOptions('edit-wrapper-serie', seriesData.lista, 'id_serie', 'nome_serie', aluno.id_serie);
            updateTurmaOptions(aluno.id_turma);
            syncState();

            // Debug badge
            try {
                if (!document.getElementById('edit-aluno-debug')) {
                    const badge = document.createElement('div');
                    badge.id = 'edit-aluno-debug';
                    badge.style.cssText = 'position:relative;margin-top:10px;padding:8px;background:#f1f1f1;border-radius:6px;font-size:13px;color:#333;';
                    document.querySelector('#editModalAluno .modal-content').appendChild(badge);
                }
                syncState();
            } catch (e) {
                console.debug('badge init failed', e);
            }

        } catch (err) {
            console.error(err);
            showToast('Erro ao carregar dados do aluno. ' + err.message, true);
            fecharModalAluno();
        }
    }

    document.querySelectorAll('.btn-editar-modal-aluno').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            if (!modalAluno) return;
            modalAluno.style.display = 'flex';
            carregarDadosEdicaoAluno(id);
        });
    });

    if (btnCancelarAluno) btnCancelarAluno.addEventListener('click', fecharModalAluno);
    if (modalAluno) modalAluno.addEventListener('click', e => { if (e.target === modalAluno) fecharModalAluno(); });

    // --- Salvar edição via AJAX ---
    if (editFormAluno) {
        editFormAluno.addEventListener('submit', async function(e) {
            e.preventDefault();

            const formData = new FormData(editFormAluno);
            // Garantir que os valores do estado JS estão no formData
            formData.set('id_serie', alunoEditState.id_serie || '');
            formData.set('id_turma', alunoEditState.id_turma || '');

            const id = formData.get('id_aluno');
            
            // 💡 CORREÇÃO: Captura o nome da turma que está visível no dropdown do modal
            const nomeTurmaSelecionadaElement = document.querySelector('#edit-wrapper-turma .custom-select');
            const nomeTurmaSelecionada = nomeTurmaSelecionadaElement ? nomeTurmaSelecionadaElement.textContent : '';

            console.debug('Submitting form with:', {
                id_aluno: formData.get('id_aluno'),
                id_serie: formData.get('id_serie'),
                id_turma: formData.get('id_turma'),
                nome: formData.get('nome'),
                nome_turma_display: nomeTurmaSelecionada 
            });

            try {
                const resp = await fetch('includes/update_aluno.php', {
                    method: 'POST',
                    body: new URLSearchParams(formData).toString(),
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
                });

                const data = await resp.json();

                if (data.success) {
                    showToast(data.message || 'Aluno atualizado com sucesso!', false);
                    fecharModalAluno();

                    const linha = document.querySelector(`tr[data-id="${id}"]`);
                    if (linha) {
                        linha.children[1].textContent = formData.get('nome'); // Coluna 2: Nome
                        
                        // Coluna 4 (índice 3): Atualiza para exibir ID + Nome da Turma
                        linha.children[3].textContent = `${formData.get('id_turma')} (${nomeTurmaSelecionada})`; 
                    }

                } else {
                    showToast('Erro ao atualizar: ' + (data.error || 'Erro desconhecido'), true);
                }

            } catch (err) {
                console.error(err);
                showToast('Erro na requisição de atualização do aluno.', true);
            }
        });
    }

    // Expose for debugging
    window.alunoEditState = alunoEditState;
    window.syncState = syncState;
    console.debug('listagem_alunos.js: initialized');

});