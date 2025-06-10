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
