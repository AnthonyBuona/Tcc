document.addEventListener('DOMContentLoaded', () => {
  // ========================
  // Controle de telas
  // ========================
  const telas = ['tela1', 'tela2', 'tela3', 'tela4', 'tela5', 'telaEsqueciSenha'];
  let historico = ['tela1'];

  function mostrarTela(idTela) {
    telas.forEach(tela => {
      document.getElementById(tela).style.display = (tela === idTela) ? 'block' : 'none';
    });
  }

  function mudarTela(novaTela) {
    mostrarTela(novaTela);
    historico.push(novaTela);
  }

  mostrarTela('tela1');

  // Navegação botões
  document.getElementById('temContaBtn').addEventListener('click', () => mudarTela('tela2'));
  document.getElementById('naoTemContaBtn').addEventListener('click', () => mudarTela('tela3'));
  document.getElementById('voltarBtn').addEventListener('click', e => { e.preventDefault(); mudarTela('tela3'); });
  document.getElementById('voltarParaLoginBtn').addEventListener('click', e => { e.preventDefault(); mudarTela('tela2'); });
  document.getElementById('esqueciSenhaLink').addEventListener('click', e => { e.preventDefault(); mudarTela('telaEsqueciSenha'); });

  document.querySelectorAll('.btn-voltar').forEach(btn => {
    btn.addEventListener('click', () => {
      if (historico.length > 1) {
        historico.pop();
        mostrarTela(historico[historico.length - 1]);
      }
    });
  });

  // ========================
  // Custom Select Tipo Usuário (Tela 3)
  // ========================
  const wrapperTipo = document.querySelector('#tela3 .custom-select-wrapper');
  const selectTipo = wrapperTipo.querySelector('.custom-select');
  const optionsTipo = wrapperTipo.querySelectorAll('.option');
  const inputCategoria = document.getElementById('categoria');

  selectTipo.addEventListener('click', () => {
    const container = selectTipo.nextElementSibling;
    container.style.display = container.style.display === 'block' ? 'none' : 'block';
  });

  optionsTipo.forEach(option => {
    option.addEventListener('click', () => {
      selectTipo.textContent = option.textContent;
      inputCategoria.value = option.dataset.value;
      selectTipo.nextElementSibling.style.display = 'none';
    });
  });

  document.getElementById('tipo-form').addEventListener('submit', e => {
    e.preventDefault();
    if (inputCategoria.value === 'aluno') {
      mudarTela('tela4');
    } else if (inputCategoria.value === 'professor') {
      mudarTela('tela5');
    } else {
      alert('Selecione uma categoria.');
    }
  });

  // ========================
  // Custom Select Aluno (Tela 4: série e curso)
  // ========================
  const customSelectsAluno = document.querySelectorAll('#tela4 .custom-select-wrapper');
  customSelectsAluno.forEach(wrapper => {
    const select = wrapper.querySelector('.custom-select');
    const optionsContainer = wrapper.querySelector('.options');
    const hiddenInput = wrapper.querySelector('input[type="hidden"]');

    select.addEventListener('click', (e) => {
      e.stopPropagation();
      optionsContainer.style.display = optionsContainer.style.display === 'block' ? 'none' : 'block';
    });

    optionsContainer.querySelectorAll('.option').forEach(option => {
      option.addEventListener('click', () => {
        select.textContent = option.textContent;
        hiddenInput.value = option.dataset.value;
        optionsContainer.style.display = 'none';
      });
    });
  });

// ========================
// Multi-select Professor (Tela 5: áreas)
// ========================
const wrapperProf = document.querySelectorAll('#tela5 .multi-select-wrapper');
wrapperProf.forEach(wrapper => {
  const select = wrapper.querySelector('.multi-select');
  const optionsContainer = wrapper.querySelector('.options');
  const hiddenInput = wrapper.querySelector('input[name="areas"]');

  if (!hiddenInput) {
    console.error("Input hidden 'areas' não encontrado dentro do wrapper:", wrapper);
    return; // evita quebrar o resto do código
  }

  const options = wrapper.querySelectorAll('.option');
  let selectedAreas = [];

  // Abrir/fechar dropdown
  select.addEventListener('click', (e) => {
    e.stopPropagation();
    optionsContainer.style.display = optionsContainer.style.display === 'block' ? 'none' : 'block';
  });

  // Seleção múltipla
  options.forEach(option => {
    option.addEventListener('click', (e) => {
      e.stopPropagation();
      const val = option.dataset.value;
      const idx = selectedAreas.findIndex(item => item.value === val);

      if (idx > -1) {
        selectedAreas.splice(idx, 1);
        option.classList.remove('selected');
      } else {
        selectedAreas.push({ value: val, text: option.textContent });
        option.classList.add('selected');
      }

      // Atualiza o input hidden
      hiddenInput.value = selectedAreas.map(a => a.value).join(',');

      // Atualiza o display do select
      if (selectedAreas.length === 0) {
        select.textContent = "Área(s) que você ensina...";
      } else {
        select.textContent = selectedAreas.map(a => a.text).join(', ');
      }
    });
  });

  // Fechar dropdown ao clicar fora
  document.addEventListener('click', (e) => {
    if (!wrapper.contains(e.target)) {
      optionsContainer.style.display = 'none';
    }
  });
});



});
