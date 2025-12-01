/**
 * Arquivo: js/disciplinas.js
 */

// 1. Carregar a Seção
function carregarDisciplinas() {
    const container = document.getElementById('visualizar-disciplina-section');
    if (!container) return;

    container.innerHTML = '<div style="text-align:center; padding:40px;"><i class="fas fa-spinner fa-spin fa-2x"></i><p>Carregando disciplinas...</p></div>';

    fetch('includes/view_disciplinas.php')
        .then(response => response.text())
        .then(html => {
            container.innerHTML = html;
            attachDisciplinaEvents(); // Conecta o submit do modal
        })
        .catch(err => {
            console.error(err);
            container.innerHTML = '<p class="message-error">Erro ao carregar lista de disciplinas.</p>';
        });
}

// 2. Filtro de Busca na Tabela
window.filtrarDisciplinas = function() {
    const termo = document.getElementById('busca-disciplina').value.toLowerCase();
    const linhas = document.querySelectorAll('#tabela-disciplinas tbody tr');

    linhas.forEach(linha => {
        const nome = linha.querySelector('.nome-disc').textContent.toLowerCase();
        const area = linha.querySelector('.area-disc').textContent.toLowerCase();
        
        if (nome.includes(termo) || area.includes(termo)) {
            linha.style.display = '';
        } else {
            linha.style.display = 'none';
        }
    });
};

// 3. Abrir Modal de Edição
window.abrirModalEdicaoDisciplina = function(id, nome, area) {
    const modal = document.getElementById('modal-edicao-disciplina');
    document.getElementById('edit-id-disc').value = id;
    document.getElementById('edit-nome-disc').value = nome;
    document.getElementById('edit-area-disc').value = area;
    
    modal.style.display = 'flex';
};

// 4. Excluir Disciplina
window.excluirDisciplina = function(id, nome) {
    if (confirm(`Tem certeza que deseja excluir a disciplina "${nome}"?`)) {
        fetch('includes/processar_disciplina.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({ acao: 'excluir', id: id }).toString()
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                // Remove a linha visualmente
                const linha = document.querySelector(`tr[data-id="${id}"]`);
                if (linha) linha.remove();
                alert(data.message);
            } else {
                alert(data.message); // Exibe erro (ex: FK constraint)
            }
        })
        .catch(err => alert('Erro de comunicação.'));
    }
};

// 5. Conectar Evento de Submit do Modal
function attachDisciplinaEvents() {
    const form = document.getElementById('form-editar-disciplina');
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const btn = form.querySelector('.btn-salvar');
            const originalText = btn.textContent;
            btn.disabled = true;
            btn.textContent = 'Salvando...';

            fetch('includes/processar_disciplina.php', {
                method: 'POST',
                body: new FormData(form)
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    document.getElementById('modal-edicao-disciplina').style.display = 'none';
                    // Recarrega a lista para atualizar os dados na tabela
                    carregarDisciplinas(); 
                } else {
                    alert(data.message);
                    btn.disabled = false;
                    btn.textContent = originalText;
                }
            })
            .catch(err => {
                console.error(err);
                alert('Erro ao salvar.');
                btn.disabled = false;
                btn.textContent = originalText;
            });
        });
    }
}