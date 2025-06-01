// Variável para controlar se já configuramos os eventos
let horariosConfigurados = false;

// Função para configurar tudo uma única vez
function configurarHorarios() {
  if (horariosConfigurados) return;

  console.log("Configurando horários...");

  // Função para gerar os horários de abertura e fechamento
  function gerarHorarios() {
    const horarios = [];
    for (let i = 0; i < 24; i++) {
      for (let j = 0; j < 60; j += 5) {
        const hora = (i < 10 ? "0" : "") + i;
        const minuto = j < 10 ? "0" + j : j;
        horarios.push(`${hora}:${minuto}h`);
      }
    }
    return horarios;
  }

  // Função para adicionar os horários ao dropdown de cada célula
  function adicionarHorarios() {
    const horarios = gerarHorarios();

    document.querySelectorAll(".horario-input").forEach((input) => {
      const dropdown = input
        .closest(".column-item")
        .querySelector(".dropdown-options");
      if (dropdown) {
        dropdown.innerHTML = ""; // Limpa os dropdowns antes de adicionar novos itens

        horarios.forEach((horario) => {
          const option = document.createElement("div");
          option.textContent = horario;
          option.classList.add("dropdown-option");
          option.onclick = function () {
            input.value = horario; // Atribui o valor selecionado ao input
            input.dataset.selected = horario; // Armazena o valor selecionado
            input.placeholder = "00:00h"; // Reseta o placeholder
            hideDropdown(input);
          };
          dropdown.appendChild(option);
        });
      }
    });
  }

  // Função para mostrar o dropdown
  function showDropdown(event) {
    const inputElement = event.target;
    if (inputElement && inputElement.closest) {
      // Fecha todos os dropdowns
      document.querySelectorAll(".dropdown-options").forEach((dropdown) => {
        dropdown.classList.remove("show");
      });

      // Abre o dropdown do item selecionado
      const dropdown = inputElement
        .closest(".column-item")
        .querySelector(".dropdown-options");
      if (dropdown) {
        dropdown.classList.add("show");
      }
    }
  }

  // Função para esconder o dropdown
  function hideDropdown(inputElement) {
    const dropdown = inputElement
      .closest(".column-item")
      .querySelector(".dropdown-options");
    if (dropdown) {
      dropdown.classList.remove("show");
    }
  }

  // Função para filtrar as opções de horário com base na digitação
  function filterDropdown(event) {
    const inputElement = event.target;
    const filter = inputElement.value.toLowerCase();
    const dropdown = inputElement
      .closest(".column-item")
      .querySelector(".dropdown-options");
    if (dropdown) {
      const options = dropdown.querySelectorAll(".dropdown-option");

      options.forEach((option) => {
        option.style.display = option.textContent.toLowerCase().includes(filter)
          ? "block"
          : "none";
      });

      // Mantém o dropdown aberto enquanto filtra
      dropdown.classList.add("show");
    }
  }

  // Função para obter os dias que foram desmarcados
  function getDiasDesmarcados() {
    // Obter dados salvos anteriormente
    const dadosSalvos = localStorage.getItem("horariosFuncionamento");
    if (!dadosSalvos) return [];

    const dadosAnteriores = JSON.parse(dadosSalvos);
    const diasAnteriores = dadosAnteriores.dias || [];

    // Obter dias atualmente selecionados
    const diasAtuais = [];
    document
      .querySelectorAll('input[name="dias[]"]:checked')
      .forEach((checkbox) => {
        diasAtuais.push(checkbox.value);
      });

    // Encontrar dias que estavam selecionados antes mas não estão mais
    return diasAnteriores.filter((dia) => !diasAtuais.includes(dia));
  }

  // Função para coletar os dados do formulário
  function coletarDadosFormulario() {
    const dados = {
      dias: [],
      horarios: {},
    };

    // Coletar dias selecionados
    document
      .querySelectorAll('input[name="dias[]"]:checked')
      .forEach((checkbox) => {
        dados.dias.push(checkbox.value);
      });

    // Coletar horários para todos os dias (independente de estarem selecionados)
    const diasSemana = [
      "segunda",
      "terca",
      "quarta",
      "quinta",
      "sexta",
      "sabado",
      "domingo",
    ];

    diasSemana.forEach((dia) => {
      const aberturaInput = document.querySelector(
        `input[name="abertura_${dia}"]`
      );
      const fechamentoInput = document.querySelector(
        `input[name="fechamento_${dia}"]`
      );

      if (aberturaInput && fechamentoInput) {
        dados.horarios[dia] = {
          abertura: aberturaInput.value || "",
          fechamento: fechamentoInput.value || "",
        };
      }
    });

    return dados;
  }

  // Função para validar os dados do formulário
  function validarFormulario(dados) {
    // Verificar se há dias desmarcados
    const diasDesmarcados = getDiasDesmarcados();

    // Se não há dias selecionados, mas há dias desmarcados, permitir o salvamento
    // (isso significa que o usuário está removendo dias)
    if (dados.dias.length === 0 && diasDesmarcados.length > 0) {
      return true;
    }

    // Se não há dias selecionados e não há dias desmarcados, exigir pelo menos um dia
    if (dados.dias.length === 0) {
      mostrarFeedback("Selecione pelo menos um dia de funcionamento.", "error");
      return false;
    }

    // Verificar se os dias selecionados têm horários preenchidos
    let horariosValidos = true;

    for (let i = 0; i < dados.dias.length; i++) {
      const dia = dados.dias[i];
      const horarios = dados.horarios[dia];

      // Verificar se o dia tem horários definidos
      if (!horarios || !horarios.abertura || !horarios.fechamento) {
        horariosValidos = false;
        mostrarFeedback(
          `Preencha os horários de abertura e fechamento para ${getDiaNome(
            dia
          )}.`,
          "error"
        );
        return false;
      }

      // Verificar se o horário de fechamento é posterior ao de abertura
      try {
        const abertura = horarios.abertura.replace("h", "").split(":");
        const fechamento = horarios.fechamento.replace("h", "").split(":");

        const aberturaHora = parseInt(abertura[0]);
        const aberturaMinuto = parseInt(abertura[1]);
        const fechamentoHora = parseInt(fechamento[0]);
        const fechamentoMinuto = parseInt(fechamento[1]);

        if (
          fechamentoHora < aberturaHora ||
          (fechamentoHora === aberturaHora &&
            fechamentoMinuto <= aberturaMinuto)
        ) {
          horariosValidos = false;
          mostrarFeedback(
            `O horário de fechamento deve ser posterior ao de abertura para ${getDiaNome(
              dia
            )}.`,
            "error"
          );
          return false;
        }
      } catch (error) {
        horariosValidos = false;
        mostrarFeedback(
          `Formato de horário inválido para ${getDiaNome(dia)}.`,
          "error"
        );
        return false;
      }
    }

    return horariosValidos;
  }

  // Função auxiliar para obter o nome do dia formatado
  function getDiaNome(dia) {
    const nomes = {
      segunda: "Segunda-feira",
      terca: "Terça-feira",
      quarta: "Quarta-feira",
      quinta: "Quinta-feira",
      sexta: "Sexta-feira",
      sabado: "Sábado",
      domingo: "Domingo",
    };

    return nomes[dia] || dia;
  }

  const notyf = new Notyf({
    duration: 4000,
    position: {
      x: "right",
      y: "top",
    },
  });

  // Função para mostrar feedback ao usuário
  function mostrarFeedback(mensagem, tipo) {
    if (tipo === "success") {
      notyf.success(mensagem);
    } else if (tipo === "error") {
      notyf.error(mensagem);
    } else {
      notyf.open({
        type: "info",
        message: mensagem,
      });
    }
  }

  // Função para salvar os dados (exemplo com localStorage)
  function salvarDados(dados) {
    try {
      // Verificar se há dias desmarcados
      const diasDesmarcados = getDiasDesmarcados();

      // Se todos os dias foram desmarcados, limpar completamente os dados
      if (dados.dias.length === 0 && diasDesmarcados.length > 0) {
        localStorage.setItem(
          "horariosFuncionamento",
          JSON.stringify({
            dias: [],
            horarios: {},
          })
        );

        mostrarFeedback(
          "Todos os dias de funcionamento foram removidos.",
          "success"
        );
        return true;
      }

      // Obter dados salvos anteriormente para manter os horários dos dias não alterados
      const dadosSalvos = localStorage.getItem("horariosFuncionamento");
      let dadosAtualizados = dados;

      if (dadosSalvos) {
        const dadosAnteriores = JSON.parse(dadosSalvos);

        // Manter os horários dos dias que não foram alterados
        Object.keys(dadosAnteriores.horarios || {}).forEach((dia) => {
          if (!dados.dias.includes(dia) && !diasDesmarcados.includes(dia)) {
            // Se o dia não está nos dias selecionados atuais e não foi desmarcado,
            // manter os horários anteriores
            dadosAtualizados.horarios[dia] = dadosAnteriores.horarios[dia];
          }
        });
      }

      // Salvar no localStorage
      localStorage.setItem(
        "horariosFuncionamento",
        JSON.stringify(dadosAtualizados)
      );

      // Mostrar mensagem de sucesso
      if (diasDesmarcados.length > 0) {
        mostrarFeedback(
          `Horários salvos com sucesso! ${diasDesmarcados.length} dia(s) removido(s).`,
          "success"
        );
      } else {
        mostrarFeedback("Horários salvos com sucesso!", "success");
      }

      return true;
    } catch (error) {
      mostrarFeedback("Erro ao salvar os horários. Tente novamente.", "error");
      return false;
    }
  }

  // Função para carregar dados salvos anteriormente
  function carregarDadosSalvos() {
    try {
      const dadosSalvos = localStorage.getItem("horariosFuncionamento");

      if (dadosSalvos) {
        const dados = JSON.parse(dadosSalvos);

        // Marcar os dias selecionados
        dados.dias.forEach((dia) => {
          const checkbox = document.querySelector(`input[value="${dia}"]`);
          if (checkbox) {
            checkbox.checked = true;
          }
        });

        // Preencher os horários
        Object.keys(dados.horarios).forEach((dia) => {
          const horarios = dados.horarios[dia];

          const aberturaInput = document.querySelector(
            `input[name="abertura_${dia}"]`
          );
          const fechamentoInput = document.querySelector(
            `input[name="fechamento_${dia}"]`
          );

          if (aberturaInput && horarios.abertura) {
            aberturaInput.value = horarios.abertura;
            aberturaInput.dataset.selected = horarios.abertura;
          }

          if (fechamentoInput && horarios.fechamento) {
            fechamentoInput.value = horarios.fechamento;
            fechamentoInput.dataset.selected = horarios.fechamento;
          }
        });
      }
    } catch (error) {
      mostrarFeedback("Erro ao carregar dados salvos.", "error");
    }
  }

  // Configura eventos dos inputs de horário
  document.querySelectorAll(".horario-input").forEach((input) => {
    // Remove eventos antigos
    input.onfocus = null;
    input.onkeyup = null;
    input.onblur = null;

    // Adiciona novos eventos
    input.addEventListener("focus", function () {
      // Mostra como placeholder o horário anterior selecionado
      input.placeholder = input.dataset.selected || "00:00h";

      // Limpa o campo para permitir nova pesquisa
      input.value = "";

      // Exibe todas as opções novamente
      const options = input
        .closest(".column-item")
        .querySelector(".dropdown-options")
        ?.querySelectorAll(".dropdown-option");

      if (options) {
        options.forEach((option) => {
          option.style.display = "block";
        });
      }

      // Mostra o dropdown
      const dropdown = input
        .closest(".column-item")
        .querySelector(".dropdown-options");
      if (dropdown) {
        dropdown.classList.add("show");
      }
    });

    input.addEventListener("keyup", function (event) {
      filterDropdown(event);
    });

    // Evento de clique fora do campo (blur)
    input.addEventListener("blur", function () {
      setTimeout(() => {
        if (!input.value && input.dataset.selected) {
          input.value = input.dataset.selected;
          input.placeholder = "00:00h"; // Volta ao padrão
        }
      }, 150); // Aguarda a seleção antes de resetar
    });
  });

  // Fecha os dropdowns se clicar fora de um input
  window.addEventListener("click", function (e) {
    if (!e.target.closest(".dropdown-container")) {
      document.querySelectorAll(".dropdown-options").forEach((dropdown) => {
        dropdown.classList.remove("show");
      });
    }
  });

  // Script para trocar o ícone de seta para "search" quando focar e restaurar ao sair
  document.querySelectorAll(".dropdown-container").forEach((container) => {
    const input = container.querySelector("input");
    const icon = container.querySelector("#dropdown-icon");
    const dropdown = container.querySelector(".dropdown-options");

    if (input && icon && dropdown) {
      // Remove eventos antigos
      input.onfocus = null;
      input.onblur = null;

      // Adiciona novos eventos
      input.addEventListener("focus", () => {
        dropdown.classList.add("show");
        icon.textContent = "search";
        icon.classList.add("search-icon");
      });

      input.addEventListener("blur", () => {
        setTimeout(() => {
          dropdown.classList.remove("show");
          icon.textContent = "keyboard_arrow_down";
          icon.classList.remove("search-icon");
        }, 150);
      });
    }
  });

  // Configura evento ao botão de salvar
  const saveButton = document.getElementById("save-button");
  if (saveButton) {
    // Remove evento antigo
    saveButton.onclick = null;

    // Adiciona novo evento
    saveButton.addEventListener("click", function () {
      const dados = coletarDadosFormulario();
      const diasDesmarcados = getDiasDesmarcados();

      // Verificar se há dias desmarcados antes de validar
      if (validarFormulario(dados)) {
        salvarDados(dados);
      }
    });
  }

  // Carregar dados salvos anteriormente (se existirem)
  carregarDadosSalvos();

  // Chama a função para adicionar os horários aos dropdowns
  adicionarHorarios();

  horariosConfigurados = true;
}

// Observador para a parte de horários
const horariosObserver = new MutationObserver((mutations) => {
  // Verifica se os elementos principais foram adicionados
  const elementosAdicionados = mutations.some((mutation) => {
    return Array.from(mutation.addedNodes).some((node) => {
      // Verifica se o nó é um elemento (Node.ELEMENT_NODE)
      if (node.nodeType !== Node.ELEMENT_NODE) return false;

      return (
        node.classList?.contains("horario-input") ||
        node.id === "save-button" ||
        (node.querySelector &&
          (node.querySelector('input[name^="abertura_"]') ||
            node.querySelector('input[name^="fechamento_"]')))
      );
    });
  });

  if (elementosAdicionados) {
    horariosConfigurados = false;
    configurarHorarios();
  }
});

// Configuração inicial quando o DOM estiver pronto
if (document.readyState === "loading") {
  document.addEventListener("DOMContentLoaded", configurarHorarios);
} else {
  configurarHorarios();
}

// Observa apenas adições/remoções de nós filhos
horariosObserver.observe(document.body, {
  childList: true,
  subtree: true,
});

// Resetar ao mudar de página
window.addEventListener("pagehide", () => {
  horariosConfigurados = false;
});
