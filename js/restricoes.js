// Arquivo: js/restricoes.js

// 1. Carrega a View Principal
function carregarRestricoes(idProf = null) {
    const container = document.getElementById('restricoes-professor-section');
    if(!container) return;

    let url = 'includes/view_restricoes.php';
    if (idProf) {
        url += '?id_prof=' + idProf;
    }

    container.innerHTML = '<div style="text-align:center; padding:40px;"><i class="fas fa-spinner fa-spin fa-2x"></i><p>Carregando...</p></div>';

    fetch(url)
        .then(res => res.text())
        .then(html => {
            container.innerHTML = html;
            attachRestricoesEvents(); // Reconecta os eventos após carregar HTML novo
        })
        .catch(err => {
            console.error(err);
            container.innerHTML = '<p class="error">Erro ao carregar seção.</p>';
        });
}

// 2. Conecta os eventos (Submit, Cliques, Change)
function attachRestricoesEvents() {
    // Form de busca (Carregar Professor)
    const formBusca = document.getElementById('form-busca-prof-restricao');
    if (formBusca) {
        formBusca.addEventListener('submit', function(e) {
            e.preventDefault();
            const id = document.getElementById('select_prof_restricao').value;
            if (id) {
                carregarRestricoes(id);
            } else {
                alert('Selecione um professor.');
            }
        });
    }

    // Forms de Salvamento (Perfil e Grade)
    document.querySelectorAll('.form-ajax-restricao').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const btn = this.querySelector('button[type="submit"]');
            const originalText = btn.textContent;
            btn.disabled = true;
            btn.textContent = 'Salvando...';

            const formData = new FormData(this);

            fetch('includes/processar_restricoes.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    showToast(data.message, false); // Usa sua função global de Toast
                } else {
                    showToast(data.message, true);
                }
            })
            .catch(err => {
                showToast('Erro de comunicação.', true);
                console.error(err);
            })
            .finally(() => {
                btn.disabled = false;
                btn.textContent = originalText;
            });
        });
    });
}

// 3. Utilitários Globais (Expostos no Window para onclick no HTML)
window.toggleHorarioSaida = function() {
    const select = document.getElementById('trabalha_outro');
    const div = document.getElementById('div-horario-saida');
    if (select && div) {
        div.style.display = (select.value === '1') ? 'block' : 'none';
        if (select.value === '0') {
            const input = div.querySelector('input');
            if(input) input.value = '';
        }
    }
};

window.toggleRestricao = function(td) {
    const checkbox = td.querySelector('input[type="checkbox"]');
    if (checkbox) {
        checkbox.checked = !checkbox.checked;
        if (checkbox.checked) {
            td.style.backgroundColor = '#ffcccc';
            td.classList.add('restrito');
        } else {
            td.style.backgroundColor = 'white';
            td.classList.remove('restrito');
        }
    }
};