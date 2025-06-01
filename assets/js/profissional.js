// Variável para controlar se já configuramos os eventos
let profissionalConfigurado = false;

const notyf = new Notyf({
  duration: 3000,
  position: {
    x: "right",
    y: "top",
  },
});

// Função para configurar tudo uma única vez
function configurarProfissional() {
  if (profissionalConfigurado) return;

  console.log("Configurando profissional...");

  // ELEMENTOS DO DOM
  const elements = {
    openOffcanvas: document.getElementById("profissional-open-offcanvas"),
    closeOffcanvas: document.getElementById("profissional-close-offcanvas"),
    offcanvas: document.getElementById("profissional-offcanvas"),
    overlay: document.getElementById("profissional-overlay"),
    navCadastro: document.getElementById("profissional-nav-cadastro"),
    navExpediente: document.getElementById("profissional-nav-expediente"),
    cadastroForm: document.getElementById(
      "profissional-cadastro-form-container"
    ),
    expedienteForm: document.getElementById(
      "profissional-expediente-form-container"
    ),
    cancelButton: document.getElementById("profissional-cancel-button"),
    saveButton: document.getElementById("profissional-save-all-button"),
    profissionalForm: document.getElementById("profissional-form"),
    horarioForm: document.getElementById("profissional-horario-form"),
    celular: document.getElementById("profissional-celular"),
    nome: document.getElementById("profissional-nome"),
    profissao: document.getElementById("profissional-profissao"),
  };

  // ARMAZENAMENTO LOCAL
  const storage = {
    save: (data) =>
      localStorage.setItem("profissionalData", JSON.stringify(data)),
    load: () => {
      const data = localStorage.getItem("profissionalData");
      return data ? JSON.parse(data) : null;
    },
    clear: () => localStorage.removeItem("profissionalData"),
  };

  // FUNÇÕES DO OFFCANVAS
  function openOffcanvas() {
    elements.offcanvas.classList.add("active");
    elements.overlay.classList.add("active");
    document.body.style.overflow = "hidden";
    showCadastroForm();
    loadSavedData(); // Carrega os dados salvos
  }

  function closeOffcanvas() {
    elements.offcanvas.classList.remove("active");
    elements.overlay.classList.remove("active");
    document.body.style.overflow = "";
  }

  // NAVEGAÇÃO ENTRE FORMULÁRIOS
  function showCadastroForm() {
    elements.cadastroForm.classList.add("active");
    elements.expedienteForm.classList.remove("active");
    elements.navCadastro.classList.add("active");
    elements.navExpediente.classList.remove("active");
    document.querySelector(".profissional-offcanvas-header h2").textContent =
      "Cadastro de Profissional";
  }

  function showExpedienteForm() {
    elements.cadastroForm.classList.remove("active");
    elements.expedienteForm.classList.add("active");
    elements.navCadastro.classList.remove("active");
    elements.navExpediente.classList.add("active");
    document.querySelector(".profissional-offcanvas-header h2").textContent =
      "Expediente";
  }

  // CARREGAR DADOS SALVOS
  function loadSavedData() {
    const savedData = storage.load();
    if (!savedData) return;

    // Preencher formulário de cadastro
    if (savedData.profissional) {
      elements.nome.value = savedData.profissional.nome || "";
      elements.celular.value = savedData.profissional.celular || "";
      elements.profissao.value = savedData.profissional.profissao || "";
    }

    // Preencher formulário de expediente
    if (savedData.expediente) {
      // Marcar dias selecionados
      savedData.expediente.dias.forEach((dia) => {
        const checkbox = document.querySelector(
          `input[name="profissional-dias[]"][value="${dia}"]`
        );
        if (checkbox) checkbox.checked = true;
      });

      // Preencher horários
      if (savedData.expediente.horarios) {
        Object.entries(savedData.expediente.horarios).forEach(
          ([dia, horarios]) => {
            Object.entries(horarios).forEach(([tipo, valor]) => {
              const input = document.querySelector(
                `input[name="profissional-${tipo}_${dia}"]`
              );
              if (input) {
                input.value = valor;
                input.dataset.selected = valor; // Salva o valor selecionado
              }
            });
          }
        );
      }
    }
  }

  // VALIDAÇÃO DE FORMULÁRIOS
  function validateCadastro() {
    if (!elements.nome.value.trim()) {
      notyf.error("Erro: Nome do profissional é obrigatório");
      return false;
    }
    if (!elements.profissao.value.trim()) {
      notyf.error("Erro: O campo profissão é obrigatório");
      return false;
    }
    return true;
  }

  function validateExpediente() {
    const diasSelecionados = document.querySelectorAll(
      'input[name="profissional-dias[]"]:checked'
    );

    if (diasSelecionados.length === 0) {
      notyf.error("Erro: Selecione pelo menos um dia de atendimento");
      return false;
    }

    // Verificar se todos os dias selecionados têm todos os horários preenchidos
    for (const checkbox of diasSelecionados) {
      const dia = checkbox.value;

      // Obter todos os campos de horário para este dia
      const abertura = document.querySelector(
        `input[name="profissional-abertura_${dia}"]`
      ).value;
      const fechamento = document.querySelector(
        `input[name="profissional-fechamento_${dia}"]`
      ).value;
      const inicioIntervalo = document.querySelector(
        `input[name="profissional-inicio_intervalo_${dia}"]`
      ).value;
      const fimIntervalo = document.querySelector(
        `input[name="profissional-fim_intervalo_${dia}"]`
      ).value;

      // Validar campos obrigatórios
      if (!abertura || !fechamento || !inicioIntervalo || !fimIntervalo) {
        notyf.error(`Erro: Preencha todos os horários para ${getDiaNome(dia)}`);
        return false;
      }
    }

    return true;
  }

  function getDiaNome(dia) {
    const dias = {
      segunda: "Segunda-feira",
      terca: "Terça-feira",
      quarta: "Quarta-feira",
      quinta: "Quinta-feira",
      sexta: "Sexta-feira",
      sabado: "Sábado",
      domingo: "Domingo",
    };
    return dias[dia] || dia;
  }

  // DROPDOWNS DE HORÁRIOS
  function generateTimeOptions() {
    const horarios = [];
    for (let hora = 0; hora < 24; hora++) {
      for (let minuto = 0; minuto < 60; minuto += 5) {
        horarios.push(
          `${String(hora).padStart(2, "0")}:${String(minuto).padStart(2, "0")}h`
        );
      }
    }
    return horarios;
  }

  function setupDropdowns() {
    const horarios = generateTimeOptions();

    document
      .querySelectorAll(".profissional-dropdown-options")
      .forEach((dropdown) => {
        dropdown.innerHTML = "";
        horarios.forEach((horario) => {
          const option = document.createElement("div");
          option.className = "profissional-dropdown-option";
          option.textContent = horario;
          option.onclick = function () {
            const input = dropdown
              .closest(".profissional-dropdown-container")
              .querySelector("input");
            input.value = horario;
            input.dataset.selected = horario; // Armazena o valor selecionado
            dropdown.classList.remove("show");
          };
          dropdown.appendChild(option);
        });
      });

    document
      .querySelectorAll(".profissional-horario-input")
      .forEach((input) => {
        // Remove eventos antigos
        input.onfocus = null;
        input.onkeyup = null;
        input.onblur = null;

        // Adiciona novos eventos
        input.addEventListener("focus", function () {
          // Mostra o valor salvo como placeholder
          this.placeholder = this.dataset.selected || "00:00h";
          this.value = "";

          const dropdown = this.closest(
            ".profissional-dropdown-container"
          ).querySelector(".profissional-dropdown-options");

          // Fecha todos os outros dropdowns
          document
            .querySelectorAll(".profissional-dropdown-options.show")
            .forEach((d) => {
              d.classList.remove("show");
            });

          // Mostra todas as opções
          dropdown
            .querySelectorAll(".profissional-dropdown-option")
            .forEach((opt) => {
              opt.style.display = "block";
            });

          dropdown.classList.add("show");
        });

        input.addEventListener("keyup", function (e) {
          const filter = e.target.value.toLowerCase();
          const dropdown = e.target
            .closest(".profissional-dropdown-container")
            .querySelector(".profissional-dropdown-options");
          dropdown
            .querySelectorAll(".profissional-dropdown-option")
            .forEach((opt) => {
              opt.style.display = opt.textContent.toLowerCase().includes(filter)
                ? "block"
                : "none";
            });
        });

        input.addEventListener("blur", function () {
          setTimeout(() => {
            if (!this.value && this.dataset.selected) {
              this.value = this.dataset.selected;
              this.placeholder = "00:00h"; // Volta ao padrão
            }
          }, 150);
        });
      });

    // Fechar dropdown ao clicar fora
    document.addEventListener("click", function (e) {
      if (!e.target.closest(".profissional-dropdown-container")) {
        document
          .querySelectorAll(".profissional-dropdown-options.show")
          .forEach((d) => {
            d.classList.remove("show");
          });
      }
    });
  }

  // SALVAMENTO DOS DADOS
  function saveData() {
    if (!validateCadastro() || !validateExpediente()) return;

    const formData = {
      profissional: {
        nome: elements.nome.value,
        celular: elements.celular.value,
        profissao: elements.profissao.value,
      },
      expediente: {
        dias: Array.from(
          document.querySelectorAll('input[name="profissional-dias[]"]:checked')
        ).map((el) => el.value),
        horarios: {},
      },
    };

    // Coletar todos os horários (abertura, fechamento, inicio_intervalo e fim_intervalo)
    document
      .querySelectorAll(".profissional-horario-input")
      .forEach((input) => {
        const nameParts = input.name.split("_");
        if (nameParts.length === 3) {
          // formato: profissional-tipo_dia
          const tipoCompleto = nameParts[0] + "_" + nameParts[1]; // junta "profissional-inicio" ou "profissional-fim"
          const tipo = tipoCompleto.replace("profissional-", "");
          const dia = nameParts[2];

          if (!formData.expediente.horarios[dia]) {
            formData.expediente.horarios[dia] = {};
          }
          formData.expediente.horarios[dia][tipo] = input.value;
        } else if (nameParts.length === 2) {
          // formato: profissional-tipo_dia (para abertura/fechamento)
          const tipo = nameParts[0].replace("profissional-", "");
          const dia = nameParts[1];

          if (!formData.expediente.horarios[dia]) {
            formData.expediente.horarios[dia] = {};
          }
          formData.expediente.horarios[dia][tipo] = input.value;
        }
      });

    // Salvar no localStorage
    storage.save(formData);
    notyf.success("Dados salvos com sucesso!");
    setTimeout(closeOffcanvas, 1000);
  }

  // MÁSCARA DE CELULAR
  function applyPhoneMask() {
    if (!elements.celular) return;

    // Remove evento antigo
    elements.celular.oninput = null;

    // Adiciona novo evento
    elements.celular.addEventListener("input", function (e) {
      let value = e.target.value.replace(/\D/g, "");
      if (value.length > 11) value = value.slice(0, 11);

      if (value.length > 2) {
        value = `(${value.slice(0, 2)}) ${value.slice(2)}`;
      }
      if (value.length > 10) {
        value = `${value.slice(0, 10)}-${value.slice(10)}`;
      }

      e.target.value = value;
    });
  }

  // Configura eventos dos elementos
  if (elements.openOffcanvas) {
    elements.openOffcanvas.addEventListener("click", openOffcanvas);
  }
  if (elements.closeOffcanvas) {
    elements.closeOffcanvas.addEventListener("click", closeOffcanvas);
  }
  if (elements.overlay) {
    elements.overlay.addEventListener("click", closeOffcanvas);
  }
  if (elements.cancelButton) {
    elements.cancelButton.addEventListener("click", closeOffcanvas);
  }
  if (elements.navCadastro) {
    elements.navCadastro.addEventListener("click", showCadastroForm);
  }
  if (elements.navExpediente) {
    elements.navExpediente.addEventListener("click", showExpedienteForm);
  }
  if (elements.saveButton) {
    elements.saveButton.addEventListener("click", saveData);
  }

  // INICIALIZAÇÃO
  setupDropdowns();
  applyPhoneMask();

  profissionalConfigurado = true;
}

// Observador para a parte de profissional
const profissionalObserver = new MutationObserver((mutations) => {
  // Verifica se os elementos principais foram adicionados
  const elementosAdicionados = mutations.some((mutation) => {
    return Array.from(mutation.addedNodes).some((node) => {
      // Verifica se o nó é um elemento (Node.ELEMENT_NODE)
      if (node.nodeType !== Node.ELEMENT_NODE) return false;

      return (
        node.id === "profissional-offcanvas" ||
        node.id === "profissional-form" ||
        node.id === "profissional-horario-form" ||
        (node.querySelector &&
          (node.querySelector(".profissional-horario-input") ||
            node.querySelector("#profissional-save-all-button")))
      );
    });
  });

  if (elementosAdicionados) {
    profissionalConfigurado = false;
    configurarProfissional();
  }
});

// Configuração inicial quando o DOM estiver pronto
if (document.readyState === "loading") {
  document.addEventListener("DOMContentLoaded", configurarProfissional);
} else {
  configurarProfissional();
}

// Observa apenas adições/remoções de nós filhos
profissionalObserver.observe(document.body, {
  childList: true,
  subtree: true,
});

// Resetar ao mudar de página
window.addEventListener("pagehide", () => {
  profissionalConfigurado = false;
});
