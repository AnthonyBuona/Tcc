// --- FUNÇÕES DE CUSTOM SELECT ---
function initializeCustomSelect(wrapper) {
    const selectDisplay = wrapper.querySelector('.custom-select');
    const optionsList = wrapper.querySelector('.options');
    const hiddenInput = wrapper.querySelector('input[type="hidden"]');
    const options = wrapper.querySelectorAll('.option');

    if (!selectDisplay || !optionsList || !hiddenInput) return;

    selectDisplay.addEventListener('click', function(e) {
        e.stopPropagation(); 
        
        // Fecha outros selects abertos
        document.querySelectorAll('.options').forEach(otherOptions => {
            if (otherOptions !== optionsList) {
                otherOptions.style.display = 'none';
            }
        });
        // Alterna o display da lista atual
        optionsList.style.display = optionsList.style.display === 'block' ? 'none' : 'block';
    });

    options.forEach(option => {
        option.addEventListener('click', function() {
            const value = this.getAttribute('data-value');
            const text = this.textContent;

            selectDisplay.textContent = text;
            hiddenInput.value = value;
            optionsList.style.display = 'none';
            hiddenInput.required = true; 
            
            // Atualiza a classe 'selected' visualmente
            options.forEach(opt => opt.classList.remove('selected'));
            this.classList.add('selected');
        });
    });

    // Fecha o custom select se clicar fora dele
    document.addEventListener('click', function(e) {
        if (!wrapper.contains(e.target)) {
            optionsList.style.display = 'none';
        }
    });
}

// --- FUNÇÕES DE MODAL E INICIALIZAÇÃO ---
document.addEventListener('DOMContentLoaded', function() {
    // 1. Inicializa o Custom Select do formulário de Cadastro
    const cadastroWrapper = document.getElementById('cadastro-area-wrapper');
    if (cadastroWrapper) {
        initializeCustomSelect(cadastroWrapper);
    }
    
    // 2. Inicializa o Custom Select do Modal de Edição
    const edicaoWrapper = document.getElementById('edicao-area-wrapper');
    if (edicaoWrapper) {
        initializeCustomSelect(edicaoWrapper);
    }

    const modalEditar = document.getElementById('modal-editar-disciplina');

    // Funcionalidade do Modal de Edição
    document.querySelectorAll('.btn-editar').forEach(button => {
        button.addEventListener('click', function() {
            const id = this.dataset.id;
            const nome = this.dataset.nome;
            const area = this.dataset.area;

            document.getElementById('id_disc_editar').value = id;
            document.getElementById('nome_disciplina_edit').value = nome;
            
            // LÓGICA DE PRÉ-SELEÇÃO DO CUSTOM SELECT DO MODAL
            if (edicaoWrapper) {
                const selectDisplayEdit = document.getElementById('area_disciplina_display_edit');
                const hiddenInputEdit = document.getElementById('area_disciplina_edit');
                
                hiddenInputEdit.value = area;
                
                let displayArea = "Selecione o Eixo/Área";
                edicaoWrapper.querySelectorAll('.option').forEach(option => {
                    // Limpa e define a seleção visual
                    option.classList.remove('selected'); 
                    
                    if (option.getAttribute('data-value') === area) {
                        displayArea = option.textContent;
                        option.classList.add('selected'); 
                    }
                });

                selectDisplayEdit.textContent = displayArea;
            }
            
            modalEditar.style.display = 'block';
        });
    });

    const fecharModalEditar = document.getElementById('fechar-modal-editar');
    const modalExcluir = document.getElementById('modal-excluir-disciplina');
    const confirmarExclusao = document.getElementById('confirmar-exclusao');

    fecharModalEditar.onclick = function() {
        modalEditar.style.display = 'none';
    }
    
    // Funcionalidade do Modal de Exclusão - CORRIGIDA (Usa :not() para ignorar o botão de confirmação)
    let idParaExcluir = null;
    document.querySelectorAll('.btn-excluir:not(#confirmar-exclusao)').forEach(button => {
        button.addEventListener('click', function(event) {
            // Usa .closest() para garantir que o elemento pai <button> seja capturado
            const targetButton = event.target.closest('.btn-excluir');
            
            if (targetButton) {
                idParaExcluir = targetButton.dataset.id;
                const nome = targetButton.dataset.nome;
                
                if (idParaExcluir && nome) {
                    document.getElementById('disciplina-nome-excluir').textContent = `${nome} (ID: ${idParaExcluir})`;
                    modalExcluir.style.display = 'block';
                } else {
                    console.error("Erro: data-id ou data-nome não encontrados no botão de exclusão. Verifique a renderização do PHP.");
                }
            }
        });
    });

    document.getElementById('fechar-modal-excluir').onclick = function() {
        modalExcluir.style.display = 'none';
        idParaExcluir = null;
    }

    document.getElementById('cancelar-exclusao').onclick = function() {
        modalExcluir.style.display = 'none';
        idParaExcluir = null;
    }
    
    confirmarExclusao.onclick = function() {
        if (idParaExcluir) {
            // Aponta para o arquivo que contém a lógica de exclusão no PHP
            window.location.href = `testes2.php?acao=excluir&id=${idParaExcluir}`; 
        }
    }

    // Fechar modal ao clicar fora
    window.onclick = function(event) {
        if (event.target == modalEditar) {
            modalEditar.style.display = 'none';
        }
        if (event.target == modalExcluir) {
            modalExcluir.style.display = 'none';
        }
    }
});