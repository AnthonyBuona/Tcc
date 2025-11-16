/**
 * Arquivo: js/listagem_alunos.js
 * Descrição: Listagem e edição de alunos com custom select para série e turma.
 */

let todasTurmas = [];

document.addEventListener('DOMContentLoaded', () => {

    // --- FUNÇÃO AUXILIAR: showToast seguro ---
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

    // --- FUNÇÃO AUXILIAR: Preencher Custom Select ---
    function preencherCustomSelect(wrapperId, lista, idKey, nomeKey, valorSelecionado = null) {
        const wrapper = document.getElementById(wrapperId);
        if (!wrapper) return;

        const customSelect = wrapper.querySelector('.custom-select');
        const optionsDiv = wrapper.querySelector('.options');
        const hiddenInput = wrapper.querySelector('input[type="hidden"]');

        optionsDiv.innerHTML = '';
        customSelect.textContent = 'Selecione uma opção';
        hiddenInput.value = '';
        optionsDiv.classList.remove('open');

        if (!Array.isArray(lista) || lista.length === 0) {
            customSelect.textContent = (wrapperId === 'edit-wrapper-turma') ? 'Selecione uma Série primeiro' : 'Nenhuma opção disponível';
            return;
        }

        lista.forEach(item => {
            const option = document.createElement('div');
            option.classList.add('option');
            option.setAttribute('data-value', item[idKey]);
            option.textContent = item[nomeKey];
            optionsDiv.appendChild(option);

            if (valorSelecionado !== null && String(item[idKey]) === String(valorSelecionado)) {
                customSelect.textContent = item[nomeKey];
                hiddenInput.value = item[idKey];
            }
        });

        // Delegação de eventos
        wrapper.removeEventListener('click', wrapper._delegatedClickListener);
        wrapper._delegatedClickListener = function(e) {
            if (e.target.classList.contains('option')) {
                const clickedOption = e.target;
                customSelect.textContent = clickedOption.textContent;
                hiddenInput.value = clickedOption.getAttribute('data-value');
                optionsDiv.classList.remove('open');

                if (wrapperId === 'edit-wrapper-serie') {
                    atualizarTurmasBaseadoNaSerie(hiddenInput.value, null);
                }
            }
        };
        wrapper.addEventListener('click', wrapper._delegatedClickListener);

        // Abrir/fechar
        customSelect.removeEventListener('click', customSelect._openListener);
        customSelect.removeEventListener('blur', customSelect._closeListener);

        customSelect._openListener = (e) => {
            e.stopPropagation();
            optionsDiv.classList.toggle('open');
        };
        customSelect._closeListener = () => optionsDiv.classList.remove('open');

        customSelect.addEventListener('click', customSelect._openListener);
        customSelect.addEventListener('blur', customSelect._closeListener);

        document.addEventListener('click', (e) => {
            if (!wrapper.contains(e.target)) {
                optionsDiv.classList.remove('open');
            }
        });
    }

    // --- Filtrar turmas com base na série ---
    function atualizarTurmasBaseadoNaSerie(idSerieSelecionada, idTurmaAluno = null) {
        if (!idSerieSelecionada) return;

        const turmasFiltradas = todasTurmas
            .filter(turma => turma && turma.id_serie != null && String(turma.id_serie) === String(idSerieSelecionada));

        preencherCustomSelect('edit-wrapper-turma', turmasFiltradas, 'id_turma', 'nome_turma', idTurmaAluno);
    }

    // --- Modal de Edição de Aluno ---
    const modalAluno = document.getElementById('editModalAluno');
    const editFormAluno = document.getElementById('editFormAluno');
    const btnCancelarAluno = modalAluno ? modalAluno.querySelector('.btn-cancelar-aluno') : null;

    function fecharModalAluno() {
        if (modalAluno) {
            modalAluno.style.display = 'none';
            if (editFormAluno) editFormAluno.reset();
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

            document.getElementById('edit-id_aluno').value = aluno.id_aluno;
            document.getElementById('edit-nome-aluno').value = aluno.nome;
            document.getElementById('edit-cpf-aluno').value = aluno.cpf;

            preencherCustomSelect('edit-wrapper-serie', seriesData.lista, 'id_serie', 'nome_serie', aluno.id_serie);
            atualizarTurmasBaseadoNaSerie(aluno.id_serie, aluno.id_turma);

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
            const id = formData.get('id_aluno');

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
                        linha.children[1].textContent = formData.get('nome'); // Nome
                        linha.children[3].textContent = formData.get('id_turma'); // ID Turma
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

});
