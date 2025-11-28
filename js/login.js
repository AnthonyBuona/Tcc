document.getElementById('login-form').addEventListener('submit', function(e) {
    e.preventDefault();

    const form = e.target;
    const formData = new FormData(form);
    const mensagemDiv = document.getElementById('mensagemLogin');
    mensagemDiv.style.display = 'none';

    // *** A CORREÇÃO ESTÁ AQUI: O caminho inclui 'includes/' ***
    fetch('includes/processar_login.php', { 
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        mensagemDiv.style.display = 'block';
        
        if (data.status === 'sucesso') {
            // 1. LOGIN BEM-SUCEDIDO
            mensagemDiv.style.color = 'green';
            mensagemDiv.textContent = 'Login bem-sucedido!';
            
            // 2. LÓGICA DE NOTIFICAÇÃO DE APROVAÇÃO
            if (data.notificacoes > 0) {
                // Se houver notificação (lida=0), exibe um alerta antes de redirecionar
                // Isso garante que ele saiba que foi aprovado na primeira tentativa.
                alert(`Parabéns! Seu cadastro foi APROVADO! Você tem ${data.notificacoes} nova(s) notificação(ões).`);
            }

            // 3. Redireciona para o painel do usuário
            window.location.href = data.redirecionar; 

        } else if (data.status === 'aguardando') {
            // PENDENTE DE APROVAÇÃO
            mensagemDiv.style.color = 'orange';
            mensagemDiv.textContent = data.mensagem;
            
        } else {
            // ERRO (CPF/Senha incorretos, etc.)
            mensagemDiv.style.color = 'red';
            mensagemDiv.textContent = data.mensagem;
        }
    })
    .catch(error => {
        console.error('Erro na requisição:', error);
        mensagemDiv.style.display = 'block';
        mensagemDiv.style.color = 'red';
        mensagemDiv.textContent = 'Erro de comunicação com o servidor.';
    });
});