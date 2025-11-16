document.addEventListener("DOMContentLoaded", () => {
  const form = document.getElementById("cadastro-professor-form");
  const mensagem = document.getElementById("mensagemProfessor");
  const btnCadastrar = document.getElementById("btnCadastrarProfessor");

  btnCadastrar.addEventListener("click", () => {
    // Pega valor do multi-select
    const hiddenAreas = form.querySelector("input[name='areas']").value;

    // Validação mínima visual
    if (!hiddenAreas || hiddenAreas.trim() === "") {
      mensagem.style.display = "block";
      mensagem.style.color = "red";
      mensagem.textContent = "Selecione ao menos uma área.";
      return;
    }

    // Envio do formulário via fetch
    const formData = new FormData(form);
    fetch(form.action, { method: "POST", body: formData })
      .then(res => res.json())
      .then(data => {
        mensagem.style.display = "block";
        mensagem.style.color = data.status === "sucesso" ? "green" : "red";

        if (data.status === "sucesso") {
          // Mensagem com link
          mensagem.innerHTML = `${data.mensagem} <a href="login.php" style="color:blue; text-decoration:underline;">Clique aqui para entrar</a>`;

          form.reset();

          // Reset visual do multi-select
          const multiSelect = form.querySelector(".multi-select");
          multiSelect.textContent = "Área(s) que você ensina...";
          form.querySelectorAll(".option").forEach(opt => opt.classList.remove("selected"));
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
