function carregarAtribuicaoAulas() {
    const container = document.getElementById('atribuicao-section');
    container.innerHTML = '<div style="text-align:center;padding:50px;"><i class="fas fa-spinner fa-spin fa-2x"></i><p>Carregando grade...</p></div>';
    
    fetch('includes/view_grade.php')
        .then(response => response.text())
        .then(html => {
            container.innerHTML = html;
            attachFormListeners(); // Reconecta os eventos dos formulários
        })
        .catch(err => {
            container.innerHTML = '<p style="color:red; text-align:center;">Erro ao carregar grade: ' + err + '</p>';
        });
}

// Conecta o evento de submit em todos os formulários carregados via AJAX
function attachFormListeners() {
    const forms = document.querySelectorAll('.form-alocacao-ajax');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const btn = form.querySelector('.btn-salvar');
            const originalText = btn.textContent;
            btn.disabled = true;
            btn.textContent = 'Salvando...';
            
            const formData = new FormData(form);
            
            fetch('includes/processar_grade.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToast(data.message, false); // Sucesso (Verde)
                    carregarAtribuicaoAulas(); // Recarrega para atualizar bloqueios/contadores
                } else {
                    showToast(data.message, true); // Erro (Vermelho)
                    btn.disabled = false;
                    btn.textContent = originalText;
                }
            })
            .catch(err => {
                showToast('Erro de comunicação ao salvar.', true);
                console.error(err);
                btn.disabled = false;
                btn.textContent = originalText;
            });
        });
    });
}

// Reutiliza sua função de Toast existente (dashboard_pendente.js ou funcoes_dashboard.js)
// Se não tiver, use esta simples:
if (typeof showToast !== 'function') {
    window.showToast = function(message, isError) {
        let toast = document.getElementById('feedback-toast');
        if (!toast) {
            toast = document.createElement('div');
            toast.id = 'feedback-toast';
            document.body.appendChild(toast);
        }
        toast.textContent = message;
        toast.style.display = 'block';
        toast.style.background = isError ? '#e74c3c' : '#27ae60';
        // ... (resto do estilo que você já tem) ...
        setTimeout(() => { toast.style.display = 'none'; }, 3000);
    };
}