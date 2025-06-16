// Configuração inicial
const notyf = new Notyf({
  duration: 3000,
  position: { x: "right", y: "top" },
});

// Função principal
function configurarProfissional() {
  // Elementos DOM
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

  // Funções de controle
  function openOffcanvas(profissionalData = null) {
    elements.offcanvas.classList.add("active");
    elements.overlay.classList.add("active");
    document.body.style.overflow = "hidden";
    showCadastroForm();

    // Limpa todos os campos do expediente
    document
      .querySelectorAll('input[name="profissional-dias[]"]')
      .forEach((cb) => {
        cb.checked = false;
      });
    document
      .querySelectorAll(".profissional-horario-input")
      .forEach((input) => {
        input.value = "";
      });

    if (profissionalData) {
      fillProfissionalData(profissionalData);
    } else {
      // Limpa também o formulário de profissional
      elements.profissionalForm.reset();
      // Remove qualquer ID existente
      document
        .querySelectorAll('input[name="idProfissional"]')
        .forEach((el) => el.remove());
    }
  }

  function closeOffcanvas() {
    elements.offcanvas.classList.remove("active");
    elements.overlay.classList.remove("active");
    document.body.style.overflow = "";
  }

  function showCadastroForm() {
    elements.cadastroForm.classList.add("active");
    elements.expedienteForm.classList.remove("active");
    elements.navCadastro.classList.add("active");
    elements.navExpediente.classList.remove("active");
    document.querySelector(".profissional-offcanvas-header h2").textContent =
      "Cadastro de Profissional";
    elements.saveButton.textContent = "Salvar Profissional";
  }

  function showExpedienteForm() {
    elements.cadastroForm.classList.remove("active");
    elements.expedienteForm.classList.add("active");
    elements.navCadastro.classList.remove("active");
    elements.navExpediente.classList.add("active");
    document.querySelector(".profissional-offcanvas-header h2").textContent =
      "Expediente";
    elements.saveButton.textContent = "Salvar Expediente";
  }

  // Funções de dados
  async function saveData() {
    const isExpedienteTab = elements.navExpediente.classList.contains("active");

    try {
      elements.saveButton.disabled = true;
      elements.saveButton.innerHTML =
        '<span class="spinner-border spinner-border-sm" role="status"></span> Salvando...';

      if (isExpedienteTab) {
        await saveExpediente();
      } else {
        const profissionalId = await saveProfissional();
        updateHiddenIdField(profissionalId);
        showExpedienteForm();
      }
    } catch (error) {
      handleError(error);
      showCadastroForm();
    } finally {
      elements.saveButton.disabled = false;
      elements.saveButton.textContent = isExpedienteTab
        ? "Salvar Expediente"
        : "Salvar Profissional";
    }
  }

  async function saveProfissional() {
    const formData = new FormData(elements.profissionalForm);
    formData.append("rota", "salvarProfissional");

    const response = await fetch("../public/ProfissionalRoutes.php", {
      method: "POST",
      body: formData,
    });

    if (!response.ok) {
      const errorText = await response.text();
      throw new Error(errorText);
    }

    const data = await response.json();
    if (!data.id) throw new Error("ID do profissional não retornado");

    notyf.success("Profissional salvo com sucesso!");
    return data.id;
  }

  async function saveExpediente() {
    const idProfissional = document.getElementById(
      "profissional-id-hidden"
    )?.value;
    if (!idProfissional) throw new Error("ID do profissional não encontrado");

    const formData = new FormData();
    formData.append("rota", "salvarExpediente");
    formData.append("idProfissional", idProfissional);

    const diasCheckboxes = document.querySelectorAll(
      'input[name="profissional-dias[]"]:checked'
    );
    if (diasCheckboxes.length === 0)
      throw new Error("Selecione pelo menos um dia");

    diasCheckboxes.forEach((checkbox) => {
      const dia = checkbox.value;
      formData.append("dias[]", dia);

      const campos = {
        abertura: document.querySelector(
          `input[name="profissional-abertura_${dia}"]`
        ),
        fechamento: document.querySelector(
          `input[name="profissional-fechamento_${dia}"]`
        ),
        inicioIntervalo: document.querySelector(
          `input[name="profissional-inicio_intervalo_${dia}"]`
        ),
        fimIntervalo: document.querySelector(
          `input[name="profissional-fim_intervalo_${dia}"]`
        ),
      };

      if (!campos.abertura.value || !campos.fechamento.value) {
        throw new Error(`Preencha todos os horários para ${dia}`);
      }

      formData.append(`abertura_${dia}`, campos.abertura.value);
      formData.append(`fechamento_${dia}`, campos.fechamento.value);
      if (campos.inicioIntervalo.value)
        formData.append(`inicioIntervalo_${dia}`, campos.inicioIntervalo.value);
      if (campos.fimIntervalo.value)
        formData.append(`fimIntervalo_${dia}`, campos.fimIntervalo.value);
    });

    const response = await fetch("../public/ProfissionalRoutes.php", {
      method: "POST",
      body: formData,
    });

    if (!response.ok) {
      const errorText = await response.text();
      throw new Error(errorText);
    }

    notyf.success("Expediente salvo com sucesso!");
    closeOffcanvas();
    window.location.reload();
  }

  // Funções auxiliares
  function fillProfissionalData(data) {
    elements.nome.value = data.nome || "";
    elements.celular.value = data.celular || "";
    elements.profissao.value = data.especialidade || "";
    updateHiddenIdField(data.idProfissional);
  }

  function updateHiddenIdField(id) {
    let hiddenField = document.getElementById("profissional-id-hidden");
    if (!hiddenField) {
      hiddenField = document.createElement("input");
      hiddenField.type = "hidden";
      hiddenField.id = "profissional-id-hidden";
      hiddenField.name = "idProfissional";
      elements.horarioForm.appendChild(hiddenField);
    }
    hiddenField.value = id;
  }

  function resetForms() {
    // Limpa formulário de profissional
    elements.profissionalForm.reset();

    // Limpa formulário de expediente
    document
      .querySelectorAll('input[name^="profissional-"][type="text"]')
      .forEach((input) => {
        input.value = "";
      });
    document
      .querySelectorAll('input[name="profissional-dias[]"]')
      .forEach((checkbox) => {
        checkbox.checked = false;
      });

    // Remove qualquer campo de ID existente
    document
      .querySelectorAll('input[name="idProfissional"]')
      .forEach((el) => el.remove());
  }

  function handleError(error) {
    notyf.error(error.message);
  }

  // Event Listeners
  if (elements.openOffcanvas) {
    elements.openOffcanvas.addEventListener("click", () => {
      // Força a limpeza completa
      resetForms();
      openOffcanvas();
    });
  }
  if (elements.closeOffcanvas)
    elements.closeOffcanvas.addEventListener("click", closeOffcanvas);
  if (elements.overlay)
    elements.overlay.addEventListener("click", closeOffcanvas);
  if (elements.cancelButton)
    elements.cancelButton.addEventListener("click", closeOffcanvas);
  if (elements.navCadastro)
    elements.navCadastro.addEventListener("click", showCadastroForm);
  if (elements.navExpediente)
    elements.navExpediente.addEventListener("click", showExpedienteForm);
  if (elements.saveButton)
    elements.saveButton.addEventListener("click", (e) => {
      e.preventDefault();
      saveData();
    });

  // Eventos para edição/exclusão
  document.addEventListener("click", function (e) {
    if (e.target.classList.contains("btn-editar")) {
      e.preventDefault();
      const row = e.target.closest("tr");
      const profissionalData = {
        idProfissional: e.target.getAttribute("data-id"),
        nome: e.target.getAttribute("data-nome"),
        celular: e.target.getAttribute("data-celular"),
        especialidade: e.target.getAttribute("data-especialidade"),
      };
      openOffcanvas(profissionalData);
      loadExpediente(profissionalData.idProfissional);
    }

    if (e.target.classList.contains("btn-excluir")) {
      e.preventDefault();
      const id = e.target.getAttribute("data-id");
      const nome = e.target
        .closest("tr")
        .querySelector("td:nth-child(2)").textContent;
      if (
        confirm(`Tem certeza que deseja excluir ${nome} e todos seus horários?`)
      ) {
        excluirProfissional(id);
      }
    }
  });

  // Funções secundárias
  async function loadExpediente(idProfissional) {
    try {
      const response = await fetch(
        `../controller/ExpedienteController.php?action=getByProfissional&id=${idProfissional}`
      );

      if (!response.ok) {
        throw new Error("Falha ao carregar expediente");
      }

      const result = await response.json();

      if (!result.success) {
        throw new Error(result.error || "Erro ao carregar expediente");
      }

      const expedientes = result.data || [];

      document
        .querySelectorAll('input[name="profissional-dias[]"]')
        .forEach((cb) => (cb.checked = false));
      document
        .querySelectorAll(".profissional-horario-input")
        .forEach((input) => (input.value = ""));

      expedientes.forEach((exp) => {
        const dia = exp.diaSemana;
        const checkbox = document.querySelector(
          `input[name="profissional-dias[]"][value="${dia}"]`
        );
        if (checkbox) checkbox.checked = true;

        const setField = (name, value) => {
          const field = document.querySelector(`input[name="${name}"]`);
          if (field && value) field.value = value;
        };

        setField(`profissional-abertura_${dia}`, exp.inicioExpediente);
        setField(`profissional-fechamento_${dia}`, exp.fimExpediente);
        setField(`profissional-inicio_intervalo_${dia}`, exp.inicioIntervalo);
        setField(`profissional-fim_intervalo_${dia}`, exp.fimIntervalo);
      });
    } catch (error) {
      notyf.error("Não foi possível carregar os horários salvos");
    }
  }

  async function excluirProfissional(id) {
    try {
      const response = await fetch("../public/ProfissionalRoutes.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: `rota=excluirProfissional&idProfissional=${id}`,
      });

      if (!response.ok) {
        const errorText = await response.text();
        throw new Error(errorText);
      }

      const result = await response.json();
      if (result.success) {
        notyf.success(result.message);
        document
          .querySelector(`.btn-excluir[data-id="${id}"]`)
          .closest("tr")
          .remove();
      } else {
        throw new Error(result.error);
      }
    } catch (error) {
      notyf.error(error.message);
    }
  }

  // Inicialização
  applyPhoneMask();
  configurarDropdownsHorarios();
}

