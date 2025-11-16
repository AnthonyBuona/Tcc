document.addEventListener("DOMContentLoaded", () => {
  // -------------------- ALUNO --------------------
  const cadastroAlunoForm = document.getElementById("cadastro-aluno-form");
  const btnCadastrarAluno = document.getElementById("btnCadastrarAluno");
  const cpfAlunoInput = document.getElementById("cpf-aluno");

  // Criar mensagem acima do botão
  const mensagemAluno = document.createElement("p");
  mensagemAluno.style.textAlign = "center";
  btnCadastrarAluno.parentNode.insertBefore(mensagemAluno, btnCadastrarAluno);

  // Verificação de CPF em tempo real
  cpfAlunoInput.addEventListener("blur", () => {
    const cpf = cpfAlunoInput.value.trim();
    if (cpf !== "") {
      fetch("includes/check_cpf.php?cpf=" + cpf)
        .then(res => res.json())
        .then(data => {
          mensagemAluno.style.display = "block";
          mensagemAluno.style.color = data.existe ? "green" : "red";
          mensagemAluno.innerHTML = data.mensagem;
        })
        .catch(() => {
          mensagemAluno.style.display = "block";
          mensagemAluno.style.color = "red";
          mensagemAluno.textContent = "Erro ao verificar CPF.";
        });
    }
  });

  // Cadastro do aluno via AJAX
  btnCadastrarAluno.addEventListener("click", () => {
    const formData = new FormData(cadastroAlunoForm);

    fetch("includes/cadastro.php", {
      method: "POST",
      body: formData
    })
      .then(res => res.json())
      .then(data => {
        mensagemAluno.style.display = "block";
        if (data.status === "sucesso") {
          mensagemAluno.style.color = "green";
          mensagemAluno.textContent = data.mensagem;
          // Redirecionar para login após 1s
          setTimeout(() => window.location.href = data.redirect, 1000);
        } else {
          mensagemAluno.style.color = "red";
          mensagemAluno.textContent = data.mensagem;
        }
      })
      .catch(() => {
        mensagemAluno.style.display = "block";
        mensagemAluno.style.color = "red";
        mensagemAluno.textContent = "Erro na requisição.";
      });
  });

  // -------------------- PROFESSOR --------------------
  const cadastroProfForm = document.getElementById("cadastro-professor-form");
  const btnCadastrarProfessor = document.getElementById("btnCadastrarProfessor");
  const cpfProfInput = document.getElementById("cpf-professor");

  const mensagemProf = document.createElement("p");
  mensagemProf.style.textAlign = "center";
  btnCadastrarProfessor.parentNode.insertBefore(mensagemProf, btnCadastrarProfessor);

  // Verificação de CPF em tempo real
  cpfProfInput.addEventListener("blur", () => {
    const cpf = cpfProfInput.value.trim();
    if (cpf !== "") {
      fetch("includes/check_cpf.php?cpf=" + cpf)
        .then(res => res.json())
        .then(data => {
          mensagemProf.style.display = "block";
          mensagemProf.style.color = data.existe ? "green" : "red";
          mensagemProf.innerHTML = data.mensagem;
        })
        .catch(() => {
          mensagemProf.style.display = "block";
          mensagemProf.style.color = "red";
          mensagemProf.textContent = "Erro ao verificar CPF.";
        });
    }
  });

  // Cadastro do professor via AJAX
  btnCadastrarProfessor.addEventListener("click", () => {
    const formData = new FormData(cadastroProfForm);

    fetch("includes/cadastro.php", {
      method: "POST",
      body: formData
    })
      .then(res => res.json())
      .then(data => {
        mensagemProf.style.display = "block";
        if (data.status === "sucesso") {
          mensagemProf.style.color = "green";
          mensagemProf.textContent = data.mensagem;
          // Redirecionar para login após 1s
          setTimeout(() => window.location.href = data.redirect, 1000);
        } else {
          mensagemProf.style.color = "red";
          mensagemProf.textContent = data.mensagem;
        }
      })
      .catch(() => {
        mensagemProf.style.display = "block";
        mensagemProf.style.color = "red";
        mensagemProf.textContent = "Erro na requisição.";
      });
  });

  // Nenhuma função utilitária duplicada de modal/multi-select encontrada aqui.
  // Funções de cadastro e validação são específicas deste contexto e estão corretas.
});
