/*

 * Arquivo: custom-scripts.js

 * Contém toda a lógica de interação (DOM/Eventos) da página.

 */



// --- Variável global para armazenar a área da disciplina selecionada ---

let selectedDisciplineArea = '';



// --- Função para o clique no Card de Turma ---

function toggleCardClick(event, idTurma) {

    // Verifica se o clique foi em um campo de input (number) dentro do card

    if (event.target.tagName === 'INPUT' && event.target.type === 'number') {

        event.stopPropagation(); // Impede a propagação para não ativar o checkbox

        return;

    }



    if (!document.getElementById('id_disc_input').value) {

        alert('Por favor, selecione uma Disciplina no Passo 1 primeiro.');

        return;

    }



    // Se o clique não foi em um input number, simula o clique no checkbox

    document.getElementById('incluir_' + idTurma).click();

}





// Função para mostrar/esconder campos de carga horária e preencher valores

function toggleCargaFields(checkbox) {

    const turmaCard = checkbox.closest('.turma-card-relacionamento');

    const cargaFields = turmaCard.querySelector('.carga-fields');

    const numberInputs = cargaFields.querySelectorAll('input[type="number"]');



    if (checkbox.checked) {

        // ATIVAR: Torna visível, remove 'disabled' e exige valores

        cargaFields.style.display = 'block';

        numberInputs.forEach(input => {

            input.required = true;

            input.disabled = false;

            if (input.value === '0') {

                input.value = '1';

            }

        });

    } else {

        // DESATIVAR: Oculta, adiciona 'disabled' para IGNORAR a validação

        cargaFields.style.display = 'none';

        numberInputs.forEach(input => {

            input.value = '0';

            input.required = false;

            input.disabled = true;

        });

    }

}



// Função para alterar o estilo visual do card

function toggleCardStyle(checkbox) {

    const card = checkbox.closest('.turma-card-relacionamento');

    const icon = card.querySelector('.toggle-icon');



    if (checkbox.checked) {

        // Ativo

        card.style.borderColor = '#48ab87';

        card.style.backgroundColor = 'rgba(72, 171, 135, 0.1)';

        icon.classList.remove('fa-toggle-off');

        icon.classList.add('fa-toggle-on');

        icon.style.color = '#48ab87';

    } else {

        // Inativo

        card.style.borderColor = '#ddd';

        card.style.backgroundColor = 'transparent';

        icon.classList.remove('fa-toggle-on');

        icon.classList.add('fa-toggle-off');

        icon.style.color = '#ccc';

    }

}



// Função principal de navegação entre seções

function mostrarSecao(idSecao) {

    document.querySelectorAll('main > div.content-section').forEach(secao => {

        secao.style.display = 'none';

    });



    const secao = document.getElementById(idSecao);

    if (secao) {

        secao.style.display = 'block';

    }



    if (idSecao !== 'cadastrar-disciplina-section' && window.location.search.includes('msg=')) {

        // Esta linha é mantida para seu código, mas não será mais usada se AJAX funcionar.

        window.history.pushState({}, '', window.location.pathname + window.location.hash);

    }

}



// --- FUNÇÕES DE FILTRO DE PROFESSOR ---



// NOVO: Função chamada ao selecionar uma disciplina

function handleDisciplinaChange(area) {

    selectedDisciplineArea = area; // Armazena a nova área

   

    // 1. Resetar o professor padrão selecionado

    const profSelect = document.querySelector('#professor-select-wrapper .custom-select');

    const profInput = document.getElementById('professor_input');

    profSelect.textContent = "Selecione o Professor";

    profInput.value = '';



    // 2. Aplicar o filtro nos professores

    const filtroAreaSelect = document.getElementById('filtro-area');

    if (filtroAreaSelect) filtroAreaSelect.value = ''; // Limpa filtro manual

    const profSearchInput = document.getElementById('professor-search');

    if (profSearchInput) profSearchInput.value = ''; // Limpa busca

    filterProfessorOptions();



}



