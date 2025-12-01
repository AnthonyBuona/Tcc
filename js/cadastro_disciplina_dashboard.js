/**
 * Arquivo: js/cadastro_disciplina_dashboard.js
 */

// --- 1. Carregamento da Seção via AJAX ---
function carregarCadastroDisciplina() {
    const container = document.getElementById('cadastrar-disciplina-section');
    if (!container) return;

    container.innerHTML = '<div style="text-align:center; padding:40px;"><i class="fas fa-spinner fa-spin fa-2x"></i><p>Carregando formulário...</p></div>';

    fetch('includes/view_cadastro_disciplina.php')
        .then(response => response.text())
        .then(html => {
            container.innerHTML = html;
            inicializarEventosCadastro(); // Reconecta os eventos JS
        })
        .catch(err => {
            console.error(err);
            container.innerHTML = '<p class="message-error">Erro ao carregar a seção de cadastro.</p>';
        });
}

// --- 2. Inicialização dos Eventos (Selects e Submit) ---
function inicializarEventosCadastro() {
    
    // Configura os Custom Selects (Disciplina e Professor)
    setupCustomSelect('wrapper-disciplina', function(option) {
        // Callback ao selecionar disciplina: Filtra os professores pela área
        const area = option.getAttribute('data-area');
        filtrarProfessoresPorAreaDisciplina(area);
    });

    setupCustomSelect('wrapper-professor');

    // Filtros de busca e área dentro do select de professor
    const buscaInput = document.getElementById('busca-prof');
    const filtroArea = document.getElementById('filtro-area-prof');
    
    if(buscaInput) {
        buscaInput.addEventListener('input', filtrarListaProfessores);
    }
    if(filtroArea) {
        filtroArea.addEventListener('change', filtrarListaProfessores);
    }

    // Submit do Formulário
    const form = document.getElementById('form-relacionamento-disciplina');
    if(form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const btn = form.querySelector('button[type="submit"]');
            const originalText = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Salvando...';

            const formData = new FormData(form);

            fetch('includes/processar_relacionamento_disciplina.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                const feedback = document.getElementById('feedback-cadastro-disciplina');
                if (data.status === 'success') {
                    feedback.innerHTML = `<div class="message-success">${data.msg}</div>`;
                    setTimeout(() => {
                        feedback.innerHTML = '';
                        // Opcional: Resetar form ou recarregar
                        // carregarCadastroDisciplina(); 
                    }, 3000);
                } else {
                    feedback.innerHTML = `<div class="message-error">${data.msg}</div>`;
                }
            })
            .catch(err => {
                console.error(err);
                alert('Erro de comunicação ao salvar.');
            })
            .finally(() => {
                btn.disabled = false;
                btn.innerHTML = originalText;
            });
        });
    }
}

// --- 3. Lógica de Custom Select Genérica ---
function setupCustomSelect(wrapperId, onSelectCallback) {
    const wrapper = document.getElementById(wrapperId);
    if(!wrapper) return;

    const select = wrapper.querySelector('.custom-select');
    const optionsContainer = wrapper.querySelector('.options');
    const hiddenInput = wrapper.querySelector('input[type="hidden"]');
    const options = wrapper.querySelectorAll('.option');

    // Toggle Dropdown
    select.addEventListener('click', (e) => {
        e.stopPropagation();
        // Fecha outros
        document.querySelectorAll('.options').forEach(opt => {
            if(opt !== optionsContainer) opt.style.display = 'none';
        });
        optionsContainer.style.display = (optionsContainer.style.display === 'block') ? 'none' : 'block';
    });

    // Seleção de Opção
    options.forEach(option => {
        option.addEventListener('click', (e) => {
            e.stopPropagation();
            const val = option.getAttribute('data-value');
            
            // Texto principal (sem o small)
            const clone = option.cloneNode(true);
            const small = clone.querySelector('small');
            if(small) small.remove();
            const text = clone.textContent.trim();

            select.textContent = text;
            hiddenInput.value = val;
            
            // Remove selecionado anterior
            options.forEach(o => o.classList.remove('selected'));
            option.classList.add('selected');
            
            optionsContainer.style.display = 'none';

            if (onSelectCallback) onSelectCallback(option);
        });
    });

    // Fechar ao clicar fora
    document.addEventListener('click', (e) => {
        if(!wrapper.contains(e.target)) optionsContainer.style.display = 'none';
    });
}

// --- 4. Funções Globais de Interação (Filtros e Cards) ---

window.filtrarProfessoresPorAreaDisciplina = function(areaDisciplina) {
    // Reseta professor selecionado
    const wrapper = document.getElementById('wrapper-professor');
    wrapper.querySelector('.custom-select').textContent = 'Selecione o Professor';
    wrapper.querySelector('input[type="hidden"]').value = '';
    
    // Armazena a área da disciplina em um atributo do wrapper para uso no filtro combinado
    wrapper.setAttribute('data-filter-area-disc', areaDisciplina || '');
    
    filtrarListaProfessores();
};

window.filtrarListaProfessores = function() {
    const busca = document.getElementById('busca-prof').value.toLowerCase();
    const filtroAreaManual = document.getElementById('filtro-area-prof').value.toLowerCase();
    
    const wrapper = document.getElementById('wrapper-professor');
    const areaDisciplina = (wrapper.getAttribute('data-filter-area-disc') || '').toLowerCase();

    const options = wrapper.querySelectorAll('.option.professor-option');

    options.forEach(opt => {
        const nome = opt.getAttribute('data-name').toLowerCase();
        const areasProf = opt.getAttribute('data-areas').toLowerCase();

        // Lógica: 
        // 1. Nome deve bater com a busca
        // 2. Área manual (se selecionada) deve estar nas áreas do prof
        // 3. Área da disciplina (se houver) deve estar nas áreas do prof
        
        const matchNome = !busca || nome.includes(busca);
        const matchAreaManual = !filtroAreaManual || areasProf.includes(filtroAreaManual);
        const matchAreaDisc = !areaDisciplina || areasProf.includes(areaDisciplina);

        if (matchNome && matchAreaManual && matchAreaDisc) {
            opt.style.display = 'block';
        } else {
            opt.style.display = 'none';
        }
    });
};

// Toggle Card de Turma (Função global chamada no onclick do HTML)
window.toggleCardTurma = function(idTurma) {
    const card = document.getElementById('card_' + idTurma);
    const checkbox = document.getElementById('chk_' + idTurma);
    const inputsDiv = document.getElementById('inputs_' + idTurma);
    const icon = document.getElementById('icon_' + idTurma);
    
    // Inverte estado
    checkbox.checked = !checkbox.checked;
    
    const isChecked = checkbox.checked;
    const inputs = inputsDiv.querySelectorAll('input');

    if (isChecked) {
        // Ativo
        card.style.borderColor = '#48ab87';
        card.style.backgroundColor = 'rgba(72, 171, 135, 0.1)';
        icon.className = 'fas fa-toggle-on toggle-icon';
        icon.style.color = '#48ab87';
        
        inputsDiv.style.display = 'block';
        inputs.forEach(inp => {
            inp.disabled = false;
            if(inp.value == '0') inp.value = ''; // Limpa o zero
            inp.required = true;
        });
    } else {
        // Inativo
        card.style.borderColor = '#ddd';
        card.style.backgroundColor = 'transparent';
        icon.className = 'fas fa-toggle-off toggle-icon';
        icon.style.color = '#ccc';
        
        inputsDiv.style.display = 'none';
        inputs.forEach(inp => {
            inp.disabled = true;
            inp.required = false;
        });
    }
};