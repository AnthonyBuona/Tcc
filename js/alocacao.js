document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('modalAlocacao');
    const closeButton = modal.querySelector('.close-button');
    const gradeCells = document.querySelectorAll('.celula-alocacao');
    const modalInfoAula = document.getElementById('modal-info-aula');
    const formAlocacao = document.getElementById('form-alocacao');
    const btnDesalocar = document.getElementById('btn-desalocar');

    const modalDia = document.getElementById('modal_dia');
    const modalHorario = document.getElementById('modal_horario');
    const modalIdTurma = document.getElementById('modal_id_turma');
    const modalIdDisciplina = document.getElementById('modal_id_disciplina');
    const modalIdProf = document.getElementById('modal_id_prof');

    // Função para abrir o modal
    function abrirModal(cell) {
        // Obter dados da célula
        const modo = cell.dataset.modo;
        const dia = cell.dataset.dia;
        const horario = cell.dataset.horarioStr;
        const idTurma = cell.dataset.idTurma;
        const idProf = cell.dataset.idProf;
        const idDiscAlocado = cell.dataset.idDiscAlocado || '0';
        const idProfAlocado = cell.dataset.idProfAlocado || '0';

        // Só permite abrir se estiver no modo Turma com Turma selecionada
        if (modo === 'turma' && idTurma && idTurma > 0) {
            
            // 1. Preenche as informações no topo do modal
            modalInfoAula.textContent = `Turma: ${idTurma ? 'ID ' + idTurma : 'N/A'} | Dia: ${dia} | Horário: ${horario}`;
            
            // 2. Preenche os campos hidden do formulário (para o PHP processar)
            modalDia.value = dia;
            modalHorario.value = horario;
            modalIdTurma.value = idTurma; // Chave principal de alocação

            // 3. Define os valores iniciais dos selects
            modalIdDisciplina.value = idDiscAlocado;
            modalIdProf.value = idProfAlocado;
            
            // 4. Configura o botão de Desalocar
            if (idDiscAlocado > 0) {
                // A aula já está alocada, exibe o botão Desalocar
                btnDesalocar.style.display = 'inline-block';
            } else {
                // Aula vazia, esconde ou desabilita Desalocar
                btnDesalocar.style.display = 'none';
            }

            // 5. Exibe o modal
            modal.style.display = 'block';

        } else {
            // Caso tente clicar em Modo Professor/Geral ou Modo Turma sem Turma selecionada
            alert('Selecione uma Turma para poder alocar horários.');
        }
    }

    // Função global para fechar o modal
    window.fecharModal = function() {
        modal.style.display = 'none';
    }

    // Event listener para fechar o modal
    closeButton.onclick = fecharModal;
    window.onclick = function(event) {
        if (event.target == modal) {
            fecharModal();
        }
    }

    // Event listener para as células da grade
    gradeCells.forEach(cell => {
        cell.addEventListener('click', function() {
            abrirModal(this);
        });
    });

    // Lógica do botão Desalocar
    btnDesalocar.addEventListener('click', function(e) {
        e.preventDefault();
        if (confirm('Tem certeza que deseja desalocar esta aula?')) {
            // Força a seleção para 0 (Desalocar)
            modalIdDisciplina.value = '0';
            modalIdProf.value = '0';
            
            // Submete o formulário com os valores 0/0 (o PHP tratará como DELETE/Desalocação)
            formAlocacao.submit();
        }
    });

    // Lógica para mudar o modo de visualização
    document.getElementById('select-modo').addEventListener('change', function() {
        // Atualiza o hidden field e submete o form de filtro
        document.querySelector('input[name="modo"]').value = this.value;
        this.closest('form').submit();
    });
});