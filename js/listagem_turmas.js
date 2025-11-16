// Filtra a tabela de turmas por curso/série
function filtrarTurmas() {
  const cursoFiltro = document.getElementById("filterCurso").value.toLowerCase();
  const serieFiltro = document.getElementById("filterSerie").value.toLowerCase();
  const linhas = document.querySelectorAll("#turmasTable tbody tr");

  linhas.forEach(linha => {
    const nomeTurma = linha.dataset.nome.toLowerCase();
    const mostrar =
      (!cursoFiltro || nomeTurma.includes(cursoFiltro)) &&
      (!serieFiltro || nomeTurma.startsWith(serieFiltro));
    linha.style.display = mostrar ? "" : "none";
  });
}

// Ao clicar em uma turma → buscar alunos dessa turma
document.querySelectorAll(".linha-turma").forEach(linha => {
  linha.addEventListener("click", () => {
    const idTurma = linha.dataset.id;
    const nomeTurma = linha.dataset.nome;

    fetch(`buscar_alunos_turma.php?id_turma=${idTurma}`)
      .then(res => res.json())
      .then(data => {
        const tbody = document.querySelector("#alunosDaTurmaTable tbody");
        tbody.innerHTML = "";

        if (data.length === 0) {
          tbody.innerHTML = "<tr><td colspan='4'>Nenhum aluno encontrado.</td></tr>";
        } else {
          data.forEach(aluno => {
            const tr = document.createElement("tr");
            tr.innerHTML = `
              <td>${aluno.id_aluno}</td>
              <td>${aluno.nome}</td>
              <td>${aluno.cpf}</td>
              <td>${aluno.status_aprovacao}</td>
            `;
            tbody.appendChild(tr);
          });
        }

        document.getElementById("alunosTurma").style.display = "block";
        document.getElementById("nomeTurmaTitulo").textContent = nomeTurma;
      });
  });
});