function filterProfessorOptions() {

    const searchInput = document.getElementById('professor-search');

    const filtroAreaSelect = document.getElementById('filtro-area');



    const searchText = searchInput ? searchInput.value.toLowerCase() : '';

    const filterArea = filtroAreaSelect ? filtroAreaSelect.value : '';

   

    const optionsList = document.querySelector('#professor-select-wrapper .options-list');

    if (!optionsList) return;

   

    const options = optionsList.querySelectorAll('.professor-option');



    options.forEach(option => {

        const name = option.getAttribute('data-name').toLowerCase();

        const areas = option.getAttribute('data-areas');

       

        let nameMatch = name.includes(searchText);

       

        // FILTRO 1: A área do professor DEVE conter a área da disciplina (Filtro Automático)

        let areaDisciplineMatch = !selectedDisciplineArea || areas.toLowerCase().includes(selectedDisciplineArea.toLowerCase());

       

        // FILTRO 2: Se o filtro manual por área estiver ativo, ele também deve ser correspondido

        let areaManualMatch = !filterArea || areas.toLowerCase().includes(filterArea.toLowerCase());



        if (nameMatch && areaDisciplineMatch && areaManualMatch) {

            option.style.display = 'block';

        } else {

            option.style.display = 'none';

        }

    });

}



// --- FUNÇÕES PARA O CUSTOM SELECT (Dropdown Estilizado) ---



function setupCustomSelects() {

    document.querySelectorAll('.custom-select-wrapper').forEach(wrapper => {

        const select = wrapper.querySelector('.custom-select');

        const optionsContainer = wrapper.querySelector('.options');

        const hiddenInput = wrapper.querySelector('input[type="hidden"]');

       

        if (!select || !optionsContainer || !hiddenInput) return;

       

        // Lógica de valor inicial (Se já houver um valor definido)

        if (hiddenInput.value !== '') {

            const initialOption = optionsContainer.querySelector(`.option[data-value="${hiddenInput.value}"]`);

            if (initialOption) {

                select.textContent = initialOption.textContent;

                initialOption.classList.add('selected');

                // Se for a disciplina, inicia o filtro do professor

                if (wrapper.querySelector('#id_disc_input')) {

                    selectedDisciplineArea = initialOption.getAttribute('data-area');

                }

            }

        }





        // Toggle o container de opções ao clicar no seletor

        select.addEventListener('click', (e) => {

            e.stopPropagation();

           

            // Fecha outros selects abertos

            document.querySelectorAll('.custom-select-wrapper').forEach(otherWrapper => {

                if (otherWrapper !== wrapper) {

                    const otherOptions = otherWrapper.querySelector('.options');

                    const otherSelect = otherWrapper.querySelector('.custom-select');

                    if (otherOptions) otherOptions.style.display = 'none';

                    if (otherSelect) otherSelect.classList.remove('open');

                }

            });



            optionsContainer.style.display = optionsContainer.style.display === 'block' ? 'none' : 'block';

            select.classList.toggle('open');

           

            // Ao abrir, foca na busca se for o select de professor

            if (wrapper.id === 'professor-select-wrapper') {

                // Garante que o filtro automático esteja ativo

                filterProfessorOptions();

               

                setTimeout(() => {

                    const searchInput = document.getElementById('professor-search');

                    if(searchInput) searchInput.focus();

                }, 10);

            }

        });



        // Evita fechar ao clicar em INPUT/SELECT dentro da área de busca

        optionsContainer.querySelectorAll('.options-search input, .options-search select').forEach(control => {

            control.addEventListener('click', (e) => {

                e.stopPropagation();

            });

        });





        // Seleciona uma opção

        optionsContainer.querySelectorAll('.option').forEach(option => {

            option.addEventListener('click', (e) => {

                const value = option.getAttribute('data-value');

                const text = option.textContent;



                // Apenas processa se for uma opção de valor real

                if (value) {

                    select.textContent = text;

                    hiddenInput.value = value;

                    optionsContainer.style.display = 'none';

                    select.classList.remove('open');

                   

                    optionsContainer.querySelectorAll('.option').forEach(o => o.classList.remove('selected'));

                    option.classList.add('selected');



                    // NOVO: Se for o select de disciplina, chama a função de filtro

                    if (wrapper.querySelector('#id_disc_input')) {

                        const area = option.getAttribute('data-area');

                        handleDisciplinaChange(area);

                    }

                }

            });

        });



        // Fecha quando clica fora

        document.addEventListener('click', () => {

            optionsContainer.style.display = 'none';

            select.classList.remove('open');

        });

    });

}



// ===================================================================

// LÓGICA AJAX PARA O FORMULÁRIO DE RELACIONAMENTO

// ===================================================================

