document.addEventListener("DOMContentLoaded", () => {
  const form = document.getElementById("cadastro-aluno-form");
  const mensagem = document.getElementById("mensagem");
  const btnCadastrar = document.getElementById("btnCadastrarAluno");

  btnCadastrar.addEventListener("click", (e) => {
    e.preventDefault(); // impede recarregar a página

    const senha = form.querySelector("input[name='senha']").value;
    const confirmar = form.querySelector("input[name='confirmar_senha']").value;

    // Validação de senha
    if (senha !== confirmar) {
      mensagem.style.display = "block";
      mensagem.style.color = "red";
      mensagem.textContent = "As senhas não coincidem!";
      return;
    }

    if (senha.length < 6) {
      mensagem.style.display = "block";
      mensagem.style.color = "red";
      mensagem.textContent = "A senha deve ter pelo menos 6 caracteres.";
      return;
    }

    const formData = new FormData(form);

    fetch(form.action, { method: "POST", body: formData })
      .then(res => res.json())
      .then(data => {
        mensagem.style.display = "block";
        mensagem.style.color = data.status === "sucesso" ? "green" : "red";

        if (data.status === "sucesso") {
          // Mensagem com link
          mensagem.innerHTML = `${data.mensagem} <a href="${data.redirect}" style="color:blue; text-decoration:underline;">Clique aqui para entrar</a>`;
          form.reset();
        } else {
          // Mensagem de erro normal
          mensagem.textContent = data.mensagem;
        }
      })
      .catch(err => {
        mensagem.style.display = "block";
        mensagem.style.color = "red";
        mensagem.textContent = "Erro ao cadastrar: " + err;
      });
  });
});
