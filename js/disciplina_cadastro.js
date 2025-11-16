document.addEventListener('DOMContentLoaded', function() {
    // --- Variáveis do Modal ---
    const modal = document.getElementById('modal-editar-disciplina');
    const btnFechar = document.getElementById('fechar-modal');
    
    // --- Abrir e Fechar Modal ---
    btnFechar.onclick = function() {
        modal.style.display = "none";
    }
    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }

    // --- Ações de Edição (Abrir Modal e Preencher) ---
    document.querySelectorAll('.btn-editar').forEach(button => {
        button.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const nome = this.getAttribute('data-nome');
            const area = this.getAttribute('data-area');
            
            // Preenche o formulário do modal
            document.getElementById('id_disc_editar').value = id;
            document.getElementById('nome_disciplina_edit').value = nome;
            document.getElementById('area_disciplina_edit').value = area;
            
            // Exibe o modal
            modal.style.display = "block";
        });
    });

    // --- Ações de Exclusão (Confirmação e Redirecionamento) ---
    document.querySelectorAll('.btn-excluir').forEach(button => {
        button.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const nome = this.getAttribute('data-nome');
            
            if (confirm(`Tem certeza que deseja EXCLUIR a disciplina "${nome}" (ID: ${id})? Essa ação é irreversível!`)) {
                // Redireciona para a URL que contém a lógica de exclusão no PHP
                window.location.href = `?acao=excluir&id=${id}`;
            }
        });
    });
});