// Funções de utilidade
function applyPhoneMask() {
  const celular = document.getElementById("profissional-celular");
  if (!celular) return;

  celular.addEventListener("input", function (e) {
    let value = e.target.value.replace(/\D/g, "");
    if (value.length > 11) value = value.slice(0, 11);

    if (value.length > 0) {
      value = value.replace(/^(\d{2})(\d)/g, "($1) $2");
      if (value.length > 10) {
        value = value.replace(/(\d{5})(\d)/, "$1-$2");
      }
    }

    e.target.value = value;
  });
}

function configurarDropdownsHorarios() {
  function gerarOpcoesHorario() {
    const horas = [];
    for (let h = 0; h < 24; h++) {
      for (let m = 0; m < 60; m += 5) {
        horas.push(
          `${h.toString().padStart(2, "0")}:${m.toString().padStart(2, "0")}h`
        );
      }
    }
    return horas;
  }

  const opcoesHorario = gerarOpcoesHorario();

  document
    .querySelectorAll(".profissional-dropdown-container")
    .forEach((container) => {
      const input = container.querySelector(".profissional-horario-input");
      const dropdown = container.querySelector(
        ".profissional-horario-dropdown"
      );

      dropdown.innerHTML = "";
      opcoesHorario.forEach((hora) => {
        const option = document.createElement("div");
        option.className = "profissional-dropdown-option";
        option.textContent = hora;
        option.addEventListener("click", () => {
          input.value = hora;
          dropdown.classList.remove("show");
        });
        dropdown.appendChild(option);
      });

      input.addEventListener("focus", () => dropdown.classList.add("show"));
      input.addEventListener("blur", () =>
        setTimeout(() => dropdown.classList.remove("show"), 200)
      );
      input.addEventListener("input", () => {
        const termo = input.value.toLowerCase();
        dropdown
          .querySelectorAll(".profissional-dropdown-option")
          .forEach((opt) => {
            opt.style.display = opt.textContent.toLowerCase().includes(termo)
              ? "block"
              : "none";
          });
      });
    });
}

// Inicialização condicional
document.addEventListener("DOMContentLoaded", function () {
  if (document.getElementById("profissional-form")) {
    configurarProfissional();
  }
});