document.addEventListener('DOMContentLoaded', function() {

   

    // Inicializa os selects personalizados

    setupCustomSelects();



    // O container de feedback é buscado no header da seção

    const form = document.getElementById('form-relacionamento-disciplina');

    const feedbackContainer = document.querySelector('#cadastrar-disciplina-section .main-header');



    // Função para exibir o feedback visualmente

    function displayFeedback(status, message) {

        let icon = '';

        let bgColor = '';

        let textColor = '#fff';



        switch (status) {

            case 'success':

                icon = '✅';

                bgColor = '#4CAF50'; // Verde

                break;

            case 'warning':

                icon = '⚠️';

                bgColor = '#FFC107'; // Amarelo

                textColor = '#333'; // Texto escuro para melhor contraste

                break;

            case 'info':

                icon = '⏳';

                bgColor = '#007bff'; // Azul (Processando)

                break;

            case 'error':

            default:

                icon = '❌';

                bgColor = '#F44336'; // Vermelho

                break;

        }



        if (feedbackContainer) {

             const messageDiv = document.createElement('div');

             messageDiv.className = `feedback-${status}`;

             messageDiv.style.cssText = `

                padding: 15px;

                margin-bottom: 20px;

                border-radius: 5px;

                color: ${textColor};

                font-weight: bold;

                background-color: ${bgColor};

            `;

            messageDiv.innerHTML = `${icon} ${message.replace(/\*\*/g, '<strong>')}`;

           

            // Remove mensagens anteriores e insere a nova no topo da seção

            document.querySelectorAll('.feedback-success, .feedback-warning, .feedback-error, .feedback-info').forEach(el => el.remove());

            feedbackContainer.parentNode.insertBefore(messageDiv, feedbackContainer.nextSibling);

        }

    }



    if (form) {

        form.addEventListener('submit', function(e) {

            e.preventDefault(); // CRÍTICO: Impede o envio tradicional



            // 1. Feedback de Carregamento

            displayFeedback('info', 'Processando dados, aguarde...');

           

            // Rola para o topo para garantir que a mensagem seja vista

            window.scrollTo({ top: 0, behavior: 'smooth' });



            const formData = new FormData(form);

           

            // Correção do Erro 404: URL definida explicitamente para a pasta 'includes'

            const ajaxUrl = 'includes/processar_relacionamento_disciplina.php';



            // 2. Requisição AJAX (Fetch API)

            fetch(ajaxUrl, {

                method: 'POST',

                body: formData

            })

            .then(response => {

                // Trata erros de HTTP (como o 404)

                if (!response.ok) {

                    throw new Error('Erro de Servidor (' + response.status + '): O arquivo de processamento não foi encontrado (404) ou houve erro de permissão.');

                }

                return response.json(); // Tenta ler a resposta JSON

            })

            .then(data => {

                // 3. Processa a resposta JSON do PHP

                displayFeedback(data.status, data.msg);

               

                // 🚀 NOVO: RECARREGA A PÁGINA SE FOR SUCESSO

                if (data.status === 'success') {

                    // Espera 1.5s para o usuário ler a mensagem de sucesso

                    setTimeout(() => {

                        window.location.reload();

                    }, 1500);

                }

            })

            .catch(error => {

                // 4. Trata falhas na comunicação (incluindo o persistente 404)

                displayFeedback('error', `Falha na Comunicação: ${error.message}. Por favor, verifique o console para detalhes.`);

                console.error('Erro no AJAX:', error);

            });

        });

    }



    // --- Seu código de navegação e inicialização deve ser mantido aqui ---

    // Define a seção inicial com base na URL (Seu código original)

    if (window.location.hash) {

        let secaoId = window.location.hash.substring(1);

        if (!secaoId.endsWith('-section') && secaoId !== 'dashboard-home') {

            secaoId += '-section';

        }

        mostrarSecao(secaoId);

    } else if (window.location.search.includes('msg=')) {

        // Mantido apenas para compatibilidade com redirecionamentos antigos

        mostrarSecao('cadastrar-disciplina-section');

    } else {

        mostrarSecao('cadastrar-disciplina-section');

    }



    // Garante que o estado inicial dos cards de turma seja processado

    document.querySelectorAll('.turma-card-relacionamento input[type="checkbox"]').forEach(checkbox => {

        toggleCargaFields(checkbox);

        toggleCardStyle(checkbox);

    });

   

    // Garante que o filtro de professor seja aplicado mesmo sem interação inicial

    filterProfessorOptions();



});